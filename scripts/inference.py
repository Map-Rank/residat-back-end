from src.utils import load_model, preprocess_data
import numpy as np

def make_predictions(model_path, data_path, scaler):
    """
    Loads a trained model and makes predictions on new data.

    Args:
        model_path (str): Path to the saved model file.
        data_path (str): Path to the new data CSV file.
        scaler (object): Fitted scaler used during training.

    Returns:
        numpy.ndarray: Predicted water risk values.
    """
    # Load model
    model = load_model(model_path)

    # Load and preprocess data
    X_new = preprocess_data(data_path, scaler)

    # Make predictions
    predictions = model.predict(X_new)
    predictions = scaler.inverse_transform(predictions)  # Reverse scaling

    return predictions