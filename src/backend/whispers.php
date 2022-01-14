<?php

function getWhisperList(): void
{
    $dsn = 'mysql:dbname=' . $_ENV['DB_DATABASE'] . ';host=' . $_ENV['DB_HOST'];
    $user = $_ENV['DB_USERNAME'];
    $password = $_ENV['DB_PASSWORD'];

    try {
        $dbh = new PDO($dsn, $user, $password);
    } catch (PDOException $e) {
        // DB接続エラー時
        echo '接続失敗: ' . $e->getMessage();
        exit();
    }

    $sql = 'SELECT id, content, created_at FROM mytable ORDER BY created_at DESC LIMIT 20;';
    $prepare = $dbh->prepare($sql);
    $prepare->execute();
    $whisper_list = $prepare->fetchAll(PDO::FETCH_ASSOC);
    $dbh = null; // DBの接続は必ず閉じる

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($whisper_list);
}

function postWhisper(): void
{
    header('Content-Type: application/json; charset=utf-8');
    $post_param = json_decode(file_get_contents('php://input'), true);

    //content 空ではない>配列ではない>content keyが存在しない
    if (
        !$post_param || !is_array($post_param)
        || !array_key_exists('content', $post_param)
        || !$post_param['content']
    ) {
        http_response_code('400');
        echo json_encode(array('error' => '不正なリクエストです'));
        exit();
    }

    $dsn = 'mysql:dbname=' . $_ENV['DB_DATABASE'] . ';host=' . $_ENV['DB_HOST'];
    $user = $_ENV['DB_USERNAME'];
    $password = $_ENV['DB_PASSWORD'];

    try {
        $dbh = new PDO($dsn, $user, $password);
    } catch (PDOException $e) {
        // DB接続エラー時
        echo '接続失敗: ' . $e->getMessage();
        exit();
    }

    $sql = 'INSERT INTO mytable(content) VALUES(?)';
    $prepare = $dbh->prepare($sql);
    $prepare->execute(array($post_param['content']));

    $select_sql = 'SELECT id, content, created_at FROM mytable WHERE id = ?';
    $select_prepare = $dbh->prepare($select_sql);
    $select_prepare->execute(array($dbh->lastInsertId()));
    $inserted = $select_prepare->fetch(PDO::FETCH_ASSOC);
    $dbh = null; // DBの接続は必ず閉じる

    http_response_code('201');
    echo json_encode($inserted);
}

function putWhisper(int $id): void
{
    header('Content-Type: application/json; charset=utf-8');
    $put_param = json_decode(file_get_contents('php://input'), true);

    if (
        !$put_param || !is_array($put_param)
        || !array_key_exists('content', $put_param)
        || !$put_param['content']
    ) {
        http_response_code('400');
        echo json_encode(array('error' => '不正なリクエストです'));
        exit();
    }

    $dbh = connect_database();
    if (is_string($dbh)) {
        http_response_code('500');
        echo json_encode(array('error' => 'サーバエラーです'));
        exit();
    }

    $sql = 'UPDATE mytable SET content = ? WHERE id = ?';
    $prepare = $dbh->prepare($sql);
    $prepare->execute(array($put_param['content'], $id));

    if ($prepare->rowCount() === 0) {
        http_response_code('400');
        echo json_encode(array("error" => "不正なリクエスト。idの指定が間違っているか、同じ値での更新です。"));
        return;
    }

    $select_sql = 'SELECT id, content, created_at FROM mytable WHERE id = ?';
    $select_prepare = $dbh->prepare($select_sql);
    $select_prepare->execute(array($id));
    $updated = $select_prepare->fetch(PDO::FETCH_ASSOC);
    $dbh = null; // DBの接続は必ず閉じる

    echo json_encode($updated);
}

function deleteWhisper(int $id): void
{
    header('Content-Type: application/json; charset=utf-8');

    $dbh = connect_database();
    if (is_string($dbh)) {
        http_response_code('500');
        echo json_encode(array('error' => 'サーバエラーです'));
        exit();
    }

    $delete_sql = 'DELETE FROM `mytable` WHERE `id` = ?';
    $prepare = $dbh->prepare($delete_sql);
    $prepare->execute(array($id));

    if ($prepare->rowCount() === 0) {
        http_response_code('400');
        echo json_encode(array("error" => "不正なリクエスト。idの指定が間違っているか、同じ値での更新です。"));
        return;
    }
    $dbh = null; // DBの接続は必ず閉じる

    http_response_code('204');
}

function connect_database(): PDO|string
{
    $dsn = 'mysql:dbname=' . $_ENV['DB_DATABASE'] . ';host=' . $_ENV['DB_HOST'];
    $user = $_ENV['DB_USERNAME'];
    $password = $_ENV['DB_PASSWORD'];

    try {
        $dbh = new PDO($dsn, $user, $password);
    } catch (PDOException $e) {
        // DB接続エラー時
        return '接続失敗: ' . $e->getMessage();
    }
    return $dbh;
}
