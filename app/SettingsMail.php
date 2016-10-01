<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class SettingsMail extends Model
{
	Protected $primaryKey = "id";
	protected $fillable = array('from_mail_id', 'from_name','password','smtp_port');
}
