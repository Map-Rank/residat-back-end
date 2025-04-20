import pandas as pd
import numpy as np
import tensorflow as tf
from tensorflow.keras.models import load_model
import joblib
from sklearn.preprocessing import StandardScaler
import sys

def load_and_preprocess_data(weather_path, hydro_path, geo_path, scaler_path):
    """Load and preprocess new data for prediction"""
    # Load datasets
    weather = pd.read_csv(weather_path, parse_dates=['date'])
    hydro = pd.read_csv(hydro_path, parse_dates=['measurement_date'])
    geo = pd.read_csv(geo_path)
    
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
    
    # Handle soil_type to match training (expecting 'soil_type_silty')
    if 'soil_type' in merged.columns:
        merged['soil_type_silty'] = (merged['soil_type'] == 'silty').astype(int)
        merged = merged.drop(columns=['soil_type'])
    elif 'soil_Clay' in merged.columns:
        merged['soil_type_silty'] = 0  # Assuming clay is not silty
        merged = merged.drop(columns=['soil_Clay'])
    
    # Handle Watershed - ensure one-hot encoding exists
    if 'Watershed' in merged.columns:
        if isinstance(merged['Watershed'].iloc[0], (int, float)):
            # Create one-hot encoding if Watershed is numeric
            merged['Watershed_0'] = (merged['Watershed'] == 0).astype(int)
            merged['Watershed_1'] = (merged['Watershed'] == 1).astype(int)
            merged = merged.drop(columns=['Watershed'])
        else:
            # Handle categorical Watershed if needed
            pass
    else:
        # Create Watershed one-hot columns if they don't exist
        merged['Watershed_0'] = 0
        merged['Watershed_1'] = 1  # Or adjust based on your data
    
    # Handle location to match training (expecting 'location_Yagoua')
    if 'location' in merged.columns:
        if pd.api.types.is_object_dtype(merged['location']):
            merged['location_Yagoua'] = (merged['location'] == 'Yagoua').astype(int)
            merged['location'] = pd.Categorical(merged['location']).codes
        else:
            # If already encoded, create the Yagoua dummy
            merged['location_Yagoua'] = 0  # Adjust based on your actual data
    
    # Add required features that might be missing
    for req_feature in ['flood', 'drought']:
        if req_feature not in merged.columns:
            merged[req_feature] = 0
    
    # Remove original temporal columns
    merged = merged.drop(columns=['month', 'day_of_year', ], errors='ignore')
    
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
        # Use the categorical codes we created earlier
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
            # Handle case where feature might be a duplicate
            if isinstance(data[feature], pd.DataFrame):
                aligned_data[feature] = data[feature].iloc[:, 0]  # Take first column if it's a DataFrame
            else:
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
        print("This usually means your preprocessing doesn't match the training setup")
        print("Check for differences in:")
        print("- One-hot encoded categories (soil_type, Watershed, location)")
        print("- Rolling window features")
        print("- Missing or extra derived features")
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

def main():
    # Paths
    model_path = 'model.h5'
    scaler_path = 'scaler.joblib'
    
    # Input data paths (new data to predict on)
    weather_path = 'CSVs/new_weather_data.csv'
    hydro_path = 'CSVs/new_hydro_data.csv'
    geo_path = 'CSVs/geo.csv'
    
    # Load model
    print("Loading model...")
    model = load_model(model_path)
    print(f"Model loaded from {model_path}")
    print(f"Model input shape: {model.input_shape}")
    
    # Load and preprocess data
    print("\nLoading and preprocessing data...")
    data, metadata, scaler = load_and_preprocess_data(
        weather_path, hydro_path, geo_path, scaler_path
    )
    print(f"\nData prepared: {data.shape[0]} samples")
    
    # Make predictions
    print("\nMaking predictions...")
    results = predict_reservoir_levels(model, data, metadata, scaler)
    
    if not results.empty:
        print(f"\nPredictions made for {len(results)} time points")
        results.to_csv('reservoir_predictions.csv', index=False)
        print("Predictions saved to 'reservoir_predictions.csv'")
    else:
        print("\nNo predictions could be made. Check if you have enough data points.")

if __name__ == "__main__":
    main()