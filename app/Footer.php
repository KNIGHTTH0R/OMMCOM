<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    Protected $primaryKey = "id";
	protected $fillable = array('address','email','mobile_no','map_url','copyright');
    public static function footerDetails(){
        $footer = Footer::select('address','email','mobile_no','map_url','copyright')->first();
        return $footer;        
    }	
}