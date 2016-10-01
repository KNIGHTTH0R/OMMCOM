<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common;
use App\Conference;
use App\ConferencesUser;
use App\CitizenNews;
use App\Http\Requests\CreateConferenceFormRequest;
use App\Http\Requests\CreateConferenceUserFormRequest;
class ConferenceController extends Controller{
    public function create(){
        $conference = new Conference();
        return view('conference.create',['conference'=>$conference,'title'=>'Add Conference']);
    }
    public function store(CreateConferenceFormRequest $request){
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $conference = Conference::find($data['id']);
        }else{
            $conference = new Conference();
            $conference->is_enable      = 1;
            $conference->is_archive     = 0;
            $conference->is_trash       = 0;
            $conference->is_close       = 0;
        }
        $common = new Common();
        if(isset($data['started_at']) && $data['started_at'] != ''){
            $data['started_at'] = $common->date2DB($data['started_at']);
            $data['started_at'] = $data['started_at'].' '.date('H:m:s');
        }       
        $conference->created_by     = \Session::get('user_id');
        $resizeImage = \Config::get('constants.RESIZE_IMAGE');
        $filePath = public_path().'/file/conference/';
        if($request->hasFile('featured_image')) {
            $file = \Input::file('featured_image');
            $timestamp = rand().time().rand();
            $name = $timestamp. '-' .$file->getClientOriginalName(); 
            $conference->featured_image = $name;
            $data['featured_image']     = $name;
            $file->move($filePath.'original/', $name);

            if(is_array($resizeImage) && count($resizeImage)>0){
                foreach($resizeImage as $resizeKey=>$resizeVal){
                    if(!is_dir($filePath.$resizeKey)){
                        mkdir($filePath.$resizeKey,0777);
                    }
                    $resizeValArr = explode(',',$resizeVal);
                    \File::copy($filePath.'original/'.$name, $filePath.$resizeKey.'/'.$name);
                    \Image::make($filePath.$resizeKey.'/'.$name)->resize($resizeValArr[0],$resizeValArr[1])->save($filePath.$resizeKey.'/'.$name);
                }
            }                                 
        }
        if($request->hasFile('conference_banner')) {
            $banner = \Input::file('conference_banner');
            $timestamp = rand().time().rand();
            $name = $timestamp. '-' .$banner->getClientOriginalName(); 
            $conference->conference_banner = $name;
            $data['conference_banner']     = $name;
            $banner->move($filePath.'original/', $name);  
            if(is_array($resizeImage) && count($resizeImage)>0){
                foreach($resizeImage as $resizeKey=>$resizeVal){
                    if(!is_dir($filePath.$resizeKey)){
                        mkdir($filePath.$resizeKey,0777);
                    }
                    $resizeValArr = explode(',',$resizeVal);
                    \File::copy($filePath.'original/'.$name, $filePath.$resizeKey.'/'.$name);
                    \Image::make($filePath.$resizeKey.'/'.$name)->resize($resizeValArr[0],$resizeValArr[1])->save($filePath.$resizeKey.'/'.$name);
                }
            }                                  
        }                  
        $conference->fill($data)->save();
        return redirect('conference/list');
    }
    public function conferencelist(){ 
        $datas = DB::table('conferences')->where('is_trash',0)->OrderBy('id', 'desc')->paginate(15);
        return view('conference.conferencelist',['title'=>'Conference List','datas'=>$datas]);      
    }
    public function edit($id){
        $common = new Common();
        $conference = Conference::findOrFail($id);
        if(isset($conference->started_at) && $conference->started_at != ''){
            $conference->started_at = $common->DB2date($conference->started_at);
        }
        if(isset($conference->closed_at) && $conference->closed_at != ''){
            $conference->closed_at = $common->DB2date($conference->closed_at);
        }        
        return view('conference.create', ['conference' => $conference,'title'=>'Update Conference' ]);              
    }   
    public function delete($id){
        $conference = Conference::find($id);
        $conference->is_trash = 1;
        $conference->save();
        return redirect('conference/list');      
    }   

    public function action($id){
        $conference = Conference::find($id);
        if(isset($conference->is_enable) && $conference->is_enable == 1){
            $conference->is_enable = 0;
        }else{
            $conference->is_enable = 1;
        }

        $conference->save();
        return redirect('conference/list'); 
    }
    public function adduser($baseid){
        $id = base64_decode($baseid);
        if($id){
            $conferencesUser = new ConferencesUser();
            $conferencesUser->conference_id = $id; 
            $datas = DB::table('conferences_users')->where(['conference_id'=>$id,'is_trash'=>0])->OrderBy('id', 'asc')->paginate(15);
            return view('conference.adduser',['conferencesUser'=>$conferencesUser,'datas'=>$datas,'title'=>'Add Conference User']);
        }else{
            return redirect('conference/list'); 
        }
    }
    public function storeuser(CreateConferenceUserFormRequest $request){
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $conferencesUser = ConferencesUser::find($data['id']);
        }else{
            $conferencesUser = new ConferencesUser();
            $conferencesUser->stream = $data['conference_id'].time();
        }
        $conferencesUser->user_id     = \Session::get('user_id');
        $conference_id = $data['conference_id'];
        $conferencesUser->fill($data)->save();
        return redirect('conference/adduser/'.base64_encode($conference_id));
    } 
    public function edituser($id){
        $conferencesUser = ConferencesUser::findOrFail($id);
        $datas = DB::table('conferences_users')->where(['conference_id'=>$conferencesUser->conference_id,'is_trash'=>0])->OrderBy('id', 'asc')->paginate(15);
        return view('conference.adduser',['conferencesUser'=>$conferencesUser,'datas'=>$datas,'title'=>'Update Conference User']);
    }   
    public function durationindate(Request $request){
        $data = $request->all();
        if($data['started_at'] != '' && $data['closed_at'] != ''){
            $common = new Common();
            $started_at = $common->date2DB($data['started_at']);
            $closed_at = $common->date2DB($data['closed_at']);
            $duration = round(abs(strtotime($started_at)-strtotime($closed_at))/86400);
            echo $duration;
        }else{
            echo '';
        }         
    }
    public function conference($slug){
        $conference = Conference::select(['id','name','is_start','slug'])->where(['is_close'=>0,'is_trash'=>0,'is_enable'=>1,'slug'=>$slug])->first();
        $archivevideos = CitizenNews::select('id','name','file_path','description','updated_at')->where(['file_type'=>'Video','is_enable'=>1,'is_trash'=>0])
        ->OrderBy('news_count','DESC')->take(10)->get();
        return view('conference.conference',['title'=>'Conference','conference'=>$conference,'archivevideos'=>$archivevideos]);
    }
    public function saveuserstreamurl(Request $request){
    	$data = $request->all();
    	//if($data['conference_id'] && \Session::get('social_user_id')){
        if($data['conference_id'] && $data['name']){
            if($data['stream']){
                echo $data['stream'];
            }else{
                $conferencesUser = new ConferencesUser();
    	    	$conferencesUser->name          = $data['name'];
                $conferencesUser->description   = $data['description'];
                $conferencesUser->stream        = $data['conference_id'].time();
                $conferencesUser->user_type     = \Config::get('constants.VIEWER_USER');
    	    	$conferencesUser->in_time 		= date('Y-m-d H:i:s');
    	    	$conferencesUser->is_enable 	= 0;
                $conferencesUser->user_id       = 1;
                $data['stream'] = $conferencesUser->stream;
                //$conferencesUser->name          = \Session::get('social_name');
                //$conferencesUser->user_id       = \Session::get('social_user_id');                

    	    	if($conferencesUser->fill($data)->save()){
                    $viewercount = ConferencesUser::count();
    	    		echo $conferencesUser->stream.'*****'.$viewercount;
    			}else{
    				echo 'FAIL';
    			}
            }
		}else{
			echo 'FAIL';
		}
    }
    public function liveconferenceuser(Request $request){
        $params = $request->all(); 
        $data = array();
        if(isset($params['conference_id']) && $params['conference_id'] != 0){
            $conference_id = $params['conference_id'];
        	$confs = ConferencesUser::where('conference_id',$conference_id)->get();
    		$cnt = 0;
        	foreach($confs as $conf){
        		$data[$cnt]['id'] 				= $conf->id;
                $data[$cnt]['conference_id']    = $conf->conference_id;
        		$data[$cnt]['name'] 			= $conf->name;
                $data[$cnt]['stream']           = $conf->stream;
                $data[$cnt]['description']      = $conf->description;
                $data[$cnt]['user_type']        = \Config::get('constants.CONFERENCE_USER')[$conf->user_type];
                $data[$cnt]['user_type_id']     = $conf->user_type;
        		$data[$cnt]['is_trash'] 		= $conf->is_trash;
        		$data[$cnt]['is_enable'] 		= $conf->is_enable;
        		$data[$cnt]['is_fullscreen'] 	= $conf->is_fullscreen;
        		$data[$cnt]['is_mute'] 			= $conf->is_mute; 
        		$data[$cnt]['is_main_screen'] 	= $conf->is_main_screen;  
                $data[$cnt]['is_remove']        = $conf->is_remove;  		
        		$cnt++;
        	}
        }
        if(is_array($data) && count($data)>0){
    	   return json_encode($data);
        }else{
            echo 0;
        }
    }
    public function videoconferenceusers(){
        $confList = [''=>'--Select Conference--'] +Conference::where(['is_enable'=>1,'is_trash'=>0,'is_close'=>0])->lists('name', 'id')->toArray();
    	return view('conference.videoconferenceusers',['title'=>'Video Conference Users','confList'=>$confList]); 
    }
    public function videoenable(Request $request){
    	$data = $request->all();
    	if($data['id']){
    		$conferenceuser = ConferencesUser::find($data['id']);
    		$conferenceuser->is_enable = $data['is_enable'];
	    	if($conferenceuser->save()){
	    		echo 'SUCC';
			}else{
				echo 'FAIL';
			}
		}else{
			echo 'FAIL';
		}    	
    }
    public function videoblack(Request $request){
    	$data = $request->all();
    	if($data['id']){
    		$conferenceuser = ConferencesUser::find($data['id']);
    		$conferenceuser->is_trash = $data['is_trash'];
	    	if($conferenceuser->save()){
	    		echo 'SUCC';
			}else{
				echo 'FAIL';
			}
		}else{
			echo 'FAIL';
		}    	
    }  
    public function videoscreen(Request $request){
    	$data = $request->all();
    	if($data['id']){
    		$conferenceuser = ConferencesUser::find($data['id']);
    		if($data['param'] == 'F'){
    			$conferenceuser->is_fullscreen = $data['flagid'];
    		}else{
    			$conferenceuser->is_mute = $data['flagid'];
    		}
	    	if($conferenceuser->save()){
	    		echo 'SUCC';
			}else{
				echo 'FAIL';
			}    		
    	}else{
    		echo 'FAIL';
    	}
    } 
    public function videomainscreen(Request $request){
    	$data = $request->all();
    	if($data['id'] && $data['conference_id']){
    		$conferenceuser = ConferencesUser::find($data['id']);
    		$conferenceuser->is_main_screen = $data['is_main_screen'];
	    	if($conferenceuser->save()){
                DB::table('conferences_users')->where('id','!=', $data['id'])->where('conference_id',$data['conference_id'])->update(['is_main_screen' => 0]);                
	    		echo 'SUCC';
			}else{
				echo 'FAIL';
			}
		}else{
			echo 'FAIL';
		}    	
    } 
    public function removeconferenceuser(Request $request){
        $data = $request->all();
        if($data['id'] && $data['conference_id']){
            $conferenceuser = ConferencesUser::find($data['id']);
            $conferenceuser->is_remove      = $data['is_remove'];
            $conferenceuser->is_enable      = 0;
            $conferenceuser->is_main_screen = 0;
            if($conferenceuser->save()){
                echo 'SUCC';
            }else{
                echo 'FAIL';
            }
        }else{
            echo 'FAIL';
        }
    }
    public function close(Request $request){
        $data = $request->all();
        if(isset($data['conference_id']) && (int)$data['conference_id'] != 0){
            $conference = Conference::find($data['conference_id']);
            $conference->is_close = 1;
            if($conference->save()){
                echo 'SUCC';
            }else{
                echo 'FAIL';
            }
        }else{
            echo 'FAIL';
        }   
    }      
    public function startConference(Request $request){
        $data = $request->all();
        if(isset($data['conference_id']) && (int)$data['conference_id'] != 0){
            $conference = Conference::find($data['conference_id']);
            $conference->id = $data['conference_id'];
            $conference->is_start = 1;
            $conference->start_date_time = date('Y-m-d');
            if($conference->save()){
                echo 'SUCC';
            }else{
                echo 'FAIL';
            }
        }else{
            echo 'FAIL';
        }
    }
    public function sendAnnouncement(Request $request){
        $data = $request->all();
        if(isset($data['conference_id']) && (int)$data['conference_id'] != 0 && isset($data['announcement']) && $data['announcement'] != ''){
            $conference = Conference::find($data['conference_id']);
            if(isset($conference->announcement) && $conference->announcement != ''){
                $conference->announcement = $conference->announcement.'#####'.$data['announcement'];
            }else{
                $conference->announcement = $data['announcement'];    
            }
            if($conference->save()){
                echo $data['announcement'];
            }else{
                echo 'FAIL';
            }
        }else{
            echo 'FAIL';
        }        
    } 
    public function pushConference(Request $request){
        $param = $request->all();
        if(isset($param['conference_id']) && (int)$param['conference_id'] != 0){
            $conference_id = $param['conference_id'];
            $confs = ConferencesUser::where('conference_id',$conference_id)->get();
            $cnt = 0;
            $data = array();
            foreach($confs as $conf){
                $data[$cnt]['id']               = $conf->id;
                $data[$cnt]['conference_id']    = $conf->conference_id;
                $data[$cnt]['name']             = $conf->name;
                $data[$cnt]['stream']           = $conf->stream;
                $data[$cnt]['user_type']        = \Config::get('constants.CONFERENCE_USER')[$conf->user_type];
                $data[$cnt]['user_type_id']     = $conf->user_type;
                $data[$cnt]['is_trash']         = $conf->is_trash;
                $data[$cnt]['is_enable']        = $conf->is_enable;
                //$data[$cnt]['is_fullscreen']    = $conf->is_fullscreen;
                $data[$cnt]['is_mute']          = $conf->is_mute; 
                $data[$cnt]['is_main_screen']   = $conf->is_main_screen;        
                $cnt++;
            }
            if(is_array($data) && count($data)>0){
               $datas = (string)json_encode($data);
               $result = Conference::pushNotification('2'.$datas);
           
                if(Conference::pushNotification('2'.$datas)){
                    echo 1;
                }else{
                    echo 0;
                }
            }else{
                echo 0;
            }
        }else{
            echo 0;
        }  
    }  
    public function pushAnnouncement(Request $request){
        $param = $request->all();
        if(isset($param['conference_id']) && (int)$param['conference_id'] != 0 && isset($param['announcement']) && $param['announcement'] != ''){
            if(Conference::pushNotification('4'.$param['announcement'])){
                echo 1;
            }else{
                echo 12;
            }
        }else{
            echo 23;
        }     
    }   
    public function checkconference(Request $request){
        $data = $request->all();
        if(isset($data['conference_id']) && (int)$data['conference_id'] != 0){
            $data = Conference::find($data['conference_id']);
            if(isset($data->is_start) && (int)$data->is_start != 0){
                echo 'CLOSE';
            }else{
                echo 'START';
            }
        }else{
            echo 0;
        }
    }
    public function showAnnouncement(Request $request){
        $data = $request->all();
        if(isset($data['conference_id']) && (int)$data['conference_id'] != 0){
            $data = Conference::select('announcement')->where('id',$data['conference_id'])->first();
            if(isset($data->announcement) && $data->announcement != ''){
                $announceArr = explode('#####',$data->announcement);
                foreach($announceArr as $announceKey=>$announceVal){
                    echo '<li>'.$announceVal.'</li>';
                }
            }else{
                echo 'FAIL';
            }
        }else{
            echo 'FAIL';
        }
    }    
}
