import pandas as pd
import numpy as np
import tensorflow as tf
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import MinMaxScaler

def train_time_series_model(data_path, target_column, model_save_path):
    """
    Trains a time series model using LSTM to predict water risk.

    Args:
        data_path (str): Path to the feature-engineered dataset.
        target_column (str): The name of the target column (e.g., 'water_risk_percentage').
        model_save_path (str): Path to save the trained LSTM model.

    Returns:
        tensorflow.keras.Model: The trained LSTM model.
    """
    # Load the engineered dataset
    data = pd.read_csv(data_path)
    print("Dataset loaded successfully.")

    # Ensure date is sorted for time series
    data.sort_values(by=['location', 'date'], inplace=True)

    # Define features and target
    features = [col for col in data.columns if col not in ['date', target_column, 'location']]
    X = data[features].values
    y = data[target_column].values

    # Normalize the target variable (optional but recommended)
    scaler_y = MinMaxScaler()
    y = scaler_y.fit_transform(y.reshape(-1, 1))

    # Split the data into train and test sets
    print("Splitting data into train and test sets...")
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42, shuffle=False)

    # Reshape data for LSTM input (samples, timesteps, features)
    time_steps = 7  # Number of days (or timesteps) to consider for prediction
    def reshape_to_lstm_format(data, target, time_steps):
        X_lstm, y_lstm = [], []
        for i in range(time_steps, len(data)):
            X_lstm.append(data[i-time_steps:i])
            y_lstm.append(target[i])
        return np.array(X_lstm), np.array(y_lstm)

    X_train, y_train = reshape_to_lstm_format(X_train, y_train, time_steps)
    X_test, y_test = reshape_to_lstm_format(X_test, y_test, time_steps)

    print(f"Training data shape: {X_train.shape}, {y_train.shape}")
    print(f"Testing data shape: {X_test.shape}, {y_test.shape}")

    # Build the LSTM model
    print("Building LSTM model...")
    model = tf.keras.Sequential([
        tf.keras.layers.LSTM(64, activation='relu', return_sequences=True, input_shape=(time_steps, X_train.shape[2])),
        tf.keras.layers.LSTM(32, activation='relu'),
        tf.keras.layers.Dense(1)  # Output layer (single value prediction)
    ])
    model.compile(optimizer='adam', loss='mse', metrics=['mae'])

    # Train the model
    print("Training the model...")
    history = model.fit(
        X_train, y_train,
        validation_data=(X_test, y_test),
        epochs=50,
        batch_size=32,
        verbose=1
    )

    # Save the model
    model.save(model_save_path)
    print(f"Model saved at {model_save_path}")

    # Evaluate the model
    print("Evaluating the model...")
    loss, mae = model.evaluate(X_test, y_test)
    print(f"Test Loss: {loss:.4f}, Test MAE: {mae:.4f}")

    return model, scaler_y

# Example Usage
# data_path = "engineered_data.csv"
# target_column = "water_risk_percentage"
# model_save_path = "water_risk_model.h5"

# model, scaler_y = train_time_series_model(data_path, target_column, model_save_path)
if __name__ == "__main__":
    import sys
    if len(sys.argv) != 4:
        print("Usage: python model.py <input_file_path> <output_file_path>")
        sys.exit(1)

    engineer_path = sys.argv[1]
    target_column = sys.argv[2]
    output_path = sys.argv[3]

    try:
        train_time_series_model(engineer_path, target_column, output_path)
        print("Model generated completed successfully!")
    except Exception as e:
        print(f"Error: {e}")