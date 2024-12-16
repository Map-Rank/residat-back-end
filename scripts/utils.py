import pandas as pd
import numpy as np
import joblib
from tensorflow.keras.models import load_model as tf_load_model

def load_model(model_path):
    """
    Loads a saved model from the specified path.

    Args:
        model_path (str): Path to the model file.

    Returns:
        Trained model object.
    """
    return tf_load_model(model_path)

def preprocess_data(data_path, scaler):
    """
    Loads and preprocesses new data for prediction.

    Args:
        data_path (str): Path to the CSV file containing new data.
        scaler (object): Fitted scaler used during training.

    Returns:
        numpy.ndarray: Preprocessed feature data ready for prediction.
    """
    # Load data
    df = pd.read_csv(data_path)

    # Select relevant columns (ensure alignment with training data)
    features = df.drop(columns=['date', 'target'], errors='ignore').values

    # Scale data
    features_scaled = scaler.transform(features)

    return features_scaled

def save_scaler(scaler, path):
    """
    Saves the scaler object to a file.

    Args:
        scaler (object): Scaler to save.
        path (str): Path to save the scaler file.
    """
    joblib.dump(scaler, path)

def load_scaler(path):
    """
    Loads a saved scaler object from a file.

    Args:
        path (str): Path to the scaler file.

    Returns:
        Scaler object.
    """
    return joblib.load(path)