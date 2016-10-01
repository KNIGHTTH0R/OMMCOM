<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
	Protected $primaryKey = "id";
	protected $fillable = array('name', 'email');
    public function notes(){
    	return $this->hasMany(Note::Class);
    }
}
