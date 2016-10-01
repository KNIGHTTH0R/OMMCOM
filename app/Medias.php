<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Medias extends Model
{
	Protected $primaryKey = "id";
	protected $fillable = array('name', 'short_desc','long_desc');
}
