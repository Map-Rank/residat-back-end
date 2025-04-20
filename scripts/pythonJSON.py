import pandas as pd
import numpy as np
import tensorflow as tf
from tensorflow.keras.models import load_model
import joblib
from sklearn.preprocessing import StandardScaler
import sys
import json
from datetime import datetime

def load_and_preprocess_data_from_json(weather_json, hydro_json, geo_json, scaler_path):
    """Load and preprocess new data for prediction from JSON inputs"""
    # Load datasets from JSON
    weather = pd.DataFrame(json.loads(weather_json))
    hydro = pd.DataFrame(json.loads(hydro_json))
    geo = pd.DataFrame(json.loads(geo_json))
    
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
    
    # Preprocess data according to your sample structure
    # Fill missing values
    merged['reservoir_level'] = merged.groupby('location')['reservoir_level'].ffill()
    merged['precipitation'] = merged['precipitation'].fillna(0)
    merged['temp_avg'] = (merged['temp_max'] + merged['temp_min']) / 2
    
    # Drop rows with missing essential values
    essential_cols = ['reservoir_level', 'river_flow', 'groundwater_level']
    merged = merged.dropna(subset=essential_cols)
    
    # Create temporal features
    merged['day_of_year'] = merged['date'].dt.dayofyear
    merged['month'] = merged['date'].dt.month
    
    # Create rolling window features
    for window in [7, 30, 90]:
        merged[f'reservoir_{window}d_mean'] = merged.groupby('location')['reservoir_level'].transform(
            lambda x: x.rolling(window).mean())
        merged[f'precip_{window}d_sum'] = merged.groupby('location')['precipitation'].transform(
            lambda x: x.rolling(window).sum())
        merged[f'river_flow_{window}d_mean'] = merged.groupby('location')['river_flow'].transform(
            lambda x: x.rolling(window).mean())
        merged[f'groundwater_{window}d_mean'] = merged.groupby('location')['groundwater_level'].transform(
            lambda x: x.rolling(window).mean())
    
    # Create other derived features
    merged['reservoir_7d_change'] = merged.groupby('location')['reservoir_level'].pct_change(7)
    merged['groundwater_7d_change'] = merged.groupby('location')['groundwater_level'].pct_change(7)
    merged['flow_3d_avg'] = merged.groupby('location')['river_flow'].transform(
        lambda x: x.rolling(3).mean())
    
    # Calculate evapotranspiration using Hargreaves equation
    merged['evapotranspiration'] = 0.0023 * (merged['temp_avg'] + 17.8) * (merged['temp_max'] - merged['temp_min'])**0.5
    merged['water_balance'] = merged['precip_30d_sum'] - merged['evapotranspiration']
    
    # Cyclical encoding for temporal features
    merged['month_sin'] = np.sin(2 * np.pi * merged['month']/12)
    merged['month_cos'] = np.cos(2 * np.pi * merged['month']/12)
    merged['day_sin'] = np.sin(2 * np.pi * merged['day_of_year']/365)
    merged['day_cos'] = np.cos(2 * np.pi * merged['day_of_year']/365)
    
    # Handle soil_type specifically for 'silty' as per your sample
    if 'soil_type' in merged.columns:
        merged['soil_type_silty'] = (merged['soil_type'] == 'silty').astype(int)
        merged = merged.drop(columns=['soil_type'])
    
    # Handle location specifically for 'Yagoua' as per your sample
    if 'location' in merged.columns:
        merged['location_Yagoua'] = (merged['location'] == 'Yagoua').astype(int)
        merged['location'] = pd.Categorical(merged['location']).codes
    
    # Add required features that might be missing
    for req_feature in ['flood', 'drought']:
        if req_feature not in merged.columns:
            merged[req_feature] = 0
    
    # Remove original temporal columns
    merged = merged.drop(columns=['month', 'day_of_year'], errors='ignore')
    
    # Remove any remaining non-numeric columns except location and date
    for col in merged.columns:
        if col not in ['date', 'location'] and pd.api.types.is_object_dtype(merged[col]):
            merged = merged.drop(columns=[col])
    
    # Load the scaler used during training
    scaler = joblib.load(scaler_path)
    
    # Print features before alignment for debugging
    print("\nFeatures before alignment:")
    print(merged.columns.tolist())
    
    # Get expected features from scaler
    expected_features = scaler.feature_names_in_
    
    # Ensure all expected features exist
    for feature in expected_features:
        if feature not in merged.columns:
            print(f"Adding missing feature: {feature}")
            merged[feature] = 0
    
    # Select ONLY the expected features (don't add date here)
    final_features = list(expected_features)
    
    # Special case: if 'location' is in expected_features but not in merged,
    # use the encoded version we created
    if 'location' in expected_features and 'location' not in merged.columns:
        merged['location'] = pd.Categorical(merged['location']).codes
    
    # Now select only the expected features
    merged = merged[final_features]
    
    print("\nFinal features after alignment:")
    print(merged.columns.tolist())
    print(f"Total features: {len(merged.columns)}")
    
    return merged, metadata, scaler

def create_sequences(data, metadata, time_steps=30):
    """Create sequences for LSTM prediction, preserving location grouping"""
    unique_locations = data['location'].unique()
    
    X_sequences = []
    sequence_metadata = []
    
    for loc in unique_locations:
        loc_data = data[data['location'] == loc].copy()
        loc_metadata = metadata.iloc[loc_data.index].copy()
        
        # Remove non-feature columns - ensure we're only keeping numeric data
        feature_data = loc_data.select_dtypes(include=[np.number])
        
        # Only create sequences if we have enough data points
        if len(feature_data) >= time_steps:
            for i in range(len(feature_data) - time_steps + 1):
                X_sequences.append(feature_data.iloc[i:i+time_steps].values)
                sequence_metadata.append(loc_metadata.iloc[i+time_steps-1])
    
    if len(X_sequences) > 0:
        print(f"\nSequence shape: {np.array(X_sequences).shape}")
    
    return np.array(X_sequences), pd.DataFrame(sequence_metadata)

def predict_reservoir_levels(model, data, metadata, scaler, time_steps=30):
    """Make predictions using the LSTM model, handling feature name mismatches"""
    # Get the expected feature names from the scaler
    expected_features = scaler.feature_names_in_
    
    print("\nFeature alignment:")
    print(f"Expected features ({len(expected_features)}): {expected_features}")
    print(f"Current features ({len(data.columns)}): {data.columns.tolist()}")
    
    # Create a DataFrame with exactly the expected features
    aligned_data = pd.DataFrame(index=data.index)
    
    # Copy existing expected features
    for feature in expected_features:
        if feature in data.columns:
            aligned_data[feature] = data[feature]
        else:
            print(f"Adding missing feature with zeros: {feature}")
            aligned_data[feature] = 0.0
    
    # Now transform using the properly aligned features
    aligned_data_transformed = scaler.transform(aligned_data)
    
    # Create a new DataFrame with scaled values
    scaled_data = pd.DataFrame(aligned_data_transformed, columns=expected_features, index=data.index)
    
    # Add back location (needed for sequence creation)
    if 'location' in data.columns:
        scaled_data['location'] = data['location'].values

    # Create sequences
    X_sequences, seq_metadata = create_sequences(scaled_data, metadata, time_steps)
    
    if len(X_sequences) == 0:
        print("\nWarning: No sequences could be created - not enough data points")
        return pd.DataFrame()
    
    # Verify input shape matches model expectations
    print(f"\nModel input shape expectation: {model.input_shape}")
    print(f"Actual input shape: {X_sequences.shape}")
    
    if X_sequences.shape[2] != model.input_shape[2]:
        print(f"\nERROR: Feature count mismatch! Model expects {model.input_shape[2]} features, got {X_sequences.shape[2]}")
        sys.exit(1)
    
    # Make predictions
    predictions = model.predict(X_sequences)
    
    # Create results DataFrame
    results = seq_metadata.copy()
    
    # Add prediction columns
    target_names = ['reservoir_7d', 'reservoir_14d', 'reservoir_30d', 
                   'reservoir_change_7d', 'reservoir_change_14d', 'reservoir_change_30d',
                   'reservoir_7d_mean', 'reservoir_30d_mean', 'reservoir_7d_change']
    
    if predictions.shape[1] <= len(target_names):
        used_targets = target_names[:predictions.shape[1]]
    else:
        used_targets = [f'target_{i}' for i in range(predictions.shape[1])]
    
    for i, name in enumerate(used_targets):
        results[name] = predictions[:, i]
    
    return results

def predict_from_json(weather_json, hydro_json, geo_json, model_path='model.h5', scaler_path='scaler.joblib'):
    """Main function to make predictions from JSON inputs"""
    # Load model
    print("Loading model...")
    model = load_model(model_path)
    print(f"Model loaded from {model_path}")
    
    # Load and preprocess data
    print("\nLoading and preprocessing data...")
    data, metadata, scaler = load_and_preprocess_data_from_json(
        weather_json, hydro_json, geo_json, scaler_path
    )
    print(f"\nData prepared: {data.shape[0]} samples")
    
    # Make predictions
    print("\nMaking predictions...")
    results = predict_reservoir_levels(model, data, metadata, scaler)
    
    if not results.empty:
        print(f"\nPredictions made for {len(results)} time points")
        # Convert date to string for JSON serialization
        results['date'] = results['date'].dt.strftime('%Y-%m-%d')
        return results.to_dict(orient='records')
    else:
        print("\nNo predictions could be made. Check if you have enough data points.")
        return []

# Example usage with your sample data format:
if __name__ == "__main__":
    # Sample data matching your CSV structure
    weather_json = '''
    {
        "date": [
            "2025-03-10", "2025-03-11", "2025-03-12", "2025-03-13", "2025-03-14", 
            "2025-03-15", "2025-03-16", "2025-03-17", "2025-03-18", "2025-03-19", 
            "2025-03-20", "2025-03-21", "2025-03-22", "2025-03-23", "2025-03-24", 
            "2025-03-25", "2025-03-26", "2025-03-27", "2025-03-28", "2025-03-29", 
            "2025-03-30", "2025-03-31", "2025-04-01", "2025-04-02", "2025-04-03", 
            "2025-04-04", "2025-04-05", "2025-04-06", "2025-04-07", "2025-04-08"
        ],
        "temp_max": [
            37.8, 38.2, 38.5, 38.9, 39.2, 
            39.5, 39.6, 39.8, 39.7, 39.5, 
            39.2, 38.9, 39.1, 39.4, 39.6, 
            39.8, 39.7, 39.5, 39.2, 38.9, 
            38.5, 38.7, 39.0, 39.3, 39.5, 
            39.7, 39.8, 39.6, 39.4, 39.2
        ],
        "temp_min": [
            22.5, 23.1, 23.4, 23.6, 23.8, 
            24.0, 24.1, 24.2, 24.3, 24.1, 
            24.0, 23.8, 23.7, 23.9, 24.2, 
            24.4, 24.3, 24.1, 23.9, 23.7, 
            23.4, 23.6, 23.8, 24.0, 24.2, 
            24.4, 24.5, 24.3, 24.1, 23.9
        ],
        "precipitation": [
            0.0, 0.0, 0.0, 0.0, 0.0, 
            0.0, 0.0, 0.0, 0.0, 0.0, 
            0.5, 0.0, 0.0, 0.0, 0.0, 
            0.0, 0.0, 0.0, 0.0, 1.2, 
            0.0, 0.0, 0.0, 0.0, 0.0, 
            0.0, 0.0, 0.0, 0.0, 0.0
        ],
        "soil_moisture": [
            18.5, 18.1, 17.8, 17.4, 17.0, 
            16.7, 16.3, 16.0, 15.7, 15.4, 
            16.1, 15.8, 15.5, 15.2, 14.9, 
            14.6, 14.3, 14.0, 13.7, 15.6, 
            15.2, 14.9, 14.6, 14.3, 14.0, 
            13.7, 13.4, 13.1, 12.8, 12.5
        ]
    }
    '''
    
    hydro_json = '''
    {
        "measurement_date": [
            "2025-03-10", "2025-03-11", "2025-03-12", "2025-03-13", "2025-03-14", 
            "2025-03-15", "2025-03-16", "2025-03-17", "2025-03-18", "2025-03-19", 
            "2025-03-20", "2025-03-21", "2025-03-22", "2025-03-23", "2025-03-24", 
            "2025-03-25", "2025-03-26", "2025-03-27", "2025-03-28", "2025-03-29", 
            "2025-03-30", "2025-03-31", "2025-04-01", "2025-04-02", "2025-04-03", 
            "2025-04-04", "2025-04-05", "2025-04-06", "2025-04-07", "2025-04-08"
        ],
        "river_flow": [
            112.3, 111.8, 111.2, 110.7, 110.1, 
            109.6, 109.0, 108.5, 108.0, 107.4, 
            107.5, 107.1, 106.6, 106.1, 105.5, 
            105.0, 104.5, 104.0, 103.4, 103.8, 
            103.4, 102.9, 102.3, 101.8, 101.3, 
            100.7, 100.2, 99.7, 99.1, 98.6
        ],
        "groundwater_level": [
            43.8, 43.6, 43.4, 43.2, 43.0, 
            42.8, 42.6, 42.4, 42.2, 42.0, 
            42.1, 41.9, 41.7, 41.5, 41.3, 
            41.1, 40.9, 40.7, 40.5, 40.8, 
            40.7, 40.5, 40.3, 40.1, 39.9, 
            39.7, 39.5, 39.3, 39.1, 38.9
        ],
        "reservoir_level": [
            147.2, 146.9, 146.5, 146.1, 145.7, 
            145.3, 144.9, 144.5, 144.1, 143.7, 
            143.8, 143.4, 143.0, 142.6, 142.2, 
            141.8, 141.4, 141.0, 140.6, 140.9, 
            140.6, 140.2, 139.8, 139.4, 139.0, 
            138.6, 138.2, 137.8, 137.4, 137.0
        ],
        "location": [
            "Yagoua", "Yagoua", "Yagoua", "Yagoua", "Yagoua",
            "Yagoua", "Yagoua", "Yagoua", "Yagoua", "Yagoua",
            "Yagoua", "Yagoua", "Yagoua", "Yagoua", "Yagoua",
            "Yagoua", "Yagoua", "Yagoua", "Yagoua", "Yagoua",
            "Yagoua", "Yagoua", "Yagoua", "Yagoua", "Yagoua",
            "Yagoua", "Yagoua", "Yagoua", "Yagoua", "Yagoua"
        ]
    }'''
    
    geo_json = '''
    {
        "location": ["Yagoua"],
        "elevation": [330],
        "slope": [3],
        "soil_type": ["silty"]
    }
    '''
    
    results = predict_from_json(weather_json, hydro_json, geo_json)
    print(json.dumps(results, indent=2))