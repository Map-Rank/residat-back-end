import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import MinMaxScaler
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import LSTM, Dense, Dropout
from tensorflow.keras.optimizers import Adam
from sklearn.preprocessing import LabelEncoder
import joblib
from tensorflow.keras.callbacks import EarlyStopping

def preprocess_data_for_lstm(engineered_data_path):
    """
    Preprocess the feature-engineered dataset for LSTM model.
    Reshape the dataset into a format suitable for LSTM.

    Args:
        engineered_data_path (str): Path to the feature-engineered dataset.

    Returns:
        X_train, X_test, y_train_flood, y_test_flood, y_train_drought, y_test_drought
    """
    # Load the engineered dataset
    data = pd.read_csv(engineered_data_path)
    print("Engineered dataset loaded successfully.")
    
    # Encode categorical features using LabelEncoder
    label_encoder = LabelEncoder()
    
    # Encode 'soil_type' column
    data['soil_type'] = label_encoder.fit_transform(data['soil_type'])
    print("Soil type encoded successfully.")

    # If you have other categorical columns like 'location', 'month', 'day_of_week', etc., encode them as well
    if 'location' in data.columns:
        data['location'] = label_encoder.fit_transform(data['location'])
    if 'day_of_week' in data.columns:
        data['day_of_week'] = label_encoder.fit_transform(data['day_of_week'])

    # Normalize the features (Min-Max Scaling)
    from sklearn.preprocessing import MinMaxScaler
    scaler = MinMaxScaler()
    features = data.drop(columns=['flood', 'drought', 'date'])  # Drop non-feature columns
    features_normalized = scaler.fit_transform(features)
    
    # Separate flood and drought labels
    labels_flood = data['flood']
    labels_drought = data['drought']

    # Reshape the data into a 3D tensor [samples, time steps, features] for LSTM
    timesteps = 30
    X = []
    y_flood = []
    y_drought = []
    
    for i in range(timesteps, len(features_normalized)):
        X.append(features_normalized[i-timesteps:i])  # Last 30 days data for LSTM
        y_flood.append(labels_flood[i])
        y_drought.append(labels_drought[i])

    X = np.array(X)
    y_flood = np.array(y_flood)
    y_drought = np.array(y_drought)

    # # Split data into training and testing sets
    # from sklearn.model_selection import train_test_split
    # X_train, X_test, y_train_flood, y_test_flood = train_test_split(X, y_flood, test_size=0.2, random_state=42)
    # _, _, y_train_drought, y_test_drought = train_test_split(X, y_drought, test_size=0.2, random_state=42)
    from sklearn.model_selection import train_test_split

    # Split data into training and testing sets
    # Adjust test_size to be smaller, or remove the test set if data is small
    if len(X) > 1:  # Check to ensure there is enough data
        X_train, X_test, y_train_flood, y_test_flood = train_test_split(X, y_flood, test_size=0.2, random_state=42)
        X_train, X_test, y_train_drought, y_test_drought = train_test_split(X, y_drought, test_size=0.2, random_state=42)
    else:
        # In case of only 1 sample, train without splitting
        X_train, y_train_flood, y_train_drought = X, y_flood, y_drought
        X_test, y_test_flood, y_test_drought = None, None, None

    return X_train, X_test, y_train_flood, y_test_flood, y_train_drought, y_test_drought, scaler

def build_lstm_model(input_shape):
    """
    Build an LSTM model for classification.

    Args:
        input_shape (tuple): Shape of the input data (samples, time steps, features).

    Returns:
        model: A compiled LSTM model.
    """
    model = Sequential()

    # LSTM layers
    model.add(LSTM(units=64, return_sequences=True, input_shape=input_shape))
    model.add(Dropout(0.2))
    model.add(LSTM(units=32))
    model.add(Dropout(0.2))

    # Dense layer
    model.add(Dense(units=1, activation='sigmoid'))  # Sigmoid for binary classification

    # Compile the model
    model.compile(optimizer=Adam(learning_rate=0.001), loss='binary_crossentropy', metrics=['accuracy'])

    return model

def train_lstm_model(engineered_data_path, model_output_path):
    """
    Train an LSTM model to predict flood and drought risk.

    Args:
        engineered_data_path (str): Path to the feature-engineered dataset.
        model_output_path (str): Path to save the trained LSTM models.

    Returns:
        None
    """
    # Preprocess the data
    X_train, X_test, y_train_flood, y_test_flood, y_train_drought, y_test_drought, scaler = preprocess_data_for_lstm(engineered_data_path)
    print("Data preprocessed successfully.")

    # Build the LSTM model for flood prediction
    print("Building LSTM model for flood prediction...")
    flood_model = build_lstm_model(X_train.shape[1:])
    
    # Train the model
    early_stop = EarlyStopping(monitor='val_loss', patience=5, restore_best_weights=True)
    flood_model.fit(X_train, y_train_flood, epochs=50, batch_size=32, validation_data=(X_test, y_test_flood), callbacks=[early_stop])

    # Evaluate the flood model
    flood_loss, flood_accuracy = flood_model.evaluate(X_test, y_test_flood)
    print(f"Flood Model Evaluation - Accuracy: {flood_accuracy}, Loss: {flood_loss}")

    # Save the flood model
    print("Saving the trained flood model...")
    flood_model.save(f'{model_output_path}_flood_model.h5')

    # Build the LSTM model for drought prediction
    print("Building LSTM model for drought prediction...")
    drought_model = build_lstm_model(X_train.shape[1:])
    
    # Train the model
    drought_model.fit(X_train, y_train_drought, epochs=50, batch_size=32, validation_data=(X_test, y_test_drought), callbacks=[early_stop])

    # Evaluate the drought model
    drought_loss, drought_accuracy = drought_model.evaluate(X_test, y_test_drought)
    print(f"Drought Model Evaluation - Accuracy: {drought_accuracy}, Loss: {drought_loss}")

    # Save the drought model
    print("Saving the trained drought model...")
    drought_model.save(f'{model_output_path}_drought_model.h5')

    print("Model training and saving completed successfully!")

if __name__ == "__main__":
    import sys
    if len(sys.argv) != 3:
        print("Usage: python train_lstm_model.py <engineered_data_path> <model_output_path>")
        sys.exit(1)

    engineered_data_path = sys.argv[1]
    model_output_path = sys.argv[2]

    try:
        train_lstm_model(engineered_data_path, model_output_path)
        print("LSTM model training completed successfully!")
    except Exception as e:
        print(f"Error: {e}")
