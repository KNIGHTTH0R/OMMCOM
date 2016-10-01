<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
	Protected $primaryKey = "id";
	protected $fillable = array('os_type', 'version', 'code_name', 'major', 'is_enable', 'is_trash');
}
