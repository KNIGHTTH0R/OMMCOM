<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    Protected $primaryKey = "id";
	protected $fillable = array('name','email','message','mobile','is_trash');
}
