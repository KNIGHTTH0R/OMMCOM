<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdvertisementSection extends Model
{
    Protected $primaryKey = "id";
	protected $fillable = array('name', 'advertisement_type_id', 'is_enable');
}
