import pandas as pd

def process_data(file_path):
    data = pd.read_csv(file_path)
    # Ajoutez ici votre logique de traitement des données
    processed_data = data.describe()
    return processed_data.to_json()