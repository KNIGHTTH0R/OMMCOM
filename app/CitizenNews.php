<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CitizenNews extends Model
{
    Protected $primaryKey = "id";
	protected $fillable = array('name','email','file_path','file_type','description','user_id','is_enable','is_trash', 'position','slug','long_description','is_anonymous');
	
	public static function citizenNews(){
        $citizenNews = CitizenNews::select('id','name','file_path','file_type','description','slug')
        ->where(['is_enable'=>1,'is_trash'=>0])
        ->OrderBy('updated_at','DESC')
        ->OrderBy('position','ASC')
        ->take(20)->get();
        return $citizenNews;
	}
}
