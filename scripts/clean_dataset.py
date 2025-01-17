import pandas as pd

def clean_weather_data(file_path, save_path):
    """
    Cleans the weather dataset by handling missing values, ensuring consistent data types,
    and selecting relevant features for AI model training.

    Args:
        file_path (str): Path to the dataset file.
        save_path (str): Path to save the cleaned dataset.

    Returns:
        pd.DataFrame: A cleaned DataFrame.
    """
    # Load the dataset
    print('reading file')
    df = pd.read_csv(file_path)

    print("Initial Data Summary:")
    print(df.info())
    print(df.describe())

    # Standardize column names
    df.columns = df.columns.str.strip().str.lower()

    # Ensure essential columns are present
    required_columns = ['date', 'temperature_max', 'temperature_min', 'precipitation',
                        'wind_speed_max', 'humidity_mean', 'soil_moisture']
    missing_columns = [col for col in required_columns if col not in df.columns]
    if missing_columns:
        raise ValueError(f"Missing required columns: {missing_columns}")

    # Convert 'date' to datetime
    print("\nConverting 'date' column to datetime...")
    df['date'] = pd.to_datetime(df['date'], errors='coerce')

    # Remove rows with invalid dates
    df = df.dropna(subset=['date'])

    # Handle missing values for numeric columns
    print("\nHandling missing values...")
    numeric_columns = ['temperature_max', 'temperature_min', 'precipitation',
                       'wind_speed_max', 'humidity_mean', 'soil_moisture']

    # Fill missing values with forward-fill and backward-fill
    df[numeric_columns] = df[numeric_columns].fillna(method='ffill').fillna(method='bfill')

    # Drop rows that still have missing values after filling
    df = df.dropna(subset=numeric_columns)

    # Ensure all numeric columns have correct data types
    print("\nEnsuring numeric data types...")
    for col in numeric_columns:
        df[col] = pd.to_numeric(df[col], errors='coerce')

    # Remove duplicates
    print("\nRemoving duplicates...")
    df = df.drop_duplicates()

    # Sort by date
    print("\nSorting by date...")
    df = df.sort_values(by='date')

    # Save cleaned data to a new CSV file
    df.to_csv(save_path, index=False)

    print(f"\nCleaned data saved to '{save_path}'.")
    return df

if __name__ == "__main__":
    import sys
    if len(sys.argv) != 3:
        print("Usage: python clean_dataset.py <input_file_path> <output_file_path>")
        sys.exit(1)

    input_file = sys.argv[1]
    output_file = sys.argv[2]

    try:
        clean_weather_data(input_file, output_file)
        print("Data cleaning completed successfully!")
    except Exception as e:
        print(f"Error: {e}")
