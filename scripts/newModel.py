import pandas as pd
import numpy as np
from sklearn.preprocessing import OneHotEncoder
from sklearn.model_selection import train_test_split
import pyarrow 

def load_and_merge_data(weather_path, history_path, hydro_path, geo_path):
    """Load and merge all data sources"""
    # Load datasets with proper date parsing
    weather = pd.read_csv(weather_path, parse_dates=['date'])
    history = pd.read_csv(history_path, parse_dates=['event_date'])
    hydro = pd.read_csv(hydro_path, parse_dates=['measurement_date'])
    geo = pd.read_csv(geo_path)
    
    print("\nFiles loaded successfully")

    # Standardize location naming
    history = history.rename(columns={'event_date': 'date'})
    hydro = hydro.rename(columns={'measurement_date': 'date'})
    
    print("\nHydro column rename")

    # Merge weather and hydro data
    merged = pd.merge(weather, hydro, 
                     on=['date', ],
                     how='left')
    
    print("\nHydro and Weather merged")

    # Merge with historical events
    merged = pd.merge(merged, history,
                     on=['date'],
                     how='left')
    
    print("\nMerged and History merged")
    
    # Merge with geo data
    merged = pd.merge(merged, geo,
                     on='location',
                     how='left')

    print("\nMerged and GEO merged")
    print("\n*************************************")
    print("\n*******FUNCTION END**********")
    # Sort by location and date
    # merged = merged.sort_values(['location', 'date']).reset_index(drop=True)
    
    return merged

def handle_missing_values(df):
    
    print("\n*************************************")
    print("\n*******FUNCTION MISSING START**********")

    """Clean and impute missing data"""
    # Forward fill hydrological measurements
    hydro_cols = ['river_flow', 'groundwater_level', 'reservoir_level']
    df[hydro_cols] = df.groupby('location')[hydro_cols].ffill()
    print("\nHydro columns grouped")

    # Fill precipitation with 0 if missing
    df['precipitation'] = df['precipitation'].fillna(0)
    print("\nPrecipitation column Fill NA")

    # Fill missing event severity with 0 (no event)
    df['flood_severity'] = df['flood_severity'].fillna(0)
    df['drought_severity'] = df['drought_severity'].fillna(0)
    print("\nFlood sev fill NA")

    # Drop remaining rows with missing values
    df = df.dropna(subset=['temp_max', 'temp_min', 'soil_moisture'])

    print("\n*************************************")
    print("\n*******FUNCTION END**********")
    return df

def create_temporal_features(df):
    print("\n*************************************")
    print("\n*******FUNCTION TEMP FEATURE START**********")

    """Generate time-based features"""
    # Rolling precipitation features
    df['3d_precip_sum'] = df.groupby('location')['precipitation'].transform(
        lambda x: x.rolling(3, min_periods=1).sum())
    print("\nFeature 3d_precip_sum added")

    df['7d_precip_avg'] = df.groupby('location')['precipitation'].transform(
        lambda x: x.rolling(7, min_periods=1).mean())
    print("\nFeature 7d_precip_avg added")

    # Temperature features
    df['temp_range'] = df['temp_max'] - df['temp_min']
    print("\nFeature date range added")

    df['temp_7d_avg'] = df.groupby('location')['temp_max'].transform(
        lambda x: x.rolling(7, min_periods=1).mean())
    print("\nFeature temp_7d_avg added")

    # Hydrological features
    # df['river_flow_3d_diff'] = df.groupby('location')['river_flow'].diff(3)
    df['river_flow_3d_diff'] = df.groupby('location')['river_flow'].diff(3).fillna(0)
    print("\nFeature river_flow_3d_diff added")

    df['groundwater_7d_avg'] = df.groupby('location')['groundwater_level'].transform(
        lambda x: x.rolling(7, min_periods=1).mean())
    print("\nFeature groundwater_7d_avg added")

    # Time since last rain
    # Usage in your pipeline
    df = calculate_days_since_rain(df)

    print("\nFeature days_since_rain added")
    
    print("\n*************************************")
    print("\n*******FUNCTION END**********")
    df.to_csv('temp.csv', index=False)
    return df

# CORRECTED CODE FOR DAYS SINCE RAIN CALCULATION
def calculate_days_since_rain(df):
    # Create groups that reset after each rainfall
    df['rain_group'] = df.groupby('location')['precipitation'].transform(
        lambda x: x.ne(0).cumsum()
    )
    
    # Calculate consecutive dry days within each group
    df['days_since_rain'] = df.groupby(['location', 'rain_group']).cumcount() + 1
    
    # Reset counter on rain days
    df['days_since_rain'] = df['days_since_rain'].where(df['precipitation'] == 0, 0)
    
    # Cleanup temporary column
    df = df.drop(columns=['rain_group'])
    
    return df

def create_derived_features(df):
    """Create domain-specific combined features"""
    print("\n*************************************")
    print("\n*******FUNCTION create_derived_features START**********")

    # Soil moisture capacity index
    # df['soil_moisture_capacity'] = df['soil_moisture'] * df['slope'] / df['elevation']
    EPSILON = 1e-7  # Small value to prevent division by zero

    # Fixed calculations
    df['soil_moisture_capacity'] = df['soil_moisture'] * df['slope'] / (df['elevation'] + EPSILON)
    df['flood_risk_index'] = (df['3d_precip_sum'] * df['river_flow']) / (df['soil_moisture_capacity'] + EPSILON)
    df['drought_risk_index'] = (df['temp_7d_avg'] * df['days_since_rain']) / (df['groundwater_level'] + EPSILON)
    print("\nFeature soil_moisture_capacity added")

    # Flood risk index
    # df['flood_risk_index'] = (df['3d_precip_sum'] * df['river_flow']) / df['soil_moisture_capacity']
    print("\nFeature flood_risk_index added")

    # Drought risk index
    # df['drought_risk_index'] = (df['temp_7d_avg'] * df['days_since_rain']) / df['groundwater_level']
    print("\nFeature drought_risk_index added")

    # Seasonality features
    df['month'] = df['date'].dt.month
    df['day_of_year'] = df['date'].dt.dayofyear
    print("\nFeature day_of_year added")
    print("\n*************************************")
    print("\n*******FUNCTION END**********")

    return df

def encode_categorical(df):
    """Encode categorical variables"""
    print("\n*************************************")
    print("\n*******FUNCTION encode_categorical START**********")
    # One-hot encode soil type
    encoder = OneHotEncoder(sparse_output=False)
    print("\nHot encoder added")
    soil_encoded = encoder.fit_transform(df[['soil_type']])
    print("\nSoil encoded")
    soil_cols = [f'soil_{cat}' for cat in encoder.categories_[0]]
    df[soil_cols] = soil_encoded
    print("\nSoil col added")

    # Cyclical encoding for month
    df['month_sin'] = np.sin(2 * np.pi * df['month']/12)
    df['month_cos'] = np.cos(2 * np.pi * df['month']/12)
    print("\nMonth cosine and sine col added")

    print("\n*************************************")
    print("\n*******FUNCTION END**********")

    return df.drop(columns=['soil_type', 'month'])

def create_targets(df):
    """Create prediction targets with temporal offset"""
    print("\n*************************************")
    print("\n*******FUNCTION create_targets START**********")

    # Flood: predict 7 days ahead
    df['flood_target'] = df.groupby('location')['flood_severity'].shift(-7)
    print("\nFlood target added")

    # Drought: predict 30 days ahead
    df['drought_target'] = df.groupby('location')['drought_severity'].shift(-30)
    print("\nDrought target added")

    # Remove rows with missing targets
    df = df.dropna(subset=['flood_target', 'drought_target'])
    print("\nSubset done")

    print("\n*************************************")
    print("\n*******FUNCTION END**********")

    return df

def preprocess_pipeline(weather_path, history_path, hydro_path, geo_path):
    """Full preprocessing pipeline"""
    # Load and merge data
    df = load_and_merge_data(weather_path, history_path, hydro_path, geo_path)
    
    # Handle missing values
    df = handle_missing_values(df)
    
    # Create features
    df = create_temporal_features(df)
    df = create_derived_features(df)
    df = encode_categorical(df)
    
    # Create targets
    df = create_targets(df)
    
    # Final cleaning
    # df = df.drop(columns=['event_date'])  # Redundant with date
    
    # Split features and targets
    features = df.drop(columns=['flood_target', 'drought_target'])
    targets = df[['flood_target', 'drought_target']]
    
    return features, targets

def temporal_train_test_split(features, targets, test_size=0.2):
    """Time-aware data splitting"""
    print("\n*************************************")
    print("\n*******FUNCTION temporal_train_test_split START**********")
    
    # Sort by date
    sorted_idx = features['date'].argsort()
    print("\nSorted IDx")

    features = features.iloc[sorted_idx]
    print("\nFeature iLOC IDx")

    targets = targets.iloc[sorted_idx]
    print("\nTarget iLOC IDx")
    
    # Split index
    split_idx = int(len(features) * (1 - test_size))
    print("\nIndex splited")
    
    X_train = features.iloc[:split_idx]
    print("\nX_train iLOC")

    X_test = features.iloc[split_idx:]
    print("\nX_test iLOC")

    y_train = targets.iloc[:split_idx]
    print("\nY_train iLOC")

    y_test = targets.iloc[split_idx:]
    print("\nY_test iLOC")
    
    print("\n*************************************")
    print("\n*******FUNCTION END**********")
    return X_train, X_test, y_train, y_test

# Example usage
if __name__ == "__main__":
    # Input paths
    data_paths = {
        'weather_path': 'scripts/weather_data.csv',
        'history_path': 'scripts/history_data.csv',
        'hydro_path': 'scripts/hydro_data.csv',
        'geo_path': 'scripts/geo.csv'
    }
    
    # Run preprocessing
    features, targets = preprocess_pipeline(**data_paths)
    
    # Split data
    X_train, X_test, y_train, y_test = temporal_train_test_split(features, targets)
    
    print("\nPreaparing data saving")
    # Save processed data
    # X_train.to_parquet('train_features.parquet',  engine='pyarrow')
    # Fallback option (less efficient)
    X_train.to_csv('train_features.csv', index=False)

    print("\nTrain Feature saved")
    # X_test.to_parquet('test_features.parquet',  engine='pyarrow')
    X_test.to_csv('test_features.csv', index=False)
    print("\nTest Feature saved")

    # y_train.to_parquet('train_targets.parquet',  engine='pyarrow')
    y_train.to_csv('train_targets.csv', index=False)
    print("\nTrain target saved")

    # y_test.to_parquet('test_targets.parquet',  engine='pyarrow')
    y_test.to_csv('test_targets.csv', index=False)
    print("\nTest Target saved")
    
    print(f"Training data shape: {X_train.shape}")
    print(f"Test data shape: {X_test.shape}")