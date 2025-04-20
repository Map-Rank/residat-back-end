import pandas as pd
import numpy as np
from sklearn.preprocessing import OneHotEncoder, StandardScaler
from sklearn.model_selection import TimeSeriesSplit
import warnings

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
    merged = pd.merge(merged, history, on=['date'],how='left')
    merged = pd.merge(merged, geo, on='location', how='left')
    
    return merged.sort_values(['location', 'date'])

def preprocess_reservoir_data(df):
    """Clean and prepare reservoir-focused dataset"""
    # Forward-fill reservoir levels (assuming continuous monitoring)
    df['reservoir_level'] = df.groupby('location')['reservoir_level'].ffill()
    
    # Handle missing values
    df['precipitation'] = df['precipitation'].fillna(0)
    df['temp_avg'] = (df['temp_max'] + df['temp_min']) / 2
    df = df.dropna(subset=['reservoir_level', 'river_flow', 'groundwater_level'])
    
    return df

def create_reservoir_features(df, lookback=90):
    """Generate features for reservoir level prediction"""
    # Temporal features
    df['day_of_year'] = df['date'].dt.dayofyear
    df['month'] = df['date'].dt.month
    
    # Rolling hydrological features
    for window in [7, 30, lookback]:
        df[f'reservoir_{window}d_mean'] = df.groupby('location')['reservoir_level'].transform(
            lambda x: x.rolling(window).mean())
        df[f'precip_{window}d_sum'] = df.groupby('location')['precipitation'].transform(
            lambda x: x.rolling(window).sum())
    
    # Rate of change features
    df['reservoir_7d_change'] = df.groupby('location')['reservoir_level'].pct_change(7)
    df['flow_3d_avg'] = df.groupby('location')['river_flow'].transform(
        lambda x: x.rolling(3).mean())
    
    # Water balance features
    df['evapotranspiration'] = 0.0023 * (df['temp_avg'] + 17.8) * (df['temp_max'] - df['temp_min'])**0.5
    df['water_balance'] = df['precip_30d_sum'] - df['evapotranspiration']
    
    return df

def create_reservoir_targets(df, forecast_horizons=[7, 14, 30]):
    """Create multiple reservoir level prediction targets"""
    for days in forecast_horizons:
        # Future reservoir level (absolute)
        df[f'reservoir_{days}d'] = df.groupby('location')['reservoir_level'].shift(-days)
        
        # Future reservoir change (percentage)
        df[f'reservoir_change_{days}d'] = df.groupby('location')['reservoir_level'].pct_change(days).shift(-days)
    
    # Drop rows where targets couldn't be created
    return df.dropna(subset=[f'reservoir_{d}d' for d in forecast_horizons])

def encode_features(df):
    """Encode categorical features for reservoir prediction"""
    # Cyclical encoding for temporal features
    df['month_sin'] = np.sin(2 * np.pi * df['month']/12)
    df['month_cos'] = np.cos(2 * np.pi * df['month']/12)
    
    # One-hot encode soil type if exists
    if 'soil_type' in df.columns:
        encoder = OneHotEncoder(sparse_output=False)
        soil_encoded = encoder.fit_transform(df[['soil_type']])
        soil_cols = [f'soil_{cat}' for cat in encoder.categories_[0]]
        df[soil_cols] = soil_encoded
    
    return df.drop(columns=['month', 'soil_type'], errors='ignore')

def prepare_reservoir_dataset(data_paths):
    """Complete pipeline for reservoir level prediction"""
    # Load and merge
    df = load_and_merge_data(**data_paths)
    
    # Preprocess
    df = preprocess_reservoir_data(df)
    df = create_reservoir_features(df)
    df = create_reservoir_targets(df)
    df = encode_features(df)
    
    # Split features and targets
    target_cols = [c for c in df.columns if c.startswith('reservoir_') and ('d' in c)]
    features = df.drop(columns=target_cols + ['date'])
    targets = df[target_cols]
    
    return features, targets

def time_series_split(features, targets, n_splits=3):
    """Time-based cross validation split"""
    tscv = TimeSeriesSplit(n_splits=n_splits)
    for train_index, test_index in tscv.split(features):
        X_train, X_test = features.iloc[train_index], features.iloc[test_index]
        y_train, y_test = targets.iloc[train_index], targets.iloc[test_index]
        yield X_train, X_test, y_train, y_test

if __name__ == "__main__":
    data_paths = {
        'weather_path': 'CSVs/weather_data.csv',
        'hydro_path': 'CSVs/hydro_data.csv',
        'geo_path': 'CSVs/geo.csv',
        'history_path': 'CSVs/history_data.csv'
    }
    
    # try:
    features, targets = prepare_reservoir_dataset(data_paths)
    
    # Example usage of time series split
    for fold, (X_train, X_test, y_train, y_test) in enumerate(time_series_split(features, targets)):
        print(f"\nFold {fold + 1}:")
        print(f"Train: {X_train.shape}, {y_train.shape}")
        print(f"Test: {X_test.shape}, {y_test.shape}")
        
        # Here you would train and evaluate your model
        # model.fit(X_train, y_train)
        # score = model.score(X_test, y_test)
            
    # except Exception as e:
    #     warnings.warn(f"Pipeline failed: {str(e)}")