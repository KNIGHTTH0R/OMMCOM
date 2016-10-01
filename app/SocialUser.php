<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'socialsite_id','socialsite','avatar','avatar_original','longitude','latitude'];
}
