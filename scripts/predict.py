import pandas as pd
import numpy as np
import tensorflow as tf
from tensorflow.keras.models import load_model
import joblib
from datetime import datetime, timedelta

def predict_next_5_days(model_path, scaler_path, current_data_paths):
    """
    Generate reservoir level predictions for the next 5 days
    
    Args:
        model_path: Path to the saved LSTM model
        scaler_path: Path to the saved scaler
        current_data_paths: Dictionary with paths to current data files
    
    Returns:
        DataFrame with predictions for the next 5 days
    """
    # Load model and scaler
    model = load_model(model_path)
    scaler = joblib.load(scaler_path)
    
    # Load and preprocess current data
    data, metadata, _ = load_and_preprocess_data(
        current_data_paths['weather_path'],
        current_data_paths['hydro_path'],
        current_data_paths['geo_path'],
        scaler_path
    )
    
    # Group by location
    locations = data['location'].unique()
    
    # Get the most recent date in the data
    latest_date = metadata['date'].max()
    
    # Initialize list to store predictions
    all_predictions = []
    
    # For each location, predict the next 5 days
    for location in locations:
        # Filter data for this location
        loc_data = data[data['location'] == location].copy()
        loc_metadata = metadata[metadata['location'] == location].copy()
        
        # Sort by date
        loc_data = loc_data.sort_values('date')
        
        # Get the latest sequence
        feature_cols = [col for col in loc_data.columns if col not in ['date', 'location']]
        
        # Make sure we have enough historical data
        if len(loc_data) < 30:  # time_steps parameter
            print(f"Warning: Not enough historical data for location {location}")
            continue
        
        # Initialize prediction date and storage
        current_sequence = loc_data[feature_cols].iloc[-30:].values  # Last 30 days
        current_date = latest_date
        location_predictions = []
        
        # Predict for next 5 days
        for i in range(5):
            # Reshape sequence for LSTM input [samples, time_steps, features]
            X_pred = current_sequence.reshape(1, 30, len(feature_cols))
            
            # Make prediction
            pred = model.predict(X_pred, verbose=0)[0]
            
            # Create prediction record
            next_date = current_date + timedelta(days=1)
            
            # Map prediction outputs to target names (adjust based on your model outputs)
            target_names = ['reservoir_7d', 'reservoir_14d', 'reservoir_30d', 
                          'reservoir_change_7d', 'reservoir_change_14d', 'reservoir_change_30d',
                          'reservoir_7d_mean', 'reservoir_30d_mean', 'reservoir_7d_change']
            
            # Use only the names that match the number of outputs
            used_targets = target_names[:pred.shape[0]]
            
            # For a 5-day forecast, we're most interested in the immediate prediction
            # Which is likely the reservoir_7d value
            
            pred_record = {
                'date': next_date, 
                'location': location, 
                'day': i+1,
                'predicted_level': pred[0] if len(used_targets) > 0 else None,  # Assuming first output is most relevant
                'predicted_change_pct': pred[3] if len(used_targets) > 3 else None  # Assuming change percentage is 4th output
            }
            
            # Add all available predictions
            for j, name in enumerate(used_targets):
                pred_record[name] = pred[j]
            
            location_predictions.append(pred_record)
            
            # Update for next iteration
            new_features = current_sequence[-1].copy()
            
            # Update the reservoir level based on prediction
            reservoir_idx = feature_cols.index('reservoir_level') if 'reservoir_level' in feature_cols else 0
            new_features[reservoir_idx] = pred[0]  # Update with prediction
            
            # Roll the sequence forward
            current_sequence = np.vstack([current_sequence[1:], new_features])
            current_date = next_date
            
        all_predictions.extend(location_predictions)
    
    # Convert to DataFrame
    predictions_df = pd.DataFrame(all_predictions)
    
    return predictions_df

def assess_short_term_risks(predictions_df, location_capacities):
    """
    Evaluate short-term water risks based on 5-day predictions
    
    Args:
        predictions_df: DataFrame with reservoir predictions
        location_capacities: Dict mapping locations to their capacities
    
    Returns:
        DataFrame with risk assessments added
    """
    results = predictions_df.copy()
    
    # Initialize risk columns
    results['risk_level'] = 'Normal'
    results['risk_type'] = 'None'
    results['alert'] = False
    
    for idx, row in results.iterrows():
        location = row['location']
        
        if location not in location_capacities:
            continue
            
        capacity = location_capacities[location]
        normal_level = capacity['normal']
        max_level = capacity['max']
        min_level = capacity['min']
        
        # Get predicted level and change
        predicted_level = row['predicted_level']
        predicted_change = row.get('predicted_change_pct', 0)
        
        # Calculate percentages of capacity
        pct_of_normal = (predicted_level / normal_level) * 100
        pct_of_max = (predicted_level / max_level) * 100
        pct_of_min = (predicted_level / min_level) * 100
        
        # ---- RISK ASSESSMENT ----
        # Flood risk assessment
        if pct_of_max > 98:
            results.loc[idx, 'risk_level'] = 'Severe'
            results.loc[idx, 'risk_type'] = 'Flood'
            results.loc[idx, 'alert'] = True
        elif pct_of_max > 90:
            results.loc[idx, 'risk_level'] = 'Moderate'
            results.loc[idx, 'risk_type'] = 'Flood'
            results.loc[idx, 'alert'] = True
        
        # Drought risk assessment
        elif pct_of_min < 110:
            results.loc[idx, 'risk_level'] = 'Severe'
            results.loc[idx, 'risk_type'] = 'Drought'
            results.loc[idx, 'alert'] = True
        elif pct_of_min < 125:
            results.loc[idx, 'risk_level'] = 'Moderate'
            results.loc[idx, 'risk_type'] = 'Drought'
            results.loc[idx, 'alert'] = results.loc[idx, 'day'] >= 3  # Alert only if persists to day 3+
            
        # Rapid change assessment
        if abs(predicted_change) > 10:
            change_type = 'Flood' if predicted_change > 0 else 'Drought'
            change_level = 'Severe' if abs(predicted_change) > 15 else 'Moderate'
            
            # Only override if this risk is higher
            if change_level == 'Severe' or results.loc[idx, 'risk_level'] != 'Severe':
                results.loc[idx, 'risk_level'] = change_level
                results.loc[idx, 'risk_type'] = change_type
                results.loc[idx, 'alert'] = True
    
    return results

# Example usage
if __name__ == "__main__":
    model_path = 'model.h5'
    scaler_path = 'scaler.joblib'
    
    current_data_paths = {
        'weather_path': 'CSVs/weather_data.csv',
        'hydro_path': 'CSVs/hydro_data.csv',
        'geo_path': 'CSVs/geo.csv'
    }
    
    # Get predictions for next 5 days
    predictions = predict_next_5_days(model_path, scaler_path, current_data_paths)
    
    # Define location capacities
    location_capacities = {
        'Riverdale': {'min': 120, 'max': 180, 'normal': 150},
        'Lakeview': {'min': 70, 'max': 110, 'normal': 90}
    }
    
    # Assess risks
    risk_assessment = assess_short_term_risks(predictions, location_capacities)
    
    # Save results
    risk_assessment.to_csv('5_day_water_risk_forecast.csv', index=False)
    
    # Print summary
    print("5-Day Water Risk Forecast:\n")
    
    for location in risk_assessment['location'].unique():
        print(f"\nLocation: {location}")
        loc_data = risk_assessment[risk_assessment['location'] == location]
        
        print(f"{'Day':^5} {'Date':^12} {'Level':^8} {'% Change':^8} {'Risk Type':^10} {'Risk Level':^10} {'Alert':^5}")
        print("-" * 65)
        
        for _, row in loc_data.iterrows():
            print(f"{row['day']:^5} {row['date'].strftime('%Y-%m-%d'):^12} "
                  f"{row['predicted_level']:.2f:^8} {row.get('predicted_change_pct', 0):.2f}%:^8 "
                  f"{row['risk_type']:^10} {row['risk_level']:^10} {'✓' if row['alert'] else '':^5}")
    
    # Count alerts
    alerts = risk_assessment[risk_assessment['alert']]
    if len(alerts) > 0:
        print(f"\n⚠️ {len(alerts)} alerts detected across all locations!")
    else:
        print("\n✓ No immediate water risks detected.")