<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminMenu extends Model
{
    Protected $primaryKey = "id";
	protected $fillable = array('name', 'parent_id', 'url', 'order');
	public static function tree() {
        $adminmenudata = AdminMenu::where('is_trash',0)->get();
        $tree = array();
        foreach($adminmenudata as $admenu){
            if((int)$admenu->parent_id == 0){
                $tree[$admenu->id]['id']      = $admenu->id;
                $tree[$admenu->id]['name']    = $admenu->name;
                $tree[$admenu->id]['is_enable']    = $admenu->is_enable;
                $tree[$admenu->id]['url']    = $admenu->url;
                $tree[$admenu->id]['order']    = $admenu->order;
                $tree[$admenu->id]['parent_id']    = $admenu->parent_id;
            }else{
                $tree[$admenu->parent_id]['child'][$admenu->position]['id'] = $admenu->id;
                $tree[$admenu->parent_id]['child'][$admenu->position]['name'] = $admenu->name;
                $tree[$admenu->parent_id]['child'][$admenu->position]['is_enable'] = $admenu->is_enable;
                $tree[$admenu->parent_id]['child'][$admenu->position]['url'] = $admenu->url;
                $tree[$admenu->parent_id]['child'][$admenu->position]['order'] = $admenu->order;
                $tree[$admenu->parent_id]['child'][$admenu->position]['parent_id'] = $admenu->parent_id;

            }
        }
        return $tree;
    }	
}
