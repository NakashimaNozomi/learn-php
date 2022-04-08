<?php
namespace App\Util;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Query\Builder;

// クエリビルダーを使ってみる
class DB
{
    private ?Capsule $capsule = null;
    private ?Builder $lastQueryBuilder = null;

    // DBへのコネクションを貼る
    public function __construct() //コンストラクタ newしたら呼び出される
    {
        $this->capsule = new Capsule();

        // TODO: 接続情報は外だしがベスト！
        $this->capsule->addConnection([
            'driver' => 'mysql',
            'host' => $_ENV['DB_HOST'],
            'database' => $_ENV['DB_DATABASE'],
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ]);
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
    }

    // SQLもこのファイルに集約したい
    public function fetchAll() :array
    {
        $this->lastQueryBuilder = Capsule::table('mytable')->OrderByDesc('created_at');
        $sql = $this->lastQueryBuilder->toSql(); //SQLを見たい場合はここで止めてみる
        return $this->lastQueryBuilder->get()->toArray();
    }
}
