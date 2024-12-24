import pandas as pd
from sklearn.preprocessing import MinMaxScaler

def clean_dataset(file_path, save_path):
    """
    Cleans the dataset in a CSV file and saves the cleaned dataset.

    Args:
        file_path (str): Path to the input CSV file.
        save_path (str): Path to save the cleaned CSV file.

    Returns:
        pd.DataFrame: The cleaned dataset.
    """
    # Load the dataset
    data = pd.read_csv(file_path)
    print("Initial dataset loaded.")

    # Step 1: Handle missing values
    # Fill numeric columns with mean
    numeric_cols = data.select_dtypes(include=['float64', 'int64']).columns
    data[numeric_cols] = data[numeric_cols].fillna(data[numeric_cols].mean())

    # Fill categorical columns with mode
    categorical_cols = data.select_dtypes(include=['object']).columns
    for col in categorical_cols:
        data[col] = data[col].fillna(data[col].mode()[0])

    print("Missing values handled.")

    # Step 2: Remove duplicates
    data.drop_duplicates(inplace=True)
    print("Duplicates removed.")

    # Step 3: Standardize formats
    # Standardize dates
    if 'date' in data.columns:
        data['date'] = pd.to_datetime(data['date'], errors='coerce')

    # Standardize text columns
    for col in categorical_cols:
        data[col] = data[col].str.strip().str.lower()

    print("Formats standardized.")

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
