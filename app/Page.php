<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    	Protected $primaryKey = "id";
		protected $fillable = array('name', 'content','meta_desc','meta_key','menu_id', 'slug', 'is_enable', 'is_trash');
}
