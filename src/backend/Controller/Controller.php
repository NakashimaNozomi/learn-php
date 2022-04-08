<?php
namespace App\Controller;

class Controller
{
    protected static function jsonResponse(mixed $json, ?int $status_code = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status_code);
        echo json_encode($json);
    }
}
