<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopNews extends Model
{
	Protected $primaryKey = "id";
	protected $fillable = array('news_id', 'position');
}
