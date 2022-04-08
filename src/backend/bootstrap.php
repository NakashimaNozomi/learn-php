<?php

namespace App;

require_once './vendor/autoload.php';

use \Illuminate\Database\Capsule\Manager as Capsule;

try {
    $capsule = new Capsule();

    // TODO: 接続情報は外だしがベスト！
    $capsule->addConnection([
        'driver' => 'mysql',
        'host' => $_ENV['DB_HOST'],
        'database' => $_ENV['DB_DATABASE'],
        'username' => $_ENV['DB_USERNAME'],
        'password' => $_ENV['DB_PASSWORD'],
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ]);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
} catch (\Exception $e) {
    // DB接続エラー
    echo '{error: "error: '.$e->getMessage(). '"}';
    exit();
}
