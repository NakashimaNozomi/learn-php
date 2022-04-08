<?php
namespace Util;

class DB
{
    private \PDO|null $dbh = null;

    // DBへのコネクションを貼る
    public function __construct() //コンストラクタ newしたら呼び出される
    {
        $dsn = 'mysql:dbname=' . $_ENV['DB_DATABASE'] . ';host=' . $_ENV['DB_HOST'];
        $user = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];

        try {
            $this->dbh = new \PDO($dsn, $user, $password);
        } catch (\PDOException $e) {
            // DB接続エラー時
            echo '接続失敗: ' . $e->getMessage();
            exit();
        }
    }

    public function __destruct()
    {
        $this->dbh = null; //DBの接続は破棄する
    }

    // SQLもこのファイルに集約したい
    public function fetchAll() :array
    {
        $sql = 'SELECT id, content, created_at FROM mytable ORDER BY created_at DESC LIMIT 20;';
        $prepare = $this->dbh->prepare($sql);
        $prepare->execute();
        return $prepare->fetchAll(\PDO::FETCH_ASSOC);
    }


}
