import pandas as pd
from sklearn.preprocessing import MinMaxScaler

def clean_dataset(file_path, save_path):
    """
    Cleans the weather dataset by handling missing values, 
    ensuring consistent data types, and selecting relevant features.

    Args:
        file_path (str): Path to the dataset file.

    Returns:
        pd.DataFrame: A cleaned DataFrame.
    """
    # Load the dataset
    df = pd.read_csv(file_path)

    # Display initial summary of the data
    print("Initial Data Summary:")
    print(df.info())
    print(df.describe())

    # Handle missing values
    print("\nHandling Missing Values...")
    missing_threshold = 0.2  # Drop columns with >20% missing data
    df = df.dropna(axis=1, thresh=int((1 - missing_threshold) * len(df)))

    # Fill remaining missing values (forward fill)
    df.fillna(method='ffill', inplace=True)
    df.fillna(method='bfill', inplace=True)

    # Ensure consistent data types
    print("\nEnsuring Data Type Consistency...")
    if 'time' in df.columns:
        df['time'] = pd.to_datetime(df['time'])
    
    # Convert specific columns to numeric (example: humidity, soil moisture)
    numeric_columns = ['relative_humidity_2m', 'soil_moisture_0_to_7cm']
    for col in numeric_columns:
        if col in df.columns:
            df[col] = pd.to_numeric(df[col], errors='coerce')

    # Drop duplicates
    print("\nDropping Duplicates...")
    df.drop_duplicates(inplace=True)

    # Select relevant columns
    print("\nSelecting Relevant Features...")
    relevant_columns = ['time', 'relative_humidity_2m', 'soil_moisture_0_to_7cm']
    df = df[relevant_columns]

    # Display cleaned summary
    print("\nCleaned Data Summary:")
    print(df.info())
    print(df.head())

    # Step 4: Handle outliers (using IQR method for numeric columns)
    for col in numeric_cols:
        Q1 = data[col].quantile(0.25)
        Q3 = data[col].quantile(0.75)
        IQR = Q3 - Q1
        lower_bound = Q1 - 1.5 * IQR
        upper_bound = Q3 + 1.5 * IQR
        data[col] = data[col].apply(lambda x: x if lower_bound <= x <= upper_bound else data[col].median())

    print("Outliers handled.")

    # Step 5: Encode categorical variables
    data = pd.get_dummies(data, columns=categorical_cols, drop_first=True)
    print("Categorical variables encoded.")

    # Step 6: Normalize numeric columns
    scaler = MinMaxScaler()
    data[numeric_cols] = scaler.fit_transform(data[numeric_cols])
    print("Numeric columns normalized.")

    # Save the cleaned dataset
    data.to_csv(save_path, index=False)
    print(f"Cleaned dataset saved to {save_path}.")

    return data
