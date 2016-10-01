<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConferencesUser extends Model
{
	Protected $primaryKey = "id";
	protected $fillable = array('name','stream','description', 'conference_id','user_id','user_type','in_time','out_time','duration','stream_url');
}
