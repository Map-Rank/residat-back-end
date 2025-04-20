import pandas as pd
import numpy as np
import tensorflow as tf
from tensorflow.keras.models import load_model
import joblib
from sklearn.preprocessing import StandardScaler
import json
from io import StringIO

def load_and_preprocess_json_data(weather_json, hydro_json, geo_json):
    """Load and preprocess data from JSON for prediction"""
    # Convert JSON to DataFrames
    weather = pd.DataFrame(weather_json)
    hydro = pd.DataFrame(hydro_json)
    geo = pd.DataFrame(geo_json)
    
    # Convert date columns to datetime
    weather['date'] = pd.to_datetime(weather['date'])
    hydro['measurement_date'] = pd.to_datetime(hydro['measurement_date'])
    
    # Standardize column names and merge
    hydro = hydro.rename(columns={'measurement_date': 'date'})
    merged = pd.merge(weather, hydro, on=['date'], how='inner')
    merged = pd.merge(merged, geo, on='location', how='left')
    
    # Sort by location and date
    merged = merged.sort_values(['location', 'date'])
    
    # Extract metadata
    metadata = merged[['date', 'location']].copy()
    
    # Preprocess data (same steps as during training)
    merged['reservoir_level'] = merged.groupby('location')['reservoir_level'].ffill()
    merged['precipitation'] = merged['precipitation'].fillna(0)
    merged['temp_avg'] = (merged['temp_max'] + merged['temp_min']) / 2
    merged = merged.dropna(subset=['reservoir_level', 'river_flow', 'groundwater_level'])
    
    # Create features
    merged['day_of_year'] = merged['date'].dt.dayofyear
    merged['month'] = merged['date'].dt.month
    
    for window in [7, 30, 90]:
        merged[f'reservoir_{window}d_mean'] = merged.groupby('location')['reservoir_level'].transform(
            lambda x: x.rolling(window).mean())
        merged[f'precip_{window}d_sum'] = merged.groupby('location')['precipitation'].transform(
            lambda x: x.rolling(window).sum())
        merged[f'river_flow_{window}d_mean'] = merged.groupby('location')['river_flow'].transform(
            lambda x: x.rolling(window).mean())
        merged[f'groundwater_{window}d_mean'] = merged.groupby('location')['groundwater_level'].transform(
            lambda x: x.rolling(window).mean())
    
    # Other features
    merged['reservoir_7d_change'] = merged.groupby('location')['reservoir_level'].pct_change(7)
    merged['groundwater_7d_change'] = merged.groupby('location')['groundwater_level'].pct_change(7)
    merged['flow_3d_avg'] = merged.groupby('location')['river_flow'].transform(
        lambda x: x.rolling(3).mean())
    merged['evapotranspiration'] = 0.0023 * (merged['temp_avg'] + 17.8) * (merged['temp_max'] - merged['temp_min'])**0.5
    merged['water_balance'] = merged[f'precip_30d_sum'] - merged['evapotranspiration']
    
    # Cyclical encoding
    merged['month_sin'] = np.sin(2 * np.pi * merged['month']/12)
    merged['month_cos'] = np.cos(2 * np.pi * merged['month']/12)
    merged['day_sin'] = np.sin(2 * np.pi * merged['day_of_year']/365)
    merged['day_cos'] = np.cos(2 * np.pi * merged['day_of_year']/365)
    
    # Encode soil_type if present
    if 'soil_type' in merged.columns:
        merged = pd.get_dummies(merged, columns=['soil_type'], prefix='soil')
    
    # Handle categorical location
    if 'location' in merged.columns and pd.api.types.is_object_dtype(merged['location']):
        merged['location'] = pd.Categorical(merged['location']).codes
    
    # Remove original temporal columns
    merged = merged.drop(columns=['month', 'day_of_year'], errors='ignore')
    
    # Remove any remaining non-numeric columns except location and date
    for col in merged.columns:
        if col not in ['location', 'date'] and pd.api.types.is_object_dtype(merged[col]):
            merged = merged.drop(columns=[col])
    
    return merged, metadata

def create_sequences(data, metadata, time_steps=30):
    """Create sequences for LSTM prediction, preserving location grouping"""
    unique_locations = data['location'].unique()
    
    X_sequences = []
    sequence_metadata = []  # To keep track of which sequence corresponds to which location/date
    
    for loc in unique_locations:
        # Filter data for this location
        loc_data = data[data['location'] == loc].copy()
        loc_metadata = metadata.iloc[loc_data.index].copy()
        
        # Remove non-feature columns
        feature_data = loc_data.drop(['location', 'date'], errors='ignore')
        
        # Only create sequences if we have enough data points
        if len(feature_data) >= time_steps:
            # Create sequences
            for i in range(len(feature_data) - time_steps + 1):
                X_sequences.append(feature_data.iloc[i:i+time_steps].values)
                # Store metadata for the prediction point (last point in sequence)
                sequence_metadata.append(loc_metadata.iloc[i+time_steps-1])
    
    return np.array(X_sequences), pd.DataFrame(sequence_metadata)

def predict_from_json(model_binary, scaler_binary, weather_json, hydro_json, geo_json, time_steps=30):
    """
    Make reservoir predictions from JSON data
    
    Args:
        model_binary: Binary content of the model.h5 file
        scaler_binary: Binary content of the scaler.joblib file
        weather_json: JSON data for weather
        hydro_json: JSON data for hydrological measurements
        geo_json: JSON data for geographic information
        time_steps: Number of time steps for LSTM sequences
        
    Returns:
        JSON string with predictions
    """
    try:
        # Save the model binary to a temporary file
        with open('temp_model.h5', 'wb') as f:
            f.write(model_binary)
        
        # Save the scaler binary to a temporary file
        with open('temp_scaler.joblib', 'wb') as f:
            f.write(scaler_binary)
        
        # Load model and scaler
        model = load_model('temp_model.h5')
        scaler = joblib.load('temp_scaler.joblib')
        
        # Process the data
        data, metadata = load_and_preprocess_json_data(
            weather_json, hydro_json, geo_json, scaler
        )
        
        # Scale the data
        feature_cols = [col for col in data.columns if col not in ['date', 'location']]
        data[feature_cols] = scaler.transform(data[feature_cols])
        
        # Create sequences
        X_sequences, seq_metadata = create_sequences(data, metadata, time_steps)
        
        if len(X_sequences) == 0:
            return json.dumps({"error": "Not enough data points to create sequences"})
        
        # Make predictions
        predictions = model.predict(X_sequences)
        
        # Create a DataFrame with predictions and metadata
        results = seq_metadata.copy()
        
        # Add prediction columns
        target_names = ['reservoir_7d', 'reservoir_14d', 'reservoir_30d', 
                        'reservoir_change_7d', 'reservoir_change_14d', 'reservoir_change_30d',
                        'reservoir_7d_mean', 'reservoir_30d_mean', 'reservoir_7d_change']
        
        # Use only the target names that match the number of predictions
        if predictions.shape[1] <= len(target_names):
            used_targets = target_names[:predictions.shape[1]]
        else:
            used_targets = [f'target_{i}' for i in range(predictions.shape[1])]
        
        for i, name in enumerate(used_targets):
            results[name] = predictions[:, i]
        
        # Handle datetime conversion for JSON serialization
        results['date'] = results['date'].dt.strftime('%Y-%m-%d')
        
        # Assess risks
        results = assess_water_risks(results)
        
        # Convert to JSON and return
        return json.dumps(results.to_dict(orient='records'))
        
    except Exception as e:
        return json.dumps({"error": str(e)})

def assess_water_risks(predictions_df, location_capacities=None):
    """
    Evaluate drought and flood risks based on prediction data
    
    Args:
        predictions_df: DataFrame with reservoir predictions
        location_capacities: Dict mapping locations to their min/max capacities
                             If None, uses default values
    
    Returns:
        DataFrame with risk assessments added
    """
    results = predictions_df.copy()
    
    # Default capacities if none provided
    if location_capacities is None:
        # Use generic values based on typical reservoir ranges
        location_capacities = {}
        for location in results['location'].unique():
            # Use the average of the current levels as "normal"
            if 'reservoir_7d' in results.columns:
                avg_level = results[results['location'] == location]['reservoir_7d'].mean()
                location_capacities[location] = {
                    'min': avg_level * 0.7,  # 70% of average as minimum
                    'max': avg_level * 1.3,  # 130% of average as maximum
                    'normal': avg_level
                }
    
    # Initialize risk columns
    results['drought_risk'] = 'None'
    results['flood_risk'] = 'None'
    results['drought_risk_level'] = 0
    results['flood_risk_level'] = 0
    
    for idx, row in results.iterrows():
        location = row['location']
        
        if location not in location_capacities:
            continue
            
        capacity = location_capacities[location]
        normal_level = capacity['normal']
        max_level = capacity['max']
        
        # Predicted reservoir level (30 days ahead)
        reservoir_30d = row.get('reservoir_30d', row.get('reservoir_7d', 0))
        reservoir_7d = row.get('reservoir_7d', 0)
        
        # Percentage of normal capacity
        pct_of_normal_30d = (reservoir_30d / normal_level) * 100
        pct_of_max_7d = (reservoir_7d / max_level) * 100
        
        # Change percentages
        change_30d = row.get('reservoir_change_30d', 0)
        change_7d = row.get('reservoir_change_7d', 0)
        
        # ----- DROUGHT RISK ASSESSMENT -----
        if pct_of_normal_30d < 40 or change_30d < -20:
            results.loc[idx, 'drought_risk'] = 'Extreme'
            results.loc[idx, 'drought_risk_level'] = 4
        elif pct_of_normal_30d < 55 or change_30d < -15:
            results.loc[idx, 'drought_risk'] = 'Severe'
            results.loc[idx, 'drought_risk_level'] = 3
        elif pct_of_normal_30d < 70 or change_30d < -10:
            results.loc[idx, 'drought_risk'] = 'Moderate'
            results.loc[idx, 'drought_risk_level'] = 2
        elif pct_of_normal_30d < 85 or change_30d < -5:
            results.loc[idx, 'drought_risk'] = 'Mild'
            results.loc[idx, 'drought_risk_level'] = 1
        
        # ----- FLOOD RISK ASSESSMENT -----
        if pct_of_max_7d > 100 or change_7d > 15:
            results.loc[idx, 'flood_risk'] = 'Extreme'
            results.loc[idx, 'flood_risk_level'] = 4
        elif pct_of_max_7d > 98 or change_7d > 12:
            results.loc[idx, 'flood_risk'] = 'Severe'
            results.loc[idx, 'flood_risk_level'] = 3
        elif pct_of_max_7d > 95 or change_7d > 8:
            results.loc[idx, 'flood_risk'] = 'Moderate'
            results.loc[idx, 'flood_risk_level'] = 2
        elif pct_of_max_7d > 90 or change_7d > 5:
            results.loc[idx, 'flood_risk'] = 'Mild'
            results.loc[idx, 'flood_risk_level'] = 1
    
    return results

# Example of function for Flask API
def predict_api(json_data):
    """Function for API endpoint that accepts JSON input"""
    try:
        # Parse the incoming JSON
        data = json.loads(json_data)
        
        # Check required fields
        required_fields = ['model_path', 'scaler_path', 'weather_data', 'hydro_data', 'geo_data']
        for field in required_fields:
            if field not in data:
                return json.dumps({"error": f"Missing required field: {field}"})
        
        # Load model and scaler
        try:
            model = load_model(data['model_path'])
            scaler = joblib.load(data['scaler_path'])
        except Exception as e:
            return json.dumps({"error": f"Error loading model or scaler: {str(e)}"})
        
        # Process data and make predictions
        processed_data, metadata = load_and_preprocess_json_data(
            data['weather_data'],
            data['hydro_data'],
            data['geo_data'],
            scaler
        )
        
        # Scale features
        feature_cols = [col for col in processed_data.columns if col not in ['date', 'location']]
        processed_data[feature_cols] = scaler.transform(processed_data[feature_cols])
        
        # Create sequences
        X_sequences, seq_metadata = create_sequences(processed_data, metadata)
        
        if len(X_sequences) == 0:
            return json.dumps({"error": "Not enough data to create sequences"})
        
        # Make predictions
        predictions = model.predict(X_sequences)
        
        # Create results dataframe
        results = seq_metadata.copy()
        
        # Map predictions to target names
        target_names = ['reservoir_7d', 'reservoir_14d', 'reservoir_30d', 
                       'reservoir_change_7d', 'reservoir_change_14d', 'reservoir_change_30d',
                       'reservoir_7d_mean', 'reservoir_30d_mean', 'reservoir_7d_change']
        
        if predictions.shape[1] <= len(target_names):
            used_targets = target_names[:predictions.shape[1]]
        else:
            used_targets = [f'target_{i}' for i in range(predictions.shape[1])]
            
        for i, name in enumerate(used_targets):
            results[name] = predictions[:, i]
        
        # Handle datetime conversion for JSON
        results['date'] = results['date'].dt.strftime('%Y-%m-%d')
        
        # Add risk assessment
        if 'location_capacities' in data:
            results = assess_water_risks(results, data['location_capacities'])
        else:
            results = assess_water_risks(results)
        
        # Return as JSON
        return json.dumps(results.to_dict(orient='records'))
        
    except Exception as e:
        return json.dumps({"error": f"Prediction failed: {str(e)}"})

# Example of a Flask API implementation
"""
from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route('/predict', methods=['POST'])
def predict():
    try:
        json_data = request.get_data(as_text=True)
        result = predict_api(json_data)
        return result
    except Exception as e:
        return jsonify({"error": str(e)})

if __name__ == '__main__':
    app.run(debug=True)
"""