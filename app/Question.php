<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
	Protected $primaryKey = "id";
	protected $fillable = array('name','is_statitics_to_public', 'is_active','is_trash');
    public function QuestionOption() {
        return $this->hasMany('App\QuestionOption','question_id');
    }
    public function Answer() {
        return $this->hasMany('App\Answer','question_id');
    } 
}
