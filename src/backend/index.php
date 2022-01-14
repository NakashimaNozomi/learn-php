<?php
require_once './whispers.php';

$url = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET' && preg_match('/^\/whispers\/?$/', $url)) {
    getWhisperList();
} elseif ($method === 'POST' && preg_match('/^\/whisper\/?$/', $url)) {
    postWhisper();
} elseif ($method === 'PUT' && preg_match('/^\/whisper\/([1-9]+[0-9]*)\/?$/', $url, $matched)) {
    putWhisper($matched[1]);
} elseif ($method === 'DELETE' && preg_match('/^\/whisper\/([1-9]+[0-9]*)\/?$/', $url, $matched)) {
    deleteWhisper($matched[1]);
} else {
    // 404 jsonレスポンス
    header('HTTP/1.1 404 Not Found');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('error' => '404 Not Found'));
}
