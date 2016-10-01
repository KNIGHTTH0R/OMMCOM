<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsTopVideo extends Model
{
    protected $table = 'news_top_videos';
	Protected $primaryKey = "id";
	protected $fillable = array('name', 'video_file', 'is_enable');
}
