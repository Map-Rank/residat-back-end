import pandas as pd

def merge_data(geo_data_path, history_data_path, weather_data_path, hydro_data_path, output_path):
    """
    Merges Geo Data, History Data, Weather Data, and Hydro Data into a single dataset.

    Args:
        geo_data_path (str): Path to the Geo Data CSV file.
        history_data_path (str): Path to the History Data CSV file.
        weather_data_path (str): Path to the Weather Data CSV file.
        hydro_data_path (str): Path to the Hydro Data CSV file.
        output_path (str): Path to save the merged dataset.

    Returns:
        pd.DataFrame: The merged dataset.
    """
    # Step 1: Load the datasets
    geo_data = pd.read_csv(geo_data_path)
    history_data = pd.read_csv(history_data_path)
    weather_data = pd.read_csv(weather_data_path)
    hydro_data = pd.read_csv(hydro_data_path)

    print("Datasets loaded successfully.")

    # Step 2: Ensure common keys are consistent
    # Example: Standardize column names
    geo_data.rename(columns={'region_id': 'region', 'latitude': 'lat', 'longitude': 'lon'}, inplace=True)
    history_data.rename(columns={'region_id': 'region', 'event_date': 'date'}, inplace=True)
    weather_data.rename(columns={'location_id': 'region', 'observation_date': 'date'}, inplace=True)
    hydro_data.rename(columns={'station_region': 'region', 'measurement_date': 'date'}, inplace=True)

    print("Column names standardized.")

    # Step 3: Convert date columns to datetime
    for df in [history_data, weather_data, hydro_data]:
        df['date'] = pd.to_datetime(df['date'])

    print("Date columns converted to datetime.")

    # Step 4: Merge datasets
    # Merging datasets on region and date keys
    merged_data = pd.merge(geo_data, history_data, on='region', how='left')
    merged_data = pd.merge(merged_data, weather_data, on=['region', 'date'], how='left')
    merged_data = pd.merge(merged_data, hydro_data, on=['region', 'date'], how='left')

    print("Datasets merged successfully.")

    # Step 5: Handle missing values
    merged_data.fillna(method='ffill', inplace=True)  # Forward fill missing values
    merged_data.fillna(method='bfill', inplace=True)  # Backward fill if ffill doesn't apply

    print("Missing values handled.")

    # Step 6: Save the merged dataset
    merged_data.to_csv(output_path, index=False)
    print(f"Merged dataset saved to {output_path}")

    # ----- DATA ASSUMPTIONS -------
    # Hydrological Data: Includes columns like date, river_flow, groundwater_level, reservoir_level.
    # Weather Data: Includes date, rainfall, temperature, humidity.
    # Geographical Data: Includes location, elevation, slope, soil_type.
    # Historical Data: Includes date, flood_severity, drought_severity.
    
    # ----- USAGE OF THE CLASS -----

    # # File paths
    # geo_data_path = "geo_data.csv"
    # history_data_path = "history_data.csv"
    # weather_data_path = "weather_data.csv"
    # hydro_data_path = "hydro_data.csv"
    # output_path = "merged_data.csv"

    # # Merge data
    # merged_data = merge_data(geo_data_path, history_data_path, weather_data_path, hydro_data_path, output_path)

    # # Display the first few rows of the merged dataset
    # print(merged_data.head())

    return merged_data
