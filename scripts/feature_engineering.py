import pandas as pd
import numpy as np

def feature_engineering(merged_data_path, output_path):
    """
    Performs feature engineering on the merged dataset.

    Args:
        merged_data_path (str): Path to the merged dataset CSV file.
        output_path (str): Path to save the engineered dataset.

    Returns:
        pd.DataFrame: The dataset with new and transformed features.
    """
    # Load the merged dataset
    data = pd.read_csv(merged_data_path)
    print("Merged dataset loaded successfully.")
    
    # Ensure date column is datetime
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
    data.sort_values(by=['region', 'date'], inplace=True)
    
    # Example: Cumulative rainfall
    if 'rainfall' in data.columns:
        data['cumulative_rainfall'] = data.groupby('region')['rainfall'].cumsum()

    # Example: Lagged water level
    if 'water_level' in data.columns:
        data['lag_water_level_1d'] = data.groupby('region')['water_level'].shift(1)  # 1-day lag
        data['lag_water_level_7d'] = data.groupby('region')['water_level'].shift(7)  # 7-day lag

    # Step 3: Derive interaction features
    print("Creating interaction features...")
    if 'rainfall' in data.columns and 'temperature' in data.columns:
        # Interaction between rainfall and temperature
        data['rain_temp_interaction'] = data['rainfall'] * data['temperature']
    
    # Step 4: Rolling statistics
    print("Calculating rolling statistics...")
    if 'water_level' in data.columns:
        data['rolling_mean_water_7d'] = data.groupby('region')['water_level'].rolling(window=7).mean().reset_index(0, drop=True)
        data['rolling_std_water_7d'] = data.groupby('region')['water_level'].rolling(window=7).std().reset_index(0, drop=True)
    
    # Step 5: Normalize key features (Min-Max Scaling)
    print("Normalizing features...")
    from sklearn.preprocessing import MinMaxScaler
    scaler = MinMaxScaler()
    
    features_to_normalize = ['rainfall', 'temperature', 'water_level', 'cumulative_rainfall']
    for feature in features_to_normalize:
        if feature in data.columns:
            data[f'{feature}_normalized'] = scaler.fit_transform(data[[feature]])
    
    # Step 6: Handle missing values from lag/rolling calculations
    print("Handling missing values...")
    data.fillna(method='ffill', inplace=True)  # Forward fill
    data.fillna(method='bfill', inplace=True)  # Backward fill

    # Save the engineered dataset
    data.to_csv(output_path, index=False)
    print(f"Feature engineered dataset saved to {output_path}")
    
    # USAGE 
    # File paths
    # merged_data_path = "merged_data.csv"
    # output_path = "engineered_data.csv"

    # # Perform feature engineering
    # engineered_data = feature_engineering(merged_data_path, output_path)

    # # Display the first few rows of the engineered dataset
    # print(engineered_data.head())
    return data
