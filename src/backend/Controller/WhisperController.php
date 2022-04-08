<?php
namespace App\Controller;

use App\Controller\Controller;
use App\Model\Whisper;


class WhisperController extends Controller
{
    public static function list() :void
    {
        $whisper_list = Whisper::orderByDesc('created_at')->get()->toArray();

        self::jsonResponse($whisper_list);
    }

    public static function add() :void
    {
        $post_param = json_decode(file_get_contents('php://input'), true);

        //content 空ではない>配列ではない>content keyが存在しない
        if (
            !$post_param || !is_array($post_param)
            || !array_key_exists('content', $post_param)
            || !$post_param['content']
        ) {
            self::jsonResponse(array('error' => '不正なリクエストです'), 400);
            exit();
        }

        $inserted = Whisper::create([
            'content' => $post_param['content'],
        ]);

        self::jsonResponse($inserted, 201);

    }
}
