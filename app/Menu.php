<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
	Protected $primaryKey = "id";
	protected $fillable = array('name', 'description','position','section','parent_id','is_enable');

	public static function getMenus() {
        $menudata = Menu::select('menus.name','pages.slug','pages.name as pagename')
        ->leftJoin('pages', 'menus.id', '=', 'pages.menu_id')
        ->where(['menus.is_enable'=>1,'menus.is_trash'=>0,'pages.is_enable'=>1,'pages.is_trash'=>0])->get();
        return $menudata;
    }
	public static function tree() {
        $menudata = Menu::where('is_trash',0)->get();
        $tree = array();
        foreach($menudata as $menu){
            if((int)$menu->parent_id == 0){
                $tree[$menu->id]['id']      = $menu->id;
                $tree[$menu->id]['name']    = $menu->name;
                $tree[$menu->id]['is_enable']    = $menu->is_enable;
                $tree[$menu->id]['description']    = $menu->description;
                $tree[$menu->id]['position']    = $menu->position;
            }else{
                $tree[$menu->parent_id]['child'][$menu->position]['id'] = $menu->id;
                $tree[$menu->parent_id]['child'][$menu->position]['name'] = $menu->name;
                $tree[$menu->parent_id]['child'][$menu->position]['is_enable'] = $menu->is_enable;
                $tree[$menu->parent_id]['child'][$menu->position]['description'] = $menu->description;
                $tree[$menu->parent_id]['child'][$menu->position]['position'] = $menu->position;
            }
        }
        return $tree;
    }    	
}
