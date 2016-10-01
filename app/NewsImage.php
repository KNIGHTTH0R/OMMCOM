<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsImage extends Model
{
    protected $table = 'news_images';
	Protected $primaryKey = "id";
	protected $fillable = array('name', 'description', 'news_id', 'position');
}
