import pandas as pd
import numpy as np
from sklearn.preprocessing import MinMaxScaler

def feature_engineering(merged_data_path, output_path):
    """
    Performs feature engineering on the merged dataset to prepare it for flood and drought prediction.

    Args:
        merged_data_path (str): Path to the merged dataset CSV file.
        output_path (str): Path to save the engineered dataset.

    Returns:
        pd.DataFrame: The dataset with new and transformed features.
    """
    # Load the merged dataset
    data = pd.read_csv(merged_data_path)
    print("Merged dataset loaded successfully.")

    # Ensure the date column is datetime
    data['date'] = pd.to_datetime(data['date'])

    # Step 1: Extract temporal features
    print("Extracting temporal features...")
    data['year'] = data['date'].dt.year
    data['month'] = data['date'].dt.month
    data['day'] = data['date'].dt.day
    data['week'] = data['date'].dt.isocalendar().week
    data['day_of_week'] = data['date'].dt.dayofweek  # 0 = Monday, 6 = Sunday
    data['is_weekend'] = data['day_of_week'].apply(lambda x: 1 if x >= 5 else 0)  # 1 for Saturday and Sunday

    # Step 2: Create cumulative and lag features
    print("Creating cumulative and lag features...")
    data.sort_values(by=['location', 'date'], inplace=True)

    if 'precipitation' in data.columns:
        data['cumulative_precipitation'] = data.groupby('location')['precipitation'].cumsum()

    if 'groundwater_level' in data.columns:
        data['lag_groundwater_level_1d'] = data.groupby('location')['groundwater_level'].shift(1)  # 1-day lag
        data['lag_groundwater_level_7d'] = data.groupby('location')['groundwater_level'].shift(7)  # 7-day lag

    # Step 3: Create interaction features
    print("Creating interaction features...")
    if 'precipitation' in data.columns and 'temperature' in data.columns:
        data['rain_temp_interaction'] = data['precipitation'] * data['temperature']

    # Step 4: Rolling statistics
    print("Calculating rolling statistics...")
    if 'groundwater_level' in data.columns:
        data['rolling_mean_water_7d'] = data.groupby('location')['groundwater_level'].transform(lambda x: x.rolling(window=7).mean())
        data['rolling_std_water_7d'] = data.groupby('location')['groundwater_level'].transform(lambda x: x.rolling(window=7).std())

    # Step 5: Normalize key features (Min-Max Scaling)
    print("Normalizing features...")
    scaler = MinMaxScaler()
    features_to_normalize = ['precipitation', 'temperature', 'groundwater_level', 'cumulative_precipitation']
    for feature in features_to_normalize:
        if feature in data.columns:
            # Flatten the values to 1D and apply MinMaxScaler
            data[f'{feature}_normalized'] = data.groupby('location')[feature].transform(
                lambda x: scaler.fit_transform(x.values.reshape(-1, 1)).flatten()
            )

    # Step 6: Handle missing values from lag/rolling calculations
    print("Handling missing values...")
    data.fillna(method='ffill', inplace=True)  # Forward fill
    data.fillna(method='bfill', inplace=True)  # Backward fill

    # Step 7: Create flood and drought binary labels (1 for risk, 0 for no risk)
    print("Creating flood and drought binary labels...")
    # Example flood condition: Flood risk if precipitation exceeds a threshold
    flood_threshold = 50  # Adjust based on your data
    data['flood'] = (data['precipitation'] > flood_threshold).astype(int)  # 1 = Flood risk, 0 = No flood risk

    # Example drought condition: Drought risk if groundwater level is below a threshold
    drought_threshold = 30  # Adjust based on your data
    data['drought'] = (data['groundwater_level'] < drought_threshold).astype(int)  # 1 = Drought risk, 0 = No drought risk

    # Save the engineered dataset
    data.to_csv(output_path, index=False)
    print(f"Feature engineered dataset saved to {output_path}")

    return data

if __name__ == "__main__":
    import sys
    if len(sys.argv) != 3:
        print("Usage: python feature_engineering.py <input_file_path> <output_file_path>")
        sys.exit(1)

    input_file = sys.argv[1]
    output_file = sys.argv[2]

    try:
        feature_engineering(input_file, output_file)
        print("Feature engineering completed successfully!")
    except Exception as e:
        print(f"Error: {e}")
