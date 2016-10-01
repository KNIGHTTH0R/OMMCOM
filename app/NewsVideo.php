<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsVideo extends Model
{
    protected $table = 'news_videos';
	Protected $primaryKey = "id";
	protected $fillable = array('name', 'is_video', 'is_audio', 'file_link','position','news_id');
}
