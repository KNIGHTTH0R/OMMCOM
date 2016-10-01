<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\RegisteredDevice;
class BreakingNews extends Model
{
    Protected $primaryKey = "id";
	protected $fillable = array('title', 'position', 'is_enable', 'is_trash');

    public static function breakingNews(){
        $curDate = date('Y-m-d H:i:s');
        $beforeDate = date('Y-m-d H:i:s', strtotime('-2 hour'));
        $breakingNews = BreakingNews::select('id','title')
        //->whereRaw('date(created_at) = ?', [date('Y-m-d')])
        //->whereBetween('created_at', [$curDate, $beforeDate])
        ->where('updated_at', '<=', $curDate)
        ->where('updated_at', '>=', $beforeDate)
        ->where(['is_trash'=>0,'is_enable'=>1])
        ->OrderBy('position','ASC')->get();
        return $breakingNews;
    }
    public static function newsUpdate(){
        $curDate = date('Y-m-d H:i:s');
        $beforeDate = date('Y-m-d H:i:s', strtotime('-24 hour'));
        $breakingNews = BreakingNews::select('id','title')
        ->where('updated_at', '<=', $curDate)
        ->where('updated_at', '>=', $beforeDate)
        ->where(['is_trash'=>0,'is_enable'=>1])
        ->OrderBy('position','ASC')->get();
        return $breakingNews;        
    }
    public static function pushNotification($news){
    	if($news != ''){
    		$registerIds = RegisteredDevice::where('notification',1)->lists('uid')->toArray();
    		$result = RegisteredDevice::send_notification($registerIds, '1'.$news);
    	}
    }	
}
