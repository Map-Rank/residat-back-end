import pandas as pd
import numpy as np
import tensorflow as tf
from sklearn.preprocessing import RobustScaler
import warnings

def predict_reservoir_levels(
    input_data, 
    model_path='model.h5', 
    time_steps=30,
    forecast_horizons=[7, 14, 30]
):
    """
    Makes reservoir level predictions using a pre-trained LSTM model.
    
    Parameters:
    -----------
    input_data : dict or pandas DataFrame
        If dict, contains paths to CSV files:
            - weather_path: path to weather data
            - hydro_path: path to hydrological data
            - history_path: path to historical event data
            - geo_path: path to geographical data
        If DataFrame, already processed and contains all necessary features
    model_path : str
        Path to the saved model file (.h5)
    time_steps : int
        Number of time steps used for prediction (must match model training)
    forecast_horizons : list
        Days ahead for which predictions will be made
        
    Returns:
    --------
    DataFrame
        Contains predictions for specified forecast horizons with dates and locations
    """
    try:
        # Load the trained model
        model = tf.keras.models.load_model(model_path)
        print(f"Model loaded from {model_path}")
        
        # If input is file paths, process the data
        if isinstance(input_data, dict):
            features, _, metadata = prepare_reservoir_dataset(input_data)
        else:
            # Input is already a DataFrame
            features = input_data.copy()
            metadata = features[['date', 'location']].copy()
            
            # Drop non-feature columns if they exist
            cols_to_drop = ['date', 'location'] + [col for col in features.columns 
                                                  if col.startswith('reservoir_') and any(f'_{d}d' in col for d in forecast_horizons)]
            features = features.drop(columns=cols_to_drop, errors='ignore')
        
        # Group data by location for processing
        predictions = []
        unique_locations = metadata['location'].unique()
        
        for loc in unique_locations:
            # Filter data for this location
            loc_mask = metadata['location'] == loc
            loc_features = features[loc_mask].reset_index(drop=True)
            loc_metadata = metadata[loc_mask].reset_index(drop=True)
            
            # Drop non-numeric columns
            for col in loc_features.columns:
                if not pd.api.types.is_numeric_dtype(loc_features[col]):
                    print(f"Dropping non-numeric column: {col}")
                    loc_features = loc_features.drop(columns=[col])
            
            # Need at least time_steps data points
            if len(loc_features) < time_steps:
                print(f"Warning: Location {loc} has insufficient data points ({len(loc_features)}). Skipping.")
                continue
                
            # Scale features
            scaler = RobustScaler()
            loc_features_scaled = scaler.fit_transform(loc_features)
            
            # Create sequences for prediction
            X_sequences = []
            for i in range(len(loc_features_scaled) - time_steps + 1):
                X_sequences.append(loc_features_scaled[i:i + time_steps])
            
            X_pred = np.array(X_sequences)
            
            # Make predictions
            y_pred = model.predict(X_pred)
            
            # Create prediction DataFrame
            pred_dates = loc_metadata['date'].iloc[time_steps-1:].reset_index(drop=True)
            pred_df = pd.DataFrame({'date': pred_dates, 'location': loc})
            
            # Add predictions for each forecast horizon
            for i, horizon in enumerate(forecast_horizons):
                pred_df[f'predicted_reservoir_{horizon}d'] = y_pred[:, i]
                
            predictions.append(pred_df)
        
        if not predictions:
            raise ValueError("No predictions could be generated. Check input data.")
            
        # Combine predictions from all locations
        all_predictions = pd.concat(predictions, ignore_index=True)
        return all_predictions
        
    except Exception as e:
        print(f"Error in prediction: {str(e)}")
        raise

def load_and_merge_data(weather_path, history_path, hydro_path, geo_path):
    """Load and merge all data sources for reservoir prediction"""
    # Load datasets
    weather = pd.read_csv(weather_path, parse_dates=['date'])
    hydro = pd.read_csv(hydro_path, parse_dates=['measurement_date'])
    history = pd.read_csv(history_path, parse_dates=['event_date'])
    geo = pd.read_csv(geo_path)
    
    # Standardize column names and merge
    hydro = hydro.rename(columns={'measurement_date': 'date'})
    history = history.rename(columns={'event_date': 'date'})

    merged = pd.merge(weather, hydro, on=['date'], how='inner')
    merged = pd.merge(merged, history, on=['date'], how='left')
    merged = pd.merge(merged, geo, on='location', how='left')
    
    return merged.sort_values(['location', 'date'])

def preprocess_reservoir_data(df):
    """Clean and prepare reservoir-focused dataset"""
    print(f"Initial data shape: {df.shape}")
    
    # Check for missing values before processing
    missing_values_count = df.isna().sum()
    print("Missing values before preprocessing:")
    print(missing_values_count[missing_values_count > 0])
    
    # Handle critical columns first
    for col in ['reservoir_level', 'river_flow', 'groundwater_level']:
        if col in df.columns and df[col].isna().any():
            print(f"Warning: Missing values in {col}. Using group-wise interpolation.")
            # Use more robust interpolation within each location group
            df[col] = df.groupby('location')[col].transform(
                lambda x: x.interpolate(method='linear', limit_direction='both'))
    
    # Forward-fill reservoir levels (assuming continuous monitoring)
    df['reservoir_level'] = df.groupby('location')['reservoir_level'].ffill()
    
    # Handle missing values in other columns
    df['precipitation'] = df['precipitation'].fillna(0)
    
    # Calculate average temperature properly, handling NaN values
    if 'temp_max' in df.columns and 'temp_min' in df.columns:
        # First interpolate any missing temperature values within each location
        df['temp_max'] = df.groupby('location')['temp_max'].transform(
            lambda x: x.interpolate(method='linear', limit_direction='both'))
        df['temp_min'] = df.groupby('location')['temp_min'].transform(
            lambda x: x.interpolate(method='linear', limit_direction='both'))
        
        # Then calculate average temperature
        df['temp_avg'] = (df['temp_max'] + df['temp_min']) / 2
    
    # Fill missing flood and drought indicators with 0 (no event)
    if 'flood' in df.columns:
        df['flood'] = df['flood'].fillna(0)
    if 'drought' in df.columns:
        df['drought'] = df['drought'].fillna(0)
    
    # Fill other columns with median values by location
    numeric_cols = df.select_dtypes(include=['float64', 'int64']).columns
    for col in numeric_cols:
        if df[col].isna().any() and col not in ['reservoir_level', 'precipitation', 'temp_avg', 'temp_max', 'temp_min', 'flood', 'drought']:
            df[col] = df.groupby('location')[col].transform(lambda x: x.fillna(x.median()))
    
    # As a last resort, drop any remaining rows with NaN in critical columns
    critical_cols = ['reservoir_level', 'river_flow', 'groundwater_level']
    before_drop = df.shape[0]
    df = df.dropna(subset=critical_cols)
    after_drop = df.shape[0]
    if before_drop > after_drop:
        print(f"Dropped {before_drop - after_drop} rows with missing values in critical columns.")
    
    # Final check for any remaining NaN values
    remaining_missing = df.isna().sum()
    if remaining_missing.sum() > 0:
        print("Remaining missing values after preprocessing:")
        print(remaining_missing[remaining_missing > 0])
        
        # For any remaining NaN values, use global median as last resort
        df = df.fillna(df.median())
    
    print(f"Final data shape after preprocessing: {df.shape}")
    return df

def create_reservoir_features(df, lookback=90):
    """Generate features for reservoir level prediction"""
    # Temporal features
    df['day_of_year'] = df['date'].dt.dayofyear
    df['month'] = df['date'].dt.month
    
    # Rolling hydrological features - using min_periods to avoid NaN issues
    for window in [7, 30, lookback]:
        # Using min_periods to avoid NaNs at the beginning
        min_periods = max(1, window // 2)
        
        # For reservoir level
        df[f'reservoir_{window}d_mean'] = df.groupby('location')['reservoir_level'].transform(
            lambda x: x.rolling(window, min_periods=min_periods).mean())
        
        # For precipitation
        df[f'precip_{window}d_sum'] = df.groupby('location')['precipitation'].transform(
            lambda x: x.rolling(window, min_periods=min_periods).sum())
        
        # For river flow
        df[f'river_flow_{window}d_mean'] = df.groupby('location')['river_flow'].transform(
            lambda x: x.rolling(window, min_periods=min_periods).mean())
        
        # For groundwater
        df[f'groundwater_{window}d_mean'] = df.groupby('location')['groundwater_level'].transform(
            lambda x: x.rolling(window, min_periods=min_periods).mean())
    
    # Rate of change features - with proper handling for NaNs
    for col in ['reservoir_level', 'groundwater_level']:
        col_name = 'reservoir' if col == 'reservoir_level' else 'groundwater'
        df[f'{col_name}_7d_change'] = df.groupby('location')[col].transform(
            lambda x: x.pct_change(7).replace([np.inf, -np.inf], np.nan).fillna(0))
    
    df['flow_3d_avg'] = df.groupby('location')['river_flow'].transform(
        lambda x: x.rolling(3, min_periods=1).mean())
    
    # Water balance features - with checks for NaN values
    if 'temp_avg' in df.columns:
        # Ensure temp_max and temp_min don't have NaN values before computation
        df['temp_diff'] = (df['temp_max'] - df['temp_min']).replace(0, 0.1)  # Avoid division by zero
        df['evapotranspiration'] = 0.0023 * (df['temp_avg'] + 17.8) * np.sqrt(df['temp_diff'])
        df['water_balance'] = df['precip_30d_sum'] - df['evapotranspiration']
    
    # Watershed and location handling (for better generalization)
    if 'Watershed' in df.columns:
        # Convert watershed to categorical if it's not already
        df['Watershed'] = df['Watershed'].astype('category')
    
    # Check for any NaN values after feature creation
    missing_counts = df.isna().sum()
    if missing_counts.sum() > 0:
        print("Missing values after feature creation:")
        print(missing_counts[missing_counts > 0])
        
        # Fill any remaining NaNs with appropriate values
        numeric_cols = df.select_dtypes(include=['float64', 'int64']).columns
        for col in numeric_cols:
            if df[col].isna().any():
                if 'change' in col or 'diff' in col:
                    # For rate-of-change features, 0 is a reasonable default
                    df[col] = df[col].fillna(0)
                else:
                    # For other features, use median by location
                    df[col] = df.groupby('location')[col].transform(lambda x: x.fillna(x.median()))
    
    return df

def encode_features(df):
    """Encode categorical features for reservoir prediction"""
    # Make a copy to avoid SettingWithCopyWarning
    df = df.copy()
    
    # Cyclical encoding for temporal features
    df['month_sin'] = np.sin(2 * np.pi * df['month']/12)
    df['month_cos'] = np.cos(2 * np.pi * df['month']/12)
    df['day_sin'] = np.sin(2 * np.pi * df['day_of_year']/365)
    df['day_cos'] = np.cos(2 * np.pi * df['day_of_year']/365)
    
    # One-hot encode soil type and other categorical features
    categorical_cols = []
    if 'soil_type' in df.columns:
        categorical_cols.append('soil_type')
    
    if 'Watershed' in df.columns:
        categorical_cols.append('Watershed')
    
    if categorical_cols:
        # For prediction, we'll use a simpler approach to handle new categories
        for col in categorical_cols:
            # Get all unique values
            unique_vals = df[col].dropna().unique()
            
            # Create dummy variables
            for val in unique_vals:
                df[f'{col}_{val}'] = (df[col] == val).astype(int)
    
    # Handle location column specially to avoid duplicates later
    if 'location' in df.columns:
        # Get unique locations
        locations = df['location'].unique()
        print(f"Encoding {len(locations)} unique locations")
        
        # Create dummy variables
        for loc in locations:
            df[f'location_{loc}'] = (df['location'] == loc).astype(int)
    
    # Drop original categorical columns and unused columns
    cols_to_drop = ['month', 'day_of_year'] + categorical_cols
    return df.drop(columns=cols_to_drop, errors='ignore')

def prepare_reservoir_dataset(data_paths):
    """Complete pipeline for reservoir level prediction"""
    # Load and merge
    df = load_and_merge_data(**data_paths)
    
    # Save a copy of the dates and locations for later reference
    metadata = df[['date', 'location']].copy()
    
    # Preprocess
    df = preprocess_reservoir_data(df)
    df = create_reservoir_features(df)
    df = encode_features(df)
    
    # For prediction, we don't need to create targets
    
    # Important: If location is used as a feature AND in metadata, we need to prevent duplication later
    # So we'll drop it from features if it has been one-hot encoded
    location_encoded = any(col.startswith('location_') for col in df.columns)
    if location_encoded and 'location' in df.columns:
        df_features = df.drop(columns=['location', 'date'], errors='ignore')
        print("Dropping 'location' from features as it's already one-hot encoded")
    else:
        df_features = df.drop(columns=['date'], errors='ignore')
    
    # Get the final feature names
    feature_names = df_features.columns.tolist()
    print("Features used in prediction:")
    print(feature_names)
    
    return df_features, None, metadata

# Example usage:
if __name__ == "__main__":
    # Example 1: Using file paths
    data_paths = {
        'weather_path': 'CSVs/new_weather_data.csv',
        'hydro_path': 'CSVs/new_hydro_data.csv',
        'geo_path': 'CSVs/geo.csv',
        'history_path': 'CSVs/history_data.csv'
    }
    
    try:
        # Make predictions
        predictions = predict_reservoir_levels(
            data_paths,
            model_path='model.h5',
            time_steps=30,
            forecast_horizons=[1, 2, 3, 4, 5, 6, 7, 14, 30]
        )
        
        # Display predictions
        print(predictions.head())
        
        # Save predictions to CSV
        predictions.to_csv('reservoir_predictions.csv', index=False)
        print("Predictions saved to 'reservoir_predictions.csv'")
        
    except Exception as e:
        print(f"Error: {str(e)}")