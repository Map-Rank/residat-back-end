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


# 8. Date prediction example
def predict_for_date(target_date, forecast_data, static_features):
    # Create 30-day window ending day before target date
    end_date = pd.to_datetime(target_date) - pd.Timedelta(days=1)
    start_date = end_date - pd.Timedelta(days=29)
    
    # Get forecasted sequence as a DataFrame
    sequence = forecast_data[
        (forecast_data['date'] >= start_date) &
        (forecast_data['date'] <= end_date)
    ].sort_values('date')
    
    # Convert to NumPy array
    sequence = sequence[['precip', 'temp', 'humid', 'soil_m', 'vegetation']].values
    
    # Ensure sequence has 30 time steps
    if len(sequence) < 30:
        padding = np.zeros((30 - len(sequence), sequence.shape[1]))
        sequence = np.vstack([sequence, padding])
    
    # Ensure sequence has 26 features
    num_missing_features = 26 - sequence.shape[1]
    if num_missing_features > 0:
        placeholder = np.zeros((sequence.shape[0], num_missing_features))
        sequence = np.hstack([sequence, placeholder])
    
    # Prepare static input
    static_input = np.array([
        static_features['elevation'],
        static_features['slope'],
        *static_features['soil_type'][:3]  # Ensure only 3 soil_type features
    ], dtype='float32')
    
    # Prepare input format
    input_data = {
        'temporal': sequence.astype('float32'),
        'static': static_input
    }
    
    # Load model without compiling
    model_path = 'flood_drought_model.h5'
    model = load_model(model_path, compile=False)
    
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


# 6. Full Pipeline Execution
if __name__ == "__main__":
    # Sample climate forecast data
    # Corrected code with consistent 40-day forecast
    forecast_df = pd.DataFrame({
        'date': pd.date_range(start='2024-03-01', periods=40),  # 40 days
        'precip': np.random.uniform(0, 20, 40),
        'temp': np.random.uniform(15, 35, 40),
        'humid': np.random.uniform(30, 80, 40),
        'soil_m': np.random.uniform(50, 120, 40),
        'vegetation': np.random.uniform(0.3, 0.7, 40)
    })

    print(forecast_df)
    # Static features for a location
    static_features = {
        'elevation': 245.6,
        'slope': 3.8,
        'soil_type': [0, 0, 1, 0, 0]  # Loam soil
    }

    # Make prediction for March 25th, 2024
    prediction = predict_for_date(
        target_date='2024-03-25',
        forecast_data=forecast_df,
        static_features=static_features
    )

    # Print output
    print(prediction)