<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegisteredDevice extends Model
{
	Protected $primaryKey = "id";
	protected $fillable = array('uid', 'email', 'notification');
	/*
	 *Query for get the notification to mobile
	 */
    public static function send_notification($registatoin_ids, $message) {
        // Set POST variables
        //$jsonArr = array('message'=>$message);
        $url = 'https://android.googleapis.com/gcm/send';
        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => ['message'=>$message],
        );
        $headers = array(
            'Authorization: key=' . \Config::get('constants.GCM_API_KEY'),
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        return $result;
    }	
}
