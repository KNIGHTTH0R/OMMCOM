<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\RegisteredDevice;
class Conference extends Model
{
	Protected $primaryKey = "id";
	protected $fillable = array('name','short_desc','long_desc','is_enable','started_at','start_time','end_time','featured_image','conference_banner','slug','announcement');
    
    public static function pushNotification($news){
    	if($news != ''){
    		$registerIds = RegisteredDevice::where('notification',1)->lists('uid')->toArray();
    		$result = RegisteredDevice::send_notification($registerIds, $news);
    		return $result;
    	}
    }	
}
