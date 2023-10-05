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


# ローカルで実行できるように同梱したデータ

- auto-mpg.data by R. Quinlan [[original](https://archive.ics.uci.edu/dataset/9/auto+mpg)]
- [chart.js](https://www.chartjs.org/)
