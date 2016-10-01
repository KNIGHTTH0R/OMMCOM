<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model
{
	Protected $primaryKey = "id";
	protected $fillable = array('user_type_id', 'admin_menu_id','admin_sub_menu_id');

    public static function tree() {
        $roledatas = RoleMenu::select('admin_menu_id','admin_sub_menu_id','admin_menus.name as menuname','admin_menus.url as menuurl','n.name as submenu','n.url as submenuurl')
        ->leftJoin('admin_menus', 'role_menus.admin_menu_id', '=', 'admin_menus.id')
        ->leftJoin('admin_menus as n', 'role_menus.admin_sub_menu_id', '=', 'n.id')
        ->OrderBy('admin_menus.order','ASC')
        ->OrderBy('n.order','ASC')
        ->get();
        $tree = array();
        foreach($roledatas as $roledata){
            if((int)$roledata->admin_sub_menu_id == 0){
                $tree[$roledata->menuname]      = $roledata->menuurl;
            }else{
                $tree[$roledata->menuname][$roledata->submenu]            = $roledata->submenuurl;
            }
        }
        return $tree;
    }	
}
