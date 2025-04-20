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
    merged['water_balance'] = merged['precip_30d_sum'] - merged['evapotranspiration']

    # Cyclical encoding
    merged['month_sin'] = np.sin(2 * np.pi * merged['month']/12)
    merged['month_cos'] = np.cos(2 * np.pi * merged['month']/12)
    merged['day_sin'] = np.sin(2 * np.pi * merged['day_of_year']/365)
    merged['day_cos'] = np.cos(2 * np.pi * merged['day_of_year']/365)

    # Soil type one-hot
    if 'soil_type' in merged.columns:
        merged['soil_type_silty'] = (merged['soil_type'] == 'silty').astype(int)
        merged = merged.drop(columns=['soil_type'])

    # Watershed one-hot
    if 'Watershed' in merged.columns:
        merged['Watershed_0'] = (merged['Watershed'] == 0).astype(int)
        merged['Watershed_1'] = (merged['Watershed'] == 1).astype(int)
        merged = merged.drop(columns=['Watershed'])
    else:
        merged['Watershed_0'] = 0
        merged['Watershed_1'] = 1

    # location_Yagoua only (drop raw location before scaler)
    if 'location' in merged.columns:
        merged['location_Yagoua'] = (merged['location'] == 'Yagoua').astype(int)
        # DROP 'location' to avoid feature mismatch
        merged = merged.drop(columns=['location'])

    # Flood / Drought defaults
    for req_feature in ['flood', 'drought']:
        if req_feature not in merged.columns:
            merged[req_feature] = 0

    # Remove unneeded
    merged = merged.drop(columns=['month', 'day_of_year'], errors='ignore')

    # Drop non-numeric just in case
    for col in merged.columns:
        if col not in ['date'] and not pd.api.types.is_numeric_dtype(merged[col]):
            merged = merged.drop(columns=[col])

    # Load the scaler
    scaler = joblib.load(scaler_path)
    expected_features = scaler.feature_names_in_

    print("\nFeatures before alignment:")
    print(merged.columns.tolist())

    # Add any missing features with zeros
    for feature in expected_features:
        if feature not in merged.columns:
            print(f"Adding missing feature: {feature}")
            merged[feature] = 0

    # Reorder and align
    merged = merged[expected_features.tolist()]
    scaled_data = scaler.transform(merged)

    return scaled_data, metadata

# ==================== MAIN ======================

if __name__ == '__main__':
    model_path = 'model.h5'
    weather_path = 'CSVs/new_weather_data.csv'
    hydro_path = 'CSVs/new_hydro_data.csv'
    geo_path = 'CSVs/geo.csv'
    scaler_path = 'scaler.joblib'

    print("Loading model...")
    model = load_model(model_path)
    print(f"Model input shape: {model.input_shape}")

    print("\nLoading and preprocessing data...")
    X, meta = load_and_preprocess_data(weather_path, hydro_path, geo_path, scaler_path)

    # Pick last 30 records (one sequence)
    sequence_length = 30
    if len(X) < sequence_length:
        raise ValueError("Not enough data to form a sequence")

    X_seq = X[-sequence_length:]
    X_seq = np.expand_dims(X_seq, axis=0)  # Shape: (1, 30, features)

    print(f"\nSequence shape: {X_seq.shape}")
    print(f"Model input shape expectation: {model.input_shape}")
    print(f"Actual input shape: {X_seq.shape}")

    if X_seq.shape[-1] != model.input_shape[-1]:
        raise ValueError(f"\nERROR: Feature count mismatch! Model expects {model.input_shape[-1]} features, got {X_seq.shape[-1]}")

    print("\nMaking predictions...")
    predictions = model.predict(X_seq)
    print("Predictions:", predictions)
