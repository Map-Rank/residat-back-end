import pandas as pd
import numpy as np
from sklearn.preprocessing import OneHotEncoder, StandardScaler
import tensorflow as tf
from tensorflow.keras.models import Sequential, load_model
from tensorflow.keras.layers import Dense, Dropout, LSTM, BatchNormalization
from tensorflow.keras.callbacks import EarlyStopping, ModelCheckpoint
import joblib
import matplotlib.pyplot as plt
import warnings
import os

# [Previous functions: load_and_merge_data, preprocess_reservoir_data, 
# create_reservoir_features, create_reservoir_targets, encode_features,
# prepare_reservoir_dataset, build_reservoir_model, prepare_lstm_data,
# train_reservoir_model - Keep all these exactly as you had them]

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
    merged = pd.merge(merged, history, on=['date', 'location'], how='left')
    merged = pd.merge(merged, geo, on='location', how='left')
    
    return merged.sort_values(['location', 'date'])

def preprocess_reservoir_data(df):
    """Clean and prepare reservoir-focused dataset"""
    # Forward-fill reservoir levels (assuming continuous monitoring)
    
    df['reservoir_level'] = df.groupby('location')['reservoir_level'].ffill()
    # Handle missing values
    df['precipitation'] = df['precipitation'].fillna(0)
    df['temp_avg'] = (df['temp_max'] + df['temp_min']) / 2
    
    # Fill missing flood and drought indicators with 0 (no event)
    if 'flood' in df.columns:
        df['flood'] = df['flood'].fillna(0)
    if 'drought' in df.columns:
        df['drought'] = df['drought'].fillna(0)
    
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
        df[f'river_flow_{window}d_mean'] = df.groupby('location')['river_flow'].transform(
            lambda x: x.rolling(window).mean())
        df[f'groundwater_{window}d_mean'] = df.groupby('location')['groundwater_level'].transform(
            lambda x: x.rolling(window).mean())
    
    # Rate of change features
    df['reservoir_7d_change'] = df.groupby('location')['reservoir_level'].pct_change(7)
    df['groundwater_7d_change'] = df.groupby('location')['groundwater_level'].pct_change(7)
    df['flow_3d_avg'] = df.groupby('location')['river_flow'].transform(
        lambda x: x.rolling(3).mean())
    
    # Water balance features
    df['evapotranspiration'] = 0.0023 * (df['temp_avg'] + 17.8) * (df['temp_max'] - df['temp_min'])**0.5
    df['water_balance'] = df['precip_30d_sum'] - df['evapotranspiration']
    
    # Watershed and location handling (for better generalization)
    if 'Watershed' in df.columns:
        # Convert watershed to categorical if it's not already
        df['Watershed'] = df['Watershed'].astype('category')
    
    return df

def create_reservoir_targets(df, forecast_horizons=[7, 14, 30]):
    """Create multiple reservoir level prediction targets"""
    for days in forecast_horizons:
        # Future reservoir level (absolute)
        df[f'reservoir_{days}d'] = df.groupby('location')['reservoir_level'].shift(-days)
        
        # Future reservoir change (percentage)
        df[f'reservoir_change_{days}d'] = df.groupby('location')['reservoir_level'].pct_change(days).shift(-days)
    
    # Drop rows where targets couldn't be created
    return df.dropna(subset=[f'reservoir_{days}d' for days in forecast_horizons])

def encode_features(df):
    """Encode categorical features for reservoir prediction"""
    # Cyclical encoding for temporal features
    df['month_sin'] = np.sin(2 * np.pi * df['month']/12)
    df['month_cos'] = np.cos(2 * np.pi * df['month']/12)
    df['day_sin'] = np.sin(2 * np.pi * df['day_of_year']/365)
    df['day_cos'] = np.cos(2 * np.pi * df['day_of_year']/365)
    
    # One-hot encode soil type
    categorical_cols = []
    if 'soil_type' in df.columns:
        categorical_cols.append('soil_type')
    
    if 'Watershed' in df.columns:
        categorical_cols.append('Watershed')
    
    if 'location' in df.columns:
        categorical_cols.append('location')
    
    if categorical_cols:
        encoder = OneHotEncoder(sparse_output=False)
        encoded_data = encoder.fit_transform(df[categorical_cols])
        encoded_cols = []
        for i, col in enumerate(categorical_cols):
            cats = encoder.categories_[i]
            cols = [f'{col}_{cat}' for cat in cats]
            encoded_cols.extend(cols)
        
        df_encoded = pd.DataFrame(encoded_data, columns=encoded_cols, index=df.index)
        df = pd.concat([df, df_encoded], axis=1)
    
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
    
    # Merge metadata back
    df = pd.concat([df, metadata], axis=1)
    
    # Split features and targets
    target_cols = [c for c in df.columns if c.startswith('reservoir_') and (any(f'_{d}d' in c for d in [7, 14, 30]))]
    drop_cols = target_cols + ['date']
    features = df.drop(columns=drop_cols)
    targets = df[target_cols]
    
    return features, targets, df[['date', 'location']]


def predict_reservoir_levels(model, scaler, recent_data, metadata, time_steps=30, forecast_horizons=[7, 14, 30]):
    """
    Make predictions using the trained reservoir level model.
    
    Args:
        model: Trained Keras model
        scaler: Fitted StandardScaler used during training
        recent_data: DataFrame containing recent features (must include at least time_steps days)
        metadata: DataFrame containing date and location information
        time_steps: Number of time steps the model expects (default 30)
        forecast_horizons: List of forecast horizons to predict (default [7,14,30] days)
    
    Returns:
        Dictionary containing:
        - predictions: DataFrame with predictions for each horizon
        - input_sequence: The processed input sequence used for prediction
        - metadata: Dates and locations corresponding to the input sequence
    """
    # Make copies to avoid modifying originals
    df = recent_data.copy()
    meta = metadata.copy()
    
    # Ensure we have enough data
    if len(df) < time_steps:
        raise ValueError(f"Need at least {time_steps} days of data for prediction. Got {len(df)}")
    
    # Get only the most recent time_steps days
    df = df.iloc[-time_steps:]
    meta = meta.iloc[-time_steps:]
    
    # Preprocess the input data to match training format
    df_processed = df.copy()
    
    # Handle location encoding (must match training preprocessing)
    if 'location' in df_processed.columns:
        df_processed['location'] = pd.Categorical(df_processed['location'])
        df_processed['location'] = df_processed['location'].cat.codes
    
    # Drop any non-numeric columns that might remain
    non_numeric = [col for col in df_processed.columns if pd.api.types.is_object_dtype(df_processed[col])]
    if non_numeric:
        print(f"Dropping non-numeric columns: {non_numeric}")
        df_processed = df_processed.drop(columns=non_numeric)
    
    
    print(f"df_processed columns: {df_processed.columns.tolist()}")
    
    # Handle Watershed - ensure one-hot encoding exists
    if 'Watershed' in df_processed.columns:
        if isinstance(df_processed['Watershed'].iloc[0], (int, float)):
            # Create one-hot encoding if Watershed is numeric
            df_processed['Watershed_0'] = (df_processed['Watershed'] == 0).astype(int)
            df_processed['Watershed_1'] = (df_processed['Watershed'] == 1).astype(int)
            df_processed = df_processed.drop(columns=['Watershed'])
        else:
            # Handle categorical Watershed if needed
            pass
    else:
        # Create Watershed one-hot columns if they don't exist
        df_processed['Watershed_0'] = 0
        df_processed['Watershed_1'] = 1  # Or adjust based on your data
    
    
    # Handle location to match training (expecting 'location_Yagoua')
    if 'location_Yagoua' in df_processed.columns:
        if pd.api.types.is_object_dtype(df_processed['location_Yagoua']):
            df_processed['location'] = (df_processed['location_Yagoua'] == 'Yagoua').astype(int)
            df_processed['location_Yagoua'] = pd.Categorical(df_processed['location']).codes
        else:
            # If already encoded, create the Yagoua dummy
            df_processed['location'] = 0  # Adjust based on your actual data
    
    if 'reservoir_30d_mean' in df_processed.columns:
        df_processed = df_processed.drop(columns=[' reservoir_30d_mean'])

    if 'reservoir_7d_mean' in df_processed.columns:
        df_processed = df_processed.drop(columns=[' reservoir_7d_mean'])
    
    if 'reservoir_7d_change' in df_processed.columns:
        df_processed = df_processed.drop(columns=[' reservoir_7d_change'])

    # Scale the features using the training scaler
    features_scaled = scaler.transform(df_processed)
    
    # Reshape into LSTM input format (1 sample, time_steps, n_features)
    input_sequence = features_scaled.reshape(1, time_steps, -1)
    
    # Make prediction
    predictions = model.predict(input_sequence)[0]
    
    # Format predictions into a DataFrame
    results = {}
    for i, days in enumerate(forecast_horizons):
        # Assuming model outputs absolute level first, then percentage change
        results[f'predicted_level_{days}d'] = predictions[i*2]
        results[f'predicted_change_{days}d_pct'] = predictions[i*2+1]
    
    prediction_df = pd.DataFrame(results, index=[meta['date'].iloc[-1]])
    
    return {
        'predictions': prediction_df,
        'input_sequence': input_sequence,
        'metadata': meta
    }

def load_data_for_prediction(data_paths, n_days=30):
    """
    Load and prepare recent data for prediction.
    Returns data in the same format as used for training.
    """
    # Load and preprocess data using the same pipeline as training
    df = load_and_merge_data(**data_paths)
    print(f"Merged columns: {df.columns.tolist()}")
    df = preprocess_reservoir_data(df)
    print(f"Preprocess columns: {df.columns.tolist()}")
    df = create_reservoir_features(df)
    print(f"Reservoir columns: {df.columns.tolist()}")
    print("================================")
    
    # Create metadata before encoding
    metadata = df[['date', 'location']].copy()
    
    # Encode features (don't create targets for prediction)
    df = encode_features(df)
    
    # Get only the most recent n_days
    print(f"Encoded columns: {df.columns.tolist()}")
    recent_data = df.sort_values('date').groupby('location_Yagoua').tail(n_days)
    recent_metadata = metadata.loc[recent_data.index]
    
    # Separate features and metadata
    features = recent_data.drop(columns=['date'])
    
    return features, recent_metadata

def save_prediction_results(results, output_dir="predictions"):
    """Save prediction results to CSV files"""
    if not os.path.exists(output_dir):
        os.makedirs(output_dir)
    
    # Save predictions
    timestamp = pd.Timestamp.now().strftime("%Y%m%d_%H%M%S")
    predictions_file = os.path.join(output_dir, f"predictions_{timestamp}.csv")
    results['predictions'].to_csv(predictions_file)
    
    # Save input sequence metadata
    metadata_file = os.path.join(output_dir, f"input_metadata_{timestamp}.csv")
    results['metadata'].to_csv(metadata_file)
    
    print(f"Predictions saved to {predictions_file}")
    print(f"Input metadata saved to {metadata_file}")

def train_reservoir_model(features, targets, test_size=0.2, time_steps=30):
    """Train and save a reservoir prediction LSTM model"""
    # Make a copy of the features dataframe to avoid modifying the original
    features_copy = features.copy()
    
    # Extract metadata before preprocessing
    metadata = pd.DataFrame()
    if 'location' in features_copy.columns:
        metadata['location'] = features_copy['location'].copy()
    if 'date' in features_copy.columns:
        metadata['date'] = features_copy['date'].copy()
        features_copy = features_copy.drop('date', axis=1)
    
    # Convert location to categorical if needed
    if 'location' in features_copy.columns and pd.api.types.is_object_dtype(features_copy['location']):
        features_copy['location'] = pd.Categorical(features_copy['location'])
        features_copy['location'] = features_copy['location'].cat.codes
    
    # Clean up any remaining non-numeric columns
    for col in features_copy.columns:
        if pd.api.types.is_object_dtype(features_copy[col]):
            print(f"Dropping non-numeric column: {col}")
            features_copy = features_copy.drop(columns=[col])
    
    # Scale all features
    scaler = StandardScaler()
    features_scaled = scaler.fit_transform(features_copy)
    features_scaled_df = pd.DataFrame(features_scaled, columns=features_copy.columns)
    
    # Add back location for grouping if it exists in both metadata and the scaled dataframe
    if 'location' in metadata.columns:
        features_scaled_df['location'] = metadata['location'].values
    
    # Create a time-ordered split (last portion as test)
    total_rows = len(features_scaled_df)
    split_idx = int(total_rows * (1 - test_size))
    
    # Prepare sequences for LSTM
    X_train_seq, y_train_seq = prepare_lstm_data(
        features_scaled_df.iloc[:split_idx], 
        targets.iloc[:split_idx], 
        time_steps
    )
    
    X_test_seq, y_test_seq = prepare_lstm_data(
        features_scaled_df.iloc[split_idx:], 
        targets.iloc[split_idx:], 
        time_steps
    )
    
    print(f"Training sequences shape: {X_train_seq.shape}")
    print(f"Target shape: {y_train_seq.shape}")
    
    # Build and train LSTM model
    n_features = X_train_seq.shape[2]  # Number of features per time step
    model = build_reservoir_model(n_features * time_steps, y_train_seq.shape[1], time_steps)
    
    # Set up callbacks
    callbacks = [
        EarlyStopping(patience=15, restore_best_weights=True),
        ModelCheckpoint('model.h5', save_best_only=True)
    ]
    
    # Train model
    history = model.fit(
        X_train_seq, y_train_seq,
        validation_data=(X_test_seq, y_test_seq),
        epochs=100,
        batch_size=32,
        callbacks=callbacks,
        verbose=1
    )
    
    # Evaluate model
    eval_results = model.evaluate(X_test_seq, y_test_seq)
    print("Test Loss:", eval_results[0])
    print("Test MAE:", eval_results[1])
    
    return model, scaler, history


def visualize_predictions(predictions, history_data=None):
    """Visualize prediction results"""
    plt.figure(figsize=(12, 6))
    
    # Extract prediction values
    horizons = sorted([int(col.split('_')[2][:-1]) for col in predictions.columns if 'level' in col])
    pred_values = [predictions[f'predicted_level_{h}d'].values[0] for h in horizons]
    
    # Plot predictions
    plt.plot(horizons, pred_values, 'o-', label='Predicted Levels', markersize=8)
    
    # If historical data is provided, plot recent trends
    if history_data is not None:
        recent_history = history_data['reservoir_level'].tail(30)
        plt.plot(range(-len(recent_history), 0), recent_history.values, 
                label='Recent History', alpha=0.7)
    
    plt.title('Reservoir Level Predictions')
    plt.xlabel('Days Ahead')
    plt.ylabel('Reservoir Level')
    plt.legend()
    plt.grid(True)
    
    # Save the plot
    plot_path = os.path.join("predictions", f"prediction_plot_{pd.Timestamp.now().strftime('%Y%m%d_%H%M%S')}.png")
    plt.savefig(plot_path)
    plt.close()
    print(f"Prediction plot saved to {plot_path}")

if __name__ == "__main__":
    # Configuration
    data_paths = {
        'weather_path': 'CSVs/new_weather_data.csv',
        'hydro_path': 'CSVs/new_hydro_data.csv',
        'geo_path': 'CSVs/geo_new.csv',
        'history_path': 'CSVs/history_data.csv'
    }
    TIME_STEPS = 30
    
    # Option 1: Train a new model
    train_new_model = False  # Set to True if you want to train a new model
    
    if train_new_model:
        print("Training new model...")
        features, targets, metadata = prepare_reservoir_dataset(data_paths)
        model, scaler, history = train_reservoir_model(features, targets)
        
        # Save model and artifacts
        model.save('model.h5')
        joblib.dump(scaler, 'scaler.joblib')
        print("Model training complete and artifacts saved.")
    
    # Option 2: Load existing model
    else:
        try:
            model = load_model('model.h5')
            scaler = joblib.load('scaler.joblib')
            print("Loaded pre-trained model and scaler")
        except Exception as e:
            raise FileNotFoundError("Model files not found. Set train_new_model=True to train a model") from e

    
    # Make predictions
    print("\nLoading data for prediction...")
    recent_features, recent_metadata = load_data_for_prediction(data_paths, n_days=TIME_STEPS)
    
    print("Making predictions...")
    results = predict_reservoir_levels(
        model=model,
        scaler=scaler,
        recent_data=recent_features,
        metadata=recent_metadata
    )
    
    # Display and save results
    print("\nPrediction Results:")
    print(results['predictions'])
    
    save_prediction_results(results)
    
    # Visualize predictions (optional - requires historical data)
    try:
        history_df = load_and_merge_data(**data_paths)
        visualize_predictions(results['predictions'], history_df)
    except Exception as e:
        print(f"Could not generate prediction plot: {str(e)}")