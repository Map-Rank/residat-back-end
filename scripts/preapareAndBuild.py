import pandas as pd
import numpy as np
from sklearn.preprocessing import OneHotEncoder, StandardScaler, RobustScaler
from sklearn.model_selection import TimeSeriesSplit
import warnings
import tensorflow as tf
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Dense, Dropout, LSTM, BatchNormalization, TimeDistributed
from tensorflow.keras.callbacks import EarlyStopping, ModelCheckpoint
import os

# Set TensorFlow to not use all GPU memory and grow memory allocation as needed
physical_devices = tf.config.list_physical_devices('GPU')
if physical_devices:
    try:
        for device in physical_devices:
            tf.config.experimental.set_memory_growth(device, True)
    except:
        print("Memory growth configuration failed")

# Add a global seed for reproducibility
RANDOM_SEED = 42
np.random.seed(RANDOM_SEED)
tf.random.set_seed(RANDOM_SEED)

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

def create_reservoir_targets(df, forecast_horizons=[ 7, 14, 30]):
    """Create multiple reservoir level prediction targets"""
    before_rows = df.shape[0]
    
    for days in forecast_horizons:
        # Future reservoir level (absolute)
        df[f'reservoir_{days}d'] = df.groupby('location')['reservoir_level'].shift(-days)
        
        # Future reservoir change (percentage)
        df[f'reservoir_change_{days}d'] = df.groupby('location')['reservoir_level'].pct_change(periods=days).shift(-days)
        # Replace inf values with NaN
        df[f'reservoir_change_{days}d'] = df[f'reservoir_change_{days}d'].replace([np.inf, -np.inf], np.nan)
    
    # Check target columns for any issues
    target_cols = [f'reservoir_{days}d' for days in forecast_horizons] + [f'reservoir_change_{days}d' for days in forecast_horizons]
    missing_targets = df[target_cols].isna().sum()
    print("Missing values in target columns:")
    print(missing_targets)
    
    # Drop rows where targets couldn't be created
    df_clean = df.dropna(subset=target_cols)
    after_rows = df_clean.shape[0]
    
    print(f"Dropped {before_rows - after_rows} rows due to missing target values (expected for last {max(forecast_horizons)} days per location)")
    
    return df_clean

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
        # Using handle_unknown='ignore' to avoid errors with new categories
        encoder = OneHotEncoder(sparse_output=False, handle_unknown='ignore')
        encoded_data = encoder.fit_transform(df[categorical_cols])
        
        # Create meaningful column names
        encoded_cols = []
        for i, col in enumerate(categorical_cols):
            cats = encoder.categories_[i]
            cols = [f'{col}_{cat}' for cat in cats]
            encoded_cols.extend(cols)
        
        df_encoded = pd.DataFrame(encoded_data, columns=encoded_cols, index=df.index)
        df = pd.concat([df, df_encoded], axis=1)
    
    # Handle location column specially to avoid duplicates later
    # We'll convert it to one-hot encoding but NOT keep the original column
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
    df = create_reservoir_targets(df)
    df = encode_features(df)
    print(f"Encoded columns: {df.columns.tolist()}")
    
    # Split features and targets
    target_cols = [c for c in df.columns if c.startswith('reservoir_') and (any(f'_{d}d' in c for d in [7, 14, 30]))]
    drop_cols = target_cols + ['date']
    
    # Important: If location is used as a feature AND in metadata, we need to prevent duplication later
    # So we'll drop it from features if it has been one-hot encoded
    location_encoded = any(col.startswith('location_') for col in df.columns)
    if location_encoded and 'location' in df.columns:
        drop_cols.append('location')
        print("Dropping 'location' from features as it's already one-hot encoded")
    
    # For features, exclude target columns and metadata
    features = df.drop(columns=drop_cols, errors='ignore')
    targets = df[target_cols]

    # Get the final feature names (excluding targets and metadata)
    feature_names = [col for col in features.columns]
    print("Features used in training:")
    print(feature_names)
    
    # Create metadata dataframe with location and date
    metadata_df = metadata.copy().reset_index(drop=True)
    
    return features, targets, metadata_df

def prepare_lstm_data(features, targets, metadata, time_steps=30, val_split=0.2):
    """Prepare data in the format required for LSTM (sequences) with proper validation split"""
    # Ensure all dataframes have the same index
    features = features.reset_index(drop=True)
    targets = targets.reset_index(drop=True)
    metadata = metadata.reset_index(drop=True)
    
    # If 'location' exists in both features and metadata, rename it in one of them
    if 'location' in features.columns and 'location' in metadata.columns:
        print("Found duplicate 'location' column. Renaming in features dataframe.")
        features = features.rename(columns={'location': 'location_feature'})
    
    # Merge for processing
    data = pd.concat([metadata, features, targets], axis=1)
    
    # Remove any remaining NaN values
    data = data.dropna()
    
    # Sort by location and date for proper time series split
    if 'date' in data.columns and 'location' in data.columns:
        data = data.sort_values(['location', 'date'])
    
    # Group by location to maintain sequence integrity
    unique_locations = data['location'].unique()
    
    X_train_seqs = []
    y_train_seqs = []
    X_val_seqs = []
    y_val_seqs = []
    
    feature_cols = features.columns
    target_cols = targets.columns
    
    # Process each location separately
    for loc in unique_locations:
        # Get data for this location
        loc_data = data[data['location'] == loc].copy()
        
        # We need enough data to form at least one sequence
        if len(loc_data) <= time_steps:
            print(f"Location {loc} has too few data points ({len(loc_data)}) - skipping")
            continue
            
        # Extract features and targets
        loc_features = loc_data[feature_cols].copy()
        loc_targets = loc_data[target_cols].copy()
        
        # Drop non-numeric columns
        for col in loc_features.columns:
            if not pd.api.types.is_numeric_dtype(loc_features[col]):
                print(f"Dropping non-numeric column: {col}")
                loc_features = loc_features.drop(columns=[col])
        
        # Scale features for this location
        scaler = RobustScaler()  # Using RobustScaler to handle outliers better
        loc_features_scaled = scaler.fit_transform(loc_features)
        
        # Determine split point - time-based split
        split_idx = int(len(loc_features_scaled) * (1 - val_split))
        
        # Create training sequences
        for i in range(len(loc_features_scaled[:split_idx]) - time_steps):
            X_train_seqs.append(loc_features_scaled[i:(i + time_steps)])
            y_train_seqs.append(loc_targets.iloc[i + time_steps].values)
            
        # Create validation sequences
        for i in range(split_idx - time_steps, len(loc_features_scaled) - time_steps):
            X_val_seqs.append(loc_features_scaled[i:(i + time_steps)])
            y_val_seqs.append(loc_targets.iloc[i + time_steps].values)
    
    # Convert to numpy arrays
    if not X_train_seqs or not X_val_seqs:
        raise ValueError("No sequences could be created. Check if there are enough data points per location.")
    
    X_train = np.array(X_train_seqs)
    y_train = np.array(y_train_seqs)
    X_val = np.array(X_val_seqs)
    y_val = np.array(y_val_seqs)
    
    print(f"Training sequences: {X_train.shape}, Training targets: {y_train.shape}")
    print(f"Validation sequences: {X_val.shape}, Validation targets: {y_val.shape}")
    
    # Check for any NaN or inf values
    print(f"Training data contains NaN: {np.isnan(X_train).any()}")
    print(f"Training targets contain NaN: {np.isnan(y_train).any()}")
    print(f"Validation data contains NaN: {np.isnan(X_val).any()}")
    print(f"Validation targets contain NaN: {np.isnan(y_val).any()}")
    
    # Replace any remaining NaN or inf values (this shouldn't happen if preprocessing is correct)
    X_train = np.nan_to_num(X_train)
    y_train = np.nan_to_num(y_train)
    X_val = np.nan_to_num(X_val)
    y_val = np.nan_to_num(y_val)
    
    return X_train, y_train, X_val, y_val

def build_reservoir_model(input_shape, output_dim):
    """Create an LSTM network for reservoir level prediction"""
    model = Sequential([
        # LSTM layers with regularization
        LSTM(64, return_sequences=True, input_shape=input_shape, 
             kernel_regularizer=tf.keras.regularizers.l2(0.001)),
        BatchNormalization(),
        Dropout(0.3),
        
        LSTM(32, return_sequences=False,
             kernel_regularizer=tf.keras.regularizers.l2(0.001)),
        BatchNormalization(),
        Dropout(0.3),
        
        # Dense layers
        Dense(32, activation='relu', kernel_regularizer=tf.keras.regularizers.l2(0.001)),
        BatchNormalization(),
        Dense(output_dim)
    ])
    
    model.compile(
        optimizer=tf.keras.optimizers.Adam(learning_rate=0.001),
        loss='mean_squared_error',
        metrics=['mae']
    )
    
    print(model.summary())
    return model

def train_reservoir_model(features, targets, metadata, time_steps=30):
    """Train and save a reservoir prediction LSTM model"""
    try:
        # Clean and prepare data for LSTM
        X_train, y_train, X_val, y_val = prepare_lstm_data(
            features, targets, metadata, time_steps=time_steps
        )
        
        # Check data shapes
        print(f"X_train shape: {X_train.shape}")
        print(f"y_train shape: {y_train.shape}")
        print(f"X_val shape: {X_val.shape}")
        print(f"y_val shape: {y_val.shape}")
        
        # Build model
        input_shape = (X_train.shape[1], X_train.shape[2])  # (time_steps, features)
        output_dim = y_train.shape[1]  # Number of target variables
        
        model = build_reservoir_model(input_shape, output_dim)
        
        # Set up callbacks with proper early stopping
        callbacks = [
            EarlyStopping(
                monitor='val_loss',
                patience=15,
                restore_best_weights=True,
                verbose=1
            ),
            ModelCheckpoint(
                'model.h5',
                monitor='val_loss',
                save_best_only=True,
                verbose=1
            )
        ]
        
        # Train model with reduced batch size
        history = model.fit(
            X_train, y_train,
            validation_data=(X_val, y_val),
            epochs=100,
            batch_size=16,  # Smaller batch size for better learning
            callbacks=callbacks,
            verbose=1
        )
        
        # Evaluate model
        eval_results = model.evaluate(X_val, y_val, verbose=1)
        print("Test Loss:", eval_results[0])
        print("Test MAE:", eval_results[1])
        
        return model, history
        
    except Exception as e:
        print(f"Error in model training: {str(e)}")
        raise

if __name__ == "__main__":
    # Set paths to your data files
    data_paths = {
        'weather_path': 'CSVs/weather_data.csv',
        'hydro_path': 'CSVs/hydro_data.csv',
        'geo_path': 'CSVs/geo.csv',
        'history_path': 'CSVs/history_data.csv'
    }
    
    # LSTM parameters
    TIME_STEPS = 30  # Number of time steps to consider for each prediction
    
    try:
        # Prepare datasets
        print("Preparing dataset...")
        features, targets, metadata = prepare_reservoir_dataset(data_paths)
        
        print(f"Dataset prepared: {features.shape[0]} samples with {features.shape[1]} features")
        print(f"Target columns: {targets.columns.tolist()}")
        
        # Train LSTM model and save as model.h5
        print(f"Training LSTM model with {TIME_STEPS} time steps...")
        model, history = train_reservoir_model(features, targets, metadata, time_steps=TIME_STEPS)

        # Save model architecture as JSON
        try:
            print("Training completed. Saving model...")
            model.save('model.h5', save_format='tf')
            print("Model saved successfully to model.h5")
        except Exception as e:
            print(f"Error saving model: {str(e)}")
        
        model_json = model.to_json()
        with open("model_architecture.json", "w") as json_file:
            json_file.write(model_json)
        
        # Save training history
        hist_df = pd.DataFrame(history.history)
        hist_df.to_csv('training_history.csv')
        
        print(f"LSTM Model saved to 'model.h5'")
        print(f"Model architecture saved to 'model_architecture.json'")
        print(f"Training history saved to 'training_history.csv'")
        
        # Plot training & validation loss values
        try:
            import matplotlib.pyplot as plt
            plt.figure(figsize=(12, 6))
            plt.subplot(1, 2, 1)
            plt.plot(history.history['loss'])
            plt.plot(history.history['val_loss'])
            plt.title('Model Loss')
            plt.ylabel('Loss')
            plt.xlabel('Epoch')
            plt.legend(['Train', 'Validation'], loc='upper right')
            
            plt.subplot(1, 2, 2)
            plt.plot(history.history['mae'])
            plt.plot(history.history['val_mae'])
            plt.title('Model MAE')
            plt.ylabel('MAE')
            plt.xlabel('Epoch')
            plt.legend(['Train', 'Validation'], loc='upper right')
            
            plt.tight_layout()
            plt.savefig('training_history.png')
            print("Training history plot saved to 'training_history.png'")
        except Exception as plot_error:
            print(f"Could not generate plot: {str(plot_error)}")
        
    except Exception as e:
        warnings.warn(f"Pipeline failed: {str(e)}")
        raise