<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdvertisementType extends Model
{
    Protected $primaryKey = "id";
	protected $fillable = array('name', 'is_enable');
	
}
