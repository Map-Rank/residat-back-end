import numpy as np
import matplotlib.pyplot as plt
from sklearn.metrics import mean_absolute_error, mean_squared_error

def evaluate_model(model, X_test, y_test):
    """
    Evaluates the model on test data and calculates evaluation metrics.

    Args:
        model: Trained model.
        X_test (numpy.ndarray): Features for the test set.
        y_test (numpy.ndarray): Ground truth labels for the test set.

    Returns:
        dict: Evaluation metrics (MAE, RMSE, R2).
    """
    # Predictions
    y_pred = model.predict(X_test)

    # Metrics
    mae = mean_absolute_error(y_test, y_pred)
    rmse = np.sqrt(mean_squared_error(y_test, y_pred))
    r2 = 1 - (np.sum((y_test - y_pred) ** 2) / np.sum((y_test - np.mean(y_test)) ** 2))

    # Print results
    print(f"Mean Absolute Error (MAE): {mae}")
    print(f"Root Mean Squared Error (RMSE): {rmse}")
    print(f"R-squared (R2): {r2}")

    return {
        "MAE": mae,
        "RMSE": rmse,
        "R2": r2
    }

def plot_predictions(y_test, y_pred, title="Actual vs Predicted Water Risk", save_path=None):
    """
    Plots the actual vs predicted values.

    Args:
        y_test (numpy.ndarray): Ground truth labels.
        y_pred (numpy.ndarray): Predicted labels.
        title (str): Title of the plot.
        save_path (str, optional): File path to save the plot. Defaults to None.
    """
    plt.figure(figsize=(10, 6))
    plt.plot(y_test, label="Actual", color="blue")
    plt.plot(y_pred, label="Predicted", color="orange")
    plt.title(title)
    plt.xlabel("Time Steps")
    plt.ylabel("Water Risk (%)")
    plt.legend()
    if save_path:
        plt.savefig(save_path)
    plt.show()

def plot_residuals(y_test, y_pred, title="Residual Analysis", save_path=None):
    """
    Plots residuals (errors) between actual and predicted values.

    Args:
        y_test (numpy.ndarray): Ground truth labels.
        y_pred (numpy.ndarray): Predicted labels.
        title (str): Title of the plot.
        save_path (str, optional): File path to save the plot. Defaults to None.
    """
    residuals = y_test - y_pred
    plt.figure(figsize=(10, 6))
    plt.scatter(range(len(residuals)), residuals, color="red", alpha=0.6)
    plt.axhline(0, linestyle="--", color="black", alpha=0.8)
    plt.title(title)
    plt.xlabel("Time Steps")
    plt.ylabel("Residuals")
    if save_path:
        plt.savefig(save_path)
    plt.show()