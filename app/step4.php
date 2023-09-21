<?php
require 'vendor/autoload.php';

use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Regressors\MLPRegressor;
use Rubix\ML\NeuralNet\Layers\Dense;
use Rubix\ML\NeuralNet\Layers\Activation;
use Rubix\ML\NeuralNet\ActivationFunctions\ReLU;
use Rubix\ML\NeuralNet\Optimizers\Adam;
use Rubix\ML\Datasets\Unlabeled;



function fetch_data() {
    $url = "http://archive.ics.uci.edu/ml/machine-learning-databases/auto-mpg/auto-mpg.data";
    $data = file_get_contents($url);
    $lines = explode("\n", trim($data));

    $dataSet = [];
    foreach ($lines as $line) {
        $items = preg_split('/\s+/', trim($line));
        if (count($items) >= 4) {
            if ($items[3] !== '?') {
                $dataSet[] = [
                    'MPG' => floatval($items[0]),
                    'Horsepower' => floatval($items[3]),
                ];
            }
        }
    }
    return $dataSet;
}

$dataSet = fetch_data();

$samples = array_map(function ($entry) {
    return [$entry['Horsepower']];
}, $dataSet);
$labels = array_map(function ($entry) {
    return $entry['MPG'];
}, $dataSet);

$dataset = new Labeled($samples, $labels);

$estimator = new MLPRegressor([
    new Dense(64),
    new Activation(new ReLU()),
    new Dense(64),
    new Activation(new ReLU()),
], 100, new Adam(0.001));

$estimator->train($dataset);



$predictions = [];
for ($hp = 1; $hp <= 250; $hp++) {
    $datasetForPrediction = new Unlabeled([[$hp]]);
    $predictedValue = $estimator->predict($datasetForPrediction);
    $predictions[$hp] = $predictedValue[0];
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horsepower vs MPG Prediction</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<canvas id="predictionChart" width="400" height="200"></canvas>
<script>
    let predictions = <?php echo json_encode($predictions); ?>;
    let actualData = {
        x: <?php echo json_encode(array_column($dataSet, 'Horsepower')); ?>,
        y: <?php echo json_encode(array_column($dataSet, 'MPG')); ?>
    };

    let ctx = document.getElementById('predictionChart').getContext('2d');
    let chart = new Chart(ctx, {
        type: 'scatter',
        data: {
            datasets: [
                {
                    label: 'Predicted MPG',
                    data: Object.keys(predictions).map((key) => ({x: parseInt(key), y: predictions[key]})),
                    borderColor: 'blue',
                    showLine: true,
                    fill: false
                },
                {
                    label: 'Actual MPG',
                    data: actualData.x.map((value, index) => ({x: value, y: actualData.y[index]})),
                    borderColor: 'red',
                    backgroundColor: 'rgba(255, 0, 0, 0.5)',
                    pointRadius: 3,
                    fill: false
                }
            ]
        },
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Horsepower'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'MPG'
                    }
                }
            }
        }
    });
</script>

</body>
</html>
