<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
	Protected $primaryKey = "id";
	protected $fillable = array('name', 'mail_id','contact_number','city','zip','address_line1','address_line_2');
}
