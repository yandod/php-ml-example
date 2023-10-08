<?php
function fetch_data() {
    $url = "auto-mpg.data";
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

function predict($weights, $horsepower) {
    return $weights[0] * $horsepower + $weights[1];
}

function gradient_decent($dataSet) {
    // データセットからhorsepowerとmpgを取得
    $horsepower = array_column($dataSet, 'Horsepower');
    $mpg = array_column($dataSet, 'MPG');

    // ハイパーパラメータ設定
    $initial_learning_rate = 0.000045; // 初期の学習率を大きく設定
    $final_learning_rate = 0.0000001; // 最終的な学習率
    $epochs = 3000;
    $weights = [0, 0];

    for ($epoch = 0; $epoch < $epochs; $epoch++) {
        // 線形に学習率を減少させる
        $learning_rate = $initial_learning_rate - ($initial_learning_rate - $final_learning_rate) * ($epoch / $epochs);

        $total_mse = 0; // MSE計算のための合計エラー
        $total_mae = 0;

        for ($i = 0; $i < count($horsepower); $i++) {
            $predicted = predict($weights, $horsepower[$i]); //推論
            $error = $mpg[$i] - $predicted;
            $total_mse += $error * $error; // 二乗誤差の合計を計算
            $total_mae += abs($error); // 絶対誤差の合計を計算

            // 重みを更新
            $weights[0] -= $learning_rate * (-2 * $horsepower[$i] * $error);
            $weights[1] -= $learning_rate * (-2 * $error);
        }
        $mse = $total_mse / count($horsepower); // 平均二乗誤差を計算
        $mae = $total_mae / count($horsepower); // 平均絶対誤差を計算

        // 必要に応じて各エポックのMSEと学習率を表示
        // echo "Epoch {$epoch}: MSE = {$mse}, Learning rate = {$learning_rate}\n";
    }

    return [
        'slope' => $weights[0],
        'intercept' => $weights[1],
        'weights' => $weights,
        'mse' => $mse,
        'mae' => $mae,
    ];
}

$dataSet = fetch_data();
$regression = gradient_decent($dataSet);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Line Chart using PHP and Chart.js</title>
    <script src="./chart.js"></script>
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
                backgroundColor: 'rgba(75, 192, 192, 0.9)',
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

    // 回帰線のデータを計算
    var regressionData = dataSet.map(data => {
        return {
            x: data.Horsepower,
            y: <?php echo $regression['slope']; ?> * data.Horsepower + <?php echo $regression['intercept']; ?>
        };
    });

    myChart.data.datasets.push({
        label: 'Regression Line',
        data: regressionData,
        borderColor: 'rgba(255, 0, 0, 1)',
        fill: false,
        pointRadius: 0,  // ポイントの表示を無効化
        borderWidth: 2,
        type: 'line'
    });

    myChart.update();
</script>

Slope: <?php echo $regression['slope']; ?><br>
Intercept: <?php echo $regression['intercept']; ?><br>
MSE: <?php echo $regression['mse'] ?><br>
MAE: <?php echo $regression['mae'] ?>
</body>
</html>
