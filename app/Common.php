<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\NewsCategory;
class Common extends Model
{
	/**
	 * [date2DB description]
	 * @param  [type] $dt     [description]
	 * @param  string $format [description]
	 * @return [type]         [description]
	 */
    function date2DB($dt=null,$format='-'){
        if($dt){
            $dateArray = explode($format,$dt);
            if(count($dateArray) > 1){    
                $formatedDate = $dateArray[2].'-'.$dateArray[1].'-'.$dateArray[0];
                return $formatedDate;
            }else{
                return '0000-00-00';
            }
        }else{
            return '0000-00-00';
        }    
    }
    /**
     * [DB2date description]
     * @param [type] $dt [description]
     */
    function DB2date($dt){
        if($dt != '' && $dt != '0000-00-00' && $dt != '1900-01-01' && $dt != '1970-01-01'){
            $formatedDate = date('d-m-Y',strtotime($dt));
            return $formatedDate;
        }    
    }
    /**
     * [getUserDetails description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    function getUserDetails($id){
        $data = array();
        if($id){
            $data = User::find($id);
            return $data;
        }else{
            return $data;
        }
    }
 
    function dateDifference($date){
        $date1  = $date;
        $date2  = date('Y-m-d H:i:s');
        $diff   = abs(strtotime($date2) - strtotime($date1));
        $years  = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        $hours  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));
        $mins   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ (60));
        $secs   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $mins*60));
        if($years){
            if($years == 1){
                return $years.' year';
            }else{
                return $years.' years'; 
            }
        }else if($months){
            if($months == 1){
                return $months.' month';
            }else{
                return $months.' months';
            }
        }else if($days){
            if($days == 1){
                return $days.' day';
            }else{
                return $days.' days';
            }
        }else if($hours){
            if($hours == 1){
                return $hours.' hour';
            }else{
                return $hours.' hours';
            }
        }else if($mins){
            if($mins == 1){
                return $mins.' minute';
            }else{
                return $mins.' minutes';
            }
        }else if($secs){
            if($secs == 1){
                return $secs.' second';
            }else{
                return $secs.' seconds';
            }
        }
        //printf("%d years, %d months, %d days, %d hour, %d mins, %d sec", $years, $months, $days,$hours,$mins,$secs);        
    } 
    public static function get_client_ip()
    {
          $ipaddress = '';
          if (getenv('HTTP_CLIENT_IP'))
              $ipaddress = getenv('HTTP_CLIENT_IP');
          else if(getenv('HTTP_X_FORWARDED_FOR'))
              $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
          else if(getenv('HTTP_X_FORWARDED'))
              $ipaddress = getenv('HTTP_X_FORWARDED');
          else if(getenv('HTTP_FORWARDED_FOR'))
              $ipaddress = getenv('HTTP_FORWARDED_FOR');
          else if(getenv('HTTP_FORWARDED'))
              $ipaddress = getenv('HTTP_FORWARDED');
          else if(getenv('REMOTE_ADDR'))
              $ipaddress = getenv('REMOTE_ADDR');
          else
              $ipaddress = 'UNKNOWN';

          return $ipaddress;
     }
    public static function newsCategoryDetails($id=0){
        if($id){
            return NewsCategory::find($id);
        }else{
            return '';
        }
    }
    public static function getMac(){
        // Turn on output buffering  
        //ob_start();  
        //Get the ipconfig details using system commond  
        system('ipconfig /all'); 
        // Capture the output into a variable  
        $mycomsys=ob_get_contents();  
        // Clean (erase) the output buffer  
        //ob_clean();  
        $find_mac = "Physical"; 
        //find the "Physical" & Find the position of Physical text  
        $pmac = strpos($mycomsys, $find_mac);  
        // Get Physical Address  
        $macaddress=substr($mycomsys,($pmac+36),17);  
        //Display Mac Address  
        
        if($macaddress != ''){
            return $macaddress;
        } else{
            exec('netstat -ie', $result);
            if(is_array($result)) {
                $iface = array();
                foreach($result as $key => $line) {
                    if($key > 0) {
                        $tmp = str_replace(" ", "", substr($line, 0, 10));
                        if($tmp <> "") {
                            $macpos = strpos($line, "HWaddr");
                            if($macpos !== false) {
                                $iface[] = array('iface' => $tmp, 'mac' => strtolower(substr($line, $macpos+7, 17)));
                            }
                        }
                    }
                }
                return $iface[0]['mac'];
            }else{
                return '';
            }        
        }
    }
    public static function seoUrl($string) {
        //Lower case everything
        $string = strtolower($string);
        //Make alphanumeric (removes all other characters)
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        //Clean up multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        //Convert whitespaces and underscore to dash
        $string = preg_replace("/[\s_]/", "-", $string);
        return $string;
    }            
}
