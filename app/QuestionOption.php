<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
	Protected $primaryKey = "id";
	protected $fillable = array('question_id','name', 'position');
}
