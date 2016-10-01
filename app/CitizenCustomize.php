<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CitizenCustomize extends Model
{
	protected $table = 'citizen_customizes';
	Protected $primaryKey = "id";
	protected $fillable = array('name','file_path','user_id','is_enable','is_trash');
	
	public static function citizenCustomizes(){
        $citizenCustomize = CitizenCustomize::select('name','file_path')
        ->where(['is_enable'=>1,'is_trash'=>0])->OrderBy('id','desc')->first();	
        return $citizenCustomize;	
	}
}