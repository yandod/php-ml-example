<?php
function fetch_data() {
    $url = "http://archive.ics.uci.edu/ml/machine-learning-databases/auto-mpg/auto-mpg.data";
    $data = file_get_contents($url);
    $lines = explode("\n", trim($data));

    $dataSet = [];
    foreach ($lines as $line) {
        $items = preg_split('/\s+/', trim($line));
        if (count($items) >= 4) {
            // Horsepowerが"?"の場合、その行をスキップします
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Line Chart using PHP and Chart.js</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<canvas id="myChart" width="400" height="200"></canvas>

<script>
    var dataSet = <?php echo json_encode($dataSet, JSON_NUMERIC_CHECK); ?>;

    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'scatter',  // scatter plotを使用
        data: {
            datasets: [{
                label: 'MPG by Horsepower',
                data: dataSet.map(data => ({x: data.Horsepower, y: data.MPG})),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Horsepower'
                    }
                },
                y: {
                    beginAtZero: true,
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
