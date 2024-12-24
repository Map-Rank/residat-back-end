import matplotlib.pyplot as plt
import numpy as np

def plot_training_history(history):
    """
    Plots training and validation loss from the model's history.
    Args:
        history: Training history object returned by model.fit().
    """
    plt.figure(figsize=(12, 6))
    plt.plot(history.history['loss'], label='Training Loss', color='blue')
    plt.plot(history.history['val_loss'], label='Validation Loss', color='orange')
    plt.title('Training and Validation Loss')
    plt.xlabel('Epochs')
    plt.ylabel('Loss (MSE)')
    plt.legend()
    plt.grid()
    plt.show()

def plot_predictions(y_test, y_pred, scaler_y):
    """
    Plots actual vs. predicted values for test data.
    Args:
        y_test: Actual target values (scaled).
        y_pred: Predicted target values (scaled).
        scaler_y: MinMaxScaler used to scale the target variable.
    """
    # Convert scaled values back to original scale
    y_test_original = scaler_y.inverse_transform(y_test)
    y_pred_original = scaler_y.inverse_transform(y_pred)

    plt.figure(figsize=(12, 6))
    plt.plot(y_test_original, label='Actual', color='green')
    plt.plot(y_pred_original, label='Predicted', color='red', linestyle='dashed')
    plt.title('Actual vs. Predicted Water Risk')
    plt.xlabel('Time Steps')
    plt.ylabel('Water Risk (%)')
    plt.legend()
    plt.grid()
    plt.show()

def plot_residuals(y_test, y_pred, scaler_y):
    """
    Plots residuals (errors) between actual and predicted values.
    Args:
        y_test: Actual target values (scaled).
        y_pred: Predicted target values (scaled).
        scaler_y: MinMaxScaler used to scale the target variable.
    """
    # Convert scaled values back to original scale
    y_test_original = scaler_y.inverse_transform(y_test)
    y_pred_original = scaler_y.inverse_transform(y_pred)

    residuals = y_test_original - y_pred_original

    plt.figure(figsize=(12, 6))
    plt.scatter(range(len(residuals)), residuals, alpha=0.5, color='purple')
    plt.axhline(0, color='black', linestyle='dashed')
    plt.title('Residuals Analysis')
    plt.xlabel('Time Steps')
    plt.ylabel('Residuals (Actual - Predicted)')
    plt.grid()
    plt.show()

# Example Usage
# Assuming `history`, `y_test`, `y_pred`, and `scaler_y` are available from the training phase
# plot_training_history(history)
# y_pred = model.predict(X_test)
# plot_predictions(y_test, y_pred, scaler_y)
# plot_residuals(y_test, y_pred, scaler_y)
