<?php
namespace App\Model; // ディレクトリ構造と一致

use Illuminate\Database\Eloquent\Model;

class Whisper extends Model // ファイル名と一致
{
    protected $table = 'mytable';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = ['content'];
}
