import pandas as pd
import numpy as np
import tensorflow as tf
from tensorflow.keras.models import Model, load_model
from tensorflow.keras.layers import Input, LSTM, Dense, Concatenate
from tensorflow.keras.optimizers import Adam
from tensorflow.keras.callbacks import EarlyStopping
from tensorflow.keras.metrics import AUC
from tensorflow.keras.losses import BinaryCrossentropy
from sklearn.metrics import classification_report, PrecisionRecallDisplay
import matplotlib.pyplot as plt
import joblib


# Define custom loss function
def weighted_loss(flood_weights, drought_weights):
    bce = BinaryCrossentropy(reduction='none')
    
    def loss(y_true, y_pred):
        # Separate flood and drought targets/predictions
        y_true_flood, y_true_drought = y_true[0], y_true[1]
        y_pred_flood, y_pred_drought = y_pred[0], y_pred[1]
        
        # Calculate losses
        flood_loss = bce(y_true_flood, y_pred_flood)
        drought_loss = bce(y_true_drought, y_pred_drought)
        
        # Apply class weights
        flood_weights_tensor = tf.gather(list(flood_weights.values()), tf.cast(y_true_flood, tf.int32))
        drought_weights_tensor = tf.gather(list(drought_weights.values()), tf.cast(y_true_drought, tf.int32))
        
        # Debugging: Print loss values
        tf.print("Flood loss:", tf.reduce_mean(flood_loss))
        tf.print("Drought loss:", tf.reduce_mean(drought_loss))
        
        return tf.reduce_mean(flood_weights_tensor * flood_loss) + \
               tf.reduce_mean(drought_weights_tensor * drought_loss)
    
    return loss

# 1. Load Preprocessed Data
def load_data():
    # X_train = pd.read_parquet('train_features.parquet')
    # X_test = pd.read_parquet('test_features.parquet')
    # y_train = pd.read_parquet('train_targets.parquet')
    # y_test = pd.read_parquet('test_targets.parquet')

    X_train = pd.read_csv('train_features.csv')
    X_test = pd.read_csv('test_features.csv')
    y_train = pd.read_csv('train_targets.csv')
    y_test = pd.read_csv('test_targets.csv')
    print("Files loaded")
    return X_train, X_test, y_train, y_test

# 2. Sequence Creation Function
def create_sequences(features_df, targets_df, timesteps=30):
    """Convert DataFrame to LSTM-ready sequences with location grouping"""
    temporal_features = [col for col in features_df.columns 
                       if col not in ['date', 'location', 'soil_type']]
    static_features = ['elevation', 'slope'] + \
                     [col for col in features_df.columns if 'soil_' in col]
    
    print("Temporal and static features variables done")
    X_temporal, X_static, y_flood, y_drought = [], [], [], []
    
    for location in features_df['location'].unique():
        loc_mask = features_df['location'] == location
        loc_features = features_df[loc_mask]
        loc_targets = targets_df[loc_mask]
        
        # Ensure chronological order
        loc_features = loc_features.sort_values('date')
        loc_targets = loc_targets.loc[loc_features.index]
        
        for i in range(timesteps, len(loc_features)):
            # Temporal features (30-day window)
            seq = loc_features.iloc[i-timesteps:i][temporal_features].values
            X_temporal.append(seq)
            
            # Static features (time-invariant)
            static = loc_features.iloc[i][static_features].values
            X_static.append(static)
            
            # Targets
            y_flood.append(loc_targets.iloc[i]['flood_target'])
            y_drought.append(loc_targets.iloc[i]['drought_target'])
    
    print("Loop done")
    return (np.array(X_temporal), 
            np.array(X_static),
            np.array(y_flood),
            np.array(y_drought))

# 3. Model Architecture
def build_model(temporal_shape, static_shape):
    """Hybrid LSTM model with multi-input architecture"""
    print("\n Building model started")
    # Temporal branch
    temporal_input = Input(shape=temporal_shape, name='temporal_input')
    x = LSTM(128, return_sequences=True, dropout=0.2)(temporal_input)
    print("\n 128 dropout ")

    x = LSTM(64, dropout=0.2)(x)
    print("\n 64 dropout")

    # Static branch
    static_input = Input(shape=static_shape, name='static_input')
    y = Dense(32, activation='relu')(static_input)

    print("\n Static input done")
    # Combined
    combined = Concatenate()([x, y])
    
    print("\n X and Y concatenated")

    # Multi-task output
    flood_out = Dense(1, activation='sigmoid', name='flood')(combined)
    drought_out = Dense(1, activation='sigmoid', name='drought')(combined)
    
    print("\n Flood and drought outed")
    
    model = Model(inputs=[temporal_input, static_input], 
                outputs=[flood_out, drought_out])
    

    model.compile(
        optimizer=Adam(learning_rate=0.001),
        loss={'flood': 'binary_crossentropy', 
            'drought': 'binary_crossentropy'},
        metrics={'flood': ['accuracy', AUC(name='prc', curve='PR')],
                'drought': ['accuracy', AUC(name='prc', curve='PR')]},
        loss_weights=[0.7, 0.3]  # Weight flood prediction higher
    )
    print("\n Model compiled")

    return model

# 4. Training Execution
def train_model(X_train_temp, X_train_stat, y_train_flood, y_train_drought,
               X_test_temp, X_test_stat, y_test_flood, y_test_drought):
    """Train model with early stopping"""
    print("\n MODEL TRAINING STARTED")
    model = build_model(
        temporal_shape=X_train_temp.shape[1:],
        static_shape=X_train_stat.shape[1:]
    )
    
    # Calculate class weights
    flood_weights = {0: 1., 1: len(y_train_flood)/sum(y_train_flood)}
    drought_weights = {0: 1., 1: len(y_train_drought)/sum(y_train_drought)}
    
    flood_weights = {0: 1.0, 1: min(flood_weights[1], 10.0)}  # Cap at 10.0
    drought_weights = {0: 1.0, 1: min(drought_weights[1], 10.0)}

    print("\n Weight calculated")
    
    # Explicit conversion (redundant safety check)
    X_train_temp = X_train_temp.astype('float32')
    X_train_stat = X_train_stat.astype('float32')
    y_train_flood = y_train_flood.astype('float32')
    y_train_drought = y_train_drought.astype('float32')

    X_test_temp = X_test_temp.astype('float32')
    X_test_stat = X_test_stat.astype('float32')
    y_test_flood = y_test_flood.astype('float32')
    y_test_drought = y_test_drought.astype('float32')


    # Check for NaNs or Infs
    print("NaNs in X_train_temp:", np.isnan(X_train_temp).any())
    print("Infs in X_train_temp:", np.isinf(X_train_temp).any())
    print("NaNs in X_train_stat:", np.isnan(X_train_stat).any())
    print("Infs in X_train_stat:", np.isinf(X_train_stat).any())

    # Check target values
    print("Unique flood targets:", np.unique(y_train_flood))
    print("Unique drought targets:", np.unique(y_train_drought))

    print("Flood weights:", flood_weights)
    print("Drought weights:", drought_weights)

    print("\nCompile done")
    flood_pred, drought_pred = model([X_train_temp[:1], X_train_stat[:1]])
    print("Flood prediction:", flood_pred.numpy())
    print("Drought prediction:", drought_pred.numpy())

    # Compile model
    model.compile(
        optimizer=Adam(learning_rate=0.0001, clipvalue=1.0),
        loss=weighted_loss(flood_weights, drought_weights),
        metrics={
            'flood': ['accuracy', AUC(name='prc', curve='PR')],
            'drought': ['accuracy', AUC(name='prc', curve='PR')]
        }
    )


    # Prepare targets as a list of arrays
    y_train_targets = [y_train_flood, y_train_drought]
    y_test_targets = [y_test_flood, y_test_drought]

    # Train model
    # Train model
    history = model.fit(
        [X_train_temp, X_train_stat],
        [y_train_flood, y_train_drought],
        validation_data=([X_test_temp, X_test_stat], 
                        [y_test_flood, y_test_drought]),
        epochs=100,
        batch_size=64,
        callbacks=[EarlyStopping(patience=10, monitor='val_flood_prc', mode='max')]
    )
    

    print("\n History gen")

    return model, history

# 5. Evaluation
def evaluate_model(model, X_test_temp, X_test_stat, y_test_flood, y_test_drought):
    """Generate comprehensive evaluation report"""
    print("\n Evaluation started")
    # Predictions
    print("X_test_temp dtype:", X_test_temp.dtype)
    print("X_test_stat dtype:", X_test_stat.dtype)

    X_test_temp = X_test_temp.astype('float32')  # or 'int32'
    X_test_stat = X_test_stat.astype('float32')

    # Replace NaN with 0 or a suitable value
    X_test_temp = np.nan_to_num(X_test_temp)
    X_test_stat = np.nan_to_num(X_test_stat)
    
    print("X_test_temp dtype:", X_test_temp.dtype)
    print("X_test_stat dtype:", X_test_stat.dtype)

    flood_pred, drought_pred = model.predict([X_test_temp, X_test_stat])
    
    # Classification reports
    flood_report = classification_report(y_test_flood, flood_pred > 0.5,
                                        target_names=['No Flood', 'Flood'])
    drought_report = classification_report(y_test_drought, drought_pred > 0.4,
                                          target_names=['No Drought', 'Drought'])
    
    # PR curves
    fig, (ax1, ax2) = plt.subplots(1, 2, figsize=(15, 6))
    PrecisionRecallDisplay.from_predictions(y_test_flood, flood_pred, ax=ax1)
    ax1.set_title('Flood Precision-Recall Curve')
    PrecisionRecallDisplay.from_predictions(y_test_drought, drought_pred, ax=ax2)
    ax2.set_title('Drought Precision-Recall Curve')
    plt.savefig('pr_curves.png')
    
    return {'flood_report': flood_report, 'drought_report': drought_report}

# 6. Full Pipeline Execution
if __name__ == "__main__":
    # Load and prepare data
    X_train, X_test, y_train, y_test = load_data()
    
    # Create sequences
    X_train_temp, X_train_stat, y_train_flood, y_train_drought = create_sequences(X_train, y_train)
    X_test_temp, X_test_stat, y_test_flood, y_test_drought = create_sequences(X_test, y_test)
    
    # Verify shapes
    print(f"Training Temporal: {X_train_temp.shape}, Static: {X_train_stat.shape}")
    print(f"Testing Temporal: {X_test_temp.shape}, Static: {X_test_stat.shape}")
    
    # Train model
    model, history = train_model(X_train_temp, X_train_stat, y_train_flood, y_train_drought,
                                X_test_temp, X_test_stat, y_test_flood, y_test_drought)
    
    # Evaluate
    report = evaluate_model(model, X_test_temp, X_test_stat, y_test_flood, y_test_drought)
    print("Flood Evaluation:\n", report['flood_report'])
    print("\nDrought Evaluation:\n", report['drought_report'])
    
    # Save model and artifacts
    model.save('flood_drought_model.h5')
    model.save("flood_drought_model_tf/1", save_format="tf") 
    model.save("flood_drought_model") 
    joblib.dump({
        'temporal_features': X_train_temp.shape[1:],
        'static_features': X_train_stat.shape[1:]
    }, 'model_config.pkl')

# 7. Inference Example
def predict_new_data(location_data, model_path='flood_drought_model.h5'):
    """Sample usage:
    location_data = {
        'temporal': np.array([[...]]),  # Shape (30, n_temporal_features)
        'static': np.array([...])       # Shape (n_static_features,)
    }
    """
    # Load model and config
    model = load_model(model_path)
    config = joblib.load('model_config.pkl')
    
    # Verify input shapes
    assert location_data['temporal'].shape[1:] == config['temporal_features'], \
        f"Temporal features mismatch. Expected {config['temporal_features']}"
    assert location_data['static'].shape == (config['static_features'][0],), \
        f"Static features mismatch. Expected {config['static_features']}"
    
    # Add batch dimension
    temporal = np.expand_dims(location_data['temporal'], axis=0)
    static = np.expand_dims(location_data['static'], axis=0)
    
    # Predict
    flood_prob, drought_prob = model.predict([temporal, static])
    
    return {
        'flood_risk_7d': float(flood_prob[0][0]),
        'drought_risk_30d': float(drought_prob[0][0])
    }

# 8. Date prediction example
def predict_for_date(target_date, forecast_data, static_features):
    """
    Predict flood/drought risk for a specific date
    using climate forecasts.
    
    Args:
        target_date (str): Date in 'YYYY-MM-DD' format
        forecast_data (pd.DataFrame): Climate forecasts with columns:
            - date
            - precipitation
            - temperature
            - humidity
            - soil_moisture
            - vegetation_index
        static_features (dict): Elevation, slope, soil type
    
    Returns:
        dict: Prediction results with confidence
    """
    # Create 30-day window ending day before target date
    end_date = pd.to_datetime(target_date) - pd.Timedelta(days=1)
    start_date = end_date - pd.Timedelta(days=29)
    
    # Get forecasted sequence
    sequence = forecast_data[
        (forecast_data['date'] >= start_date) &
        (forecast_data['date'] <= end_date)
    ].sort_values('date')
    
    # Prepare input format
    input_data = {
        'temporal': sequence[['precip', 'temp', 'humid', 'soil_m', 'vegetation']].values.astype('float32'),
        'static': np.array([
            static_features['elevation'],
            static_features['slope'],
            *static_features['soil_type']
        ], dtype='float32')
    }
    
    # Make prediction
    flood_prob, drought_prob = model.predict([
        np.expand_dims(input_data['temporal'], axis=0),
        np.expand_dims(input_data['static'], axis=0)
    ])
    
    return {
        'target_date': target_date,
        'flood_risk': float(flood_prob[0][0]),
        'drought_risk': float(drought_prob[0][0]),
        'validity_window': {
            'flood': f"{(end_date + pd.Timedelta(days=1)).strftime('%Y-%m-%d')} to {end_date + pd.Timedelta(days=7)}",
            'drought': f"{(end_date + pd.Timedelta(days=1)).strftime('%Y-%m-%d')} to {end_date + pd.Timedelta(days=30)}"
        }
    }
