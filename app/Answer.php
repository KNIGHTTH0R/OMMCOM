<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Common;
use App\QuestionOption;
class Answer extends Model
{
    public function getStatisticsReport($question_id){
    	if($question_id){
    		$answer = array();
    		$answerCnt = Answer::where('question_id',$question_id)->where('question_option_id','<>',0)->get()->count();
    		if($answerCnt){
	    		$datas = Answer::select('question_option_id',DB::raw('count(question_option_id) as totalanswer'))
	    		->where('question_id',$question_id)
                ->where('question_option_id','<>',0)
	    		->groupBy('question_option_id')->get();
	    		if($datas){
	    			foreach($datas as $data){
	    				$answer[$data->question_option_id] = round(($data->totalanswer/$answerCnt) * 100);
	    			}
	    			return $answer;
	    		}else{
	    			return array();
	    		}
    		}else{
    			return array();
    		}
    	}else{
    		return array();
    	}
    } 
    public function validatePollUniqueUser($question_id,$ipAddress){ 
    	if($question_id){
	    	//$ipAddress = Common::getMac();
	    	$answerCnt = Answer::where(['question_id'=>$question_id,'ip_address'=>$ipAddress])->get()->count();
	    	return $answerCnt;
    	}else{
    		return 0;
    	}
    }
    public static function getMobileStatisticsReport($question_id){
        if($question_id){
            $answer = array();
            $answerCnt = Answer::where('question_id',$question_id)->where('question_option_id','<>',0)->get()->count();
            $optionArr = QuestionOption::select( 'id', 'name')->where('question_id',$question_id)->get();
            if($answerCnt){
                $datas = Answer::select('question_option_id','question_options.name as optionname',DB::raw('count(question_option_id) as totalanswer'))
                ->leftJoin('question_options','question_options.id','=','answers.question_option_id')
                ->where('answers.question_id',$question_id)
                ->where('question_option_id','<>',0)
                ->groupBy('answers.question_option_id','question_options.name')->get();
                if($datas){
                    $counter = 0;
                    $pollOptionArr = array();
                    foreach($datas as $data){
                        $counter++;
                        $pollOptionArr[] = $data->question_option_id;
                        $answer[$counter]['name'] = $data->optionname;
                        $answer[$counter]['percentage'] = round(($data->totalanswer/$answerCnt) * 100);
                    }
                    if($optionArr){
                        foreach($optionArr as $optionVal){
                            if(!in_array($optionVal->id,$pollOptionArr)){
                                $counter++;
                                $answer[$counter]['name']       = $optionVal->name;
                                $answer[$counter]['percentage'] = 0;
                            }
                        }
                    }
                    return $answer;
                }else{
                    return array();
                }
            }else{
                return array();
            }
        }else{
            return array();
        }
    }     
}
