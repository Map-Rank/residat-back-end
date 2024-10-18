<?php

namespace App\Service;

use Exception;
use Phpml\Classification\Ensemble\RandomForest;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\Dataset\ArrayDataset;
use Phpml\Metric\Accuracy;

class FloodAIService{
    private $data;
    private $featureRanges;
    private $labels;
    private $model;

    public function loadData($filename) {
        // Load data from CSV file
        $rawData = array_map('str_getcsv', file($filename));
        $headers = array_shift($rawData);

        // Calculate feature ranges for normalization
        $this->calculateFeatureRanges($rawData, $headers);

        // Preprocess the data
        $this->data = array_map(function($row) use ($headers) {
            $processedRow = array_combine($headers, $row);

            // Convert date string to timestamp
            $processedRow['date'] = strtotime($processedRow['date']);

            // Normalize numerical values
            $numericalColumns = ['temperature', 'precipitation', 'river_level', 'elevation', 'soil_moisture'];
            foreach ($numericalColumns as $column) {
                $processedRow[$column] = $this->normalizeValue($column, $processedRow[$column]);
            }

            // Handle missing values
            foreach ($processedRow as $key => $value) {
                if ($value === '' || $value === null) {
                    $processedRow[$key] = $this->handleMissingValue($key);
                }
            }

            return $processedRow;
        }, $rawData);
    }

    private function calculateFeatureRanges($rawData, $headers) {
        $numericalColumns = ['temperature', 'precipitation', 'river_level', 'elevation', 'soil_moisture'];
        foreach ($numericalColumns as $column) {
            $columnIndex = array_search($column, $headers);
            $values = array_column($rawData, $columnIndex);
            $values = array_filter($values, 'is_numeric');
            $this->featureRanges[$column] = [
                'min' => min($values),
                'max' => max($values)
            ];
        }
    }

    private function normalizeValue($column, $value) {
        if (!isset($this->featureRanges[$column])) {
            return $value; // Return original value if not a numerical column
        }
        $min = $this->featureRanges[$column]['min'];
        $max = $this->featureRanges[$column]['max'];
        if ($max == $min) {
            return 0.5; // Handle the case where all values are the same
        }
        return ($value - $min) / ($max - $min);
    }

    private function handleMissingValue($column) {
        switch($column) {
            case 'temperature':
                // For temperature, use the average of nearby time points or the overall mean
                return $this->calculateAverage($column);
            case 'precipitation':
                // For precipitation, missing might mean no rain, so we return 0
                return 0;
            case 'river_level':
                // For river level, use the last known value or the average
                return $this->getLastKnownValueOrAverage($column);
            case 'elevation':
                // For elevation, we use the median as it's less affected by outliers
                return $this->calculateMedian($column);
            case 'soil_moisture':
                // For soil moisture, use the average of nearby points or the overall mean
                return $this->calculateAverage($column);
            default:
                // For any other columns, we'll return null
                return null;
        }
    }

    private function calculateAverage($column) {
        $values = array_column($this->data, $column);
        $values = array_filter($values, 'is_numeric');
        return array_sum($values) / count($values);
    }

    private function calculateMedian($column) {
        $values = array_column($this->data, $column);
        $values = array_filter($values, 'is_numeric');
        sort($values);
        $count = count($values);
        $middleIndex = floor($count / 2);
        if ($count % 2 == 0) {
            return ($values[$middleIndex - 1] + $values[$middleIndex]) / 2;
        } else {
            return $values[$middleIndex];
        }
    }

    private function getLastKnownValueOrAverage($column) {
        $values = array_column($this->data, $column);
        $lastKnownValue = null;
        foreach ($values as $value) {
            if (is_numeric($value)) {
                $lastKnownValue = $value;
            }
        }
        return $lastKnownValue !== null ? $lastKnownValue : $this->calculateAverage($column);
    }

    public function engineerFeatures() {
        $this->data = array_map(function($row) {
            // Add rolling average for temperature and precipitation
            $row['temp_7day_avg'] = $this->calculateRollingAverage('temperature', 7, $row['date']);
            $row['precip_7day_avg'] = $this->calculateRollingAverage('precipitation', 7, $row['date']);

            // Calculate days since last significant rainfall
            $row['days_since_rainfall'] = $this->daysSinceSignificantRainfall($row['date']);

            // Create interaction feature between soil moisture and precipitation
            $row['soil_moisture_precip_interaction'] = $row['soil_moisture'] * $row['precipitation'];

            // Add seasonal features
            $seasonFeatures = $this->getSeasonFeatures($row['date']);
            $row['sin_day'] = $seasonFeatures['sin_day'];
            $row['cos_day'] = $seasonFeatures['cos_day'];

            // Calculate rate of change for river level
            $row['river_level_change'] = $this->calculateRateOfChange('river_level', $row['date']);

            return $row;
        }, $this->data);

        // Recalculate feature ranges after engineering
        $this->calculateFeatureRanges($this->data, array_keys($this->data[0]));
    }

    private function calculateRollingAverage($feature, $days, $currentDate) {
        $relevantData = array_filter($this->data, function($row) use ($currentDate, $days) {
            return $row['date'] <= $currentDate && $row['date'] > $currentDate - ($days * 86400);
        });
        $values = array_column($relevantData, $feature);
        return !empty($values) ? array_sum($values) / count($values) : null;
    }

    private function daysSinceSignificantRainfall($currentDate, $threshold = 10) {
        $days = 0;
        foreach (array_reverse($this->data) as $row) {
            if ($row['date'] <= $currentDate) {
                if ($row['precipitation'] >= $threshold) {
                    return $days;
                }
                $days++;
            }
        }
        return $days;
    }

    private function getSeasonFeatures($date) {
        $dayOfYear = date('z', $date);
        return [
            'sin_day' => sin(2 * M_PI * $dayOfYear / 365),
            'cos_day' => cos(2 * M_PI * $dayOfYear / 365)
        ];
    }

    private function calculateRateOfChange($feature, $currentDate) {
        $currentValue = $this->getValueAtDate($feature, $currentDate);
        $previousValue = $this->getValueAtDate($feature, $currentDate - 86400); // 1 day before
        return $currentValue && $previousValue ? $currentValue - $previousValue : null;
    }

    private function getValueAtDate($feature, $date) {
        foreach (array_reverse($this->data) as $row) {
            if ($row['date'] <= $date) {
                return $row[$feature];
            }
        }
        return null;
    }

    public function prepareDataForTraining() {
        $features = [];
        $this->labels = [];

        foreach ($this->data as $row) {
            $features[] = $this->extractFeatures($row);
            $this->labels[] = $row['flood_occurred'] ?? 0; // Assuming 'flood_occurred' is our target variable
        }

        return new ArrayDataset($features, $this->labels);
    }

    private function extractFeatures($row) {
        return [
            $row['temperature'],
            $row['precipitation'],
            $row['river_level'],
            $row['soil_moisture'],
            $row['temp_7day_avg'],
            $row['precip_7day_avg'],
            $row['days_since_rainfall'],
            $row['soil_moisture_precip_interaction'],
            $row['sin_day'],
            $row['cos_day'],
            $row['river_level_change']
        ];
    }

    public function trainModel() {
        $dataset = $this->prepareDataForTraining();

        // Split the dataset into training and testing sets
        $split = new StratifiedRandomSplit($dataset, 0.2);

        // Initialize and train the model
        $this->model = new RandomForest();
        $this->model->train($split->getTrainSamples(), $split->getTrainLabels());

        // Evaluate the model
        $predictions = $this->model->predict($split->getTestSamples());
        $accuracy = Accuracy::score($split->getTestLabels(), $predictions);

        echo "Model accuracy: " . $accuracy . "\n";
    }

    public function predict($inputData) {
        if (!$this->model) {
            throw new Exception("Model has not been trained yet.");
        }

        $features = $this->extractFeatures($inputData);
        return $this->model->predict([$features])[0];
    }
}
