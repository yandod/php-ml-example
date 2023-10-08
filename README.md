# php-ml-example

PHPで機械学習の基礎、勾配降下法を体験する為のサンプルコード集です。

題材はTensorFlowの[線形回帰のチュートリアル](https://www.tensorflow.org/tutorials/keras/regression?hl=ja)をPHPで進める形なっています。

# 使い方
- 起動
```
docker compose up
```
- アクセス
  - テストデータの表示
    - http://127.0.0.1:8080/step1.php 
  - 最小二乗法を用いた線形回帰
    - http://127.0.0.1:8080/step2.php
  - 勾配降下法を用いた線形回帰
    - http://127.0.0.1:8080/step3.php
  - ニューラルネットワークを用いた非線形回帰 
    - http://127.0.0.1:8080/step4.php
  - 上記に誤差情報を追加
    - http://127.0.0.1:8080/step5.php

# 理解を深める為の次の一歩

## ハイパーパラメーターを変更して性能を上げる

### step3.php
下記の設定でのMAEは4.1284024634384
- 学習率、エポック数、初期値などの変更で学習の進み方が変わる。
```
// ハイパーパラメータ設定
$initial_learning_rate = 0.000045; // 初期の学習率を大きく設定
$final_learning_rate = 0.0000001; // 最終的な学習率
$epochs = 3000;
$weights = [0, 0];
```

### step5.php
下記の設定でのMAEは3.3915150225828
- Denseのノード数、optimizer (学習率) epoch(エポック)などの変更で挙動が変化する。
```
$estimator = new MLPRegressor([
    new Dense(64),
    new Activation(new ReLU()),
    new Dense(64),
    new Activation(new ReLU()),
],
    64, // batch size
    new Adam(0.001), // optimizer
    1e-4, // Penalty
    100, // epochs    
);
```

# ローカルで実行できるように同梱したデータ

- auto-mpg.data by R. Quinlan [[original](https://archive.ics.uci.edu/dataset/9/auto+mpg)]
- [chart.js](https://www.chartjs.org/)
