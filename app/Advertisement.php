<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    //protected $table = 'advertisement';
    Protected $primaryKey = "id";
	protected $fillable = array('name', 'sponsor_id', 'is_publish', 'start_date', 'end_date', 'is_enable', 'file_type', 'file_path', 'publish_date','advertisement_type_id','advertisement_section_id', 'cat_id','is_url','url_link','priority');
	
	public static function getadvertisement($advertisement_type_id,$advertisement_section_id){
		$newscatdata = Advertisement::where(['is_enable'=>1,'is_publish'=>1,'advertisement_type_id'=>$advertisement_type_id,'advertisement_section_id'=>$advertisement_section_id])
		->where('end_date', '>=', date('Y-m-d'))
		->first();
		return $newscatdata;
	}
	public static function getadvtdetails($advertisement_type_id,$advertisement_section_id){
		$newscatdata = Advertisement::where(['is_enable'=>1,'is_publish'=>1,'file_type'=>'Image','advertisement_type_id'=>$advertisement_type_id,'advertisement_section_id'=>$advertisement_section_id])
		->where('end_date', '>=', date('Y-m-d'))
		->take(4)->get();
		return $newscatdata;
	}
    public function AdvertisementSection() {
        return $this->belongsTo('App\AdvertisementSection','advertisement_section_id');
    } 	
}