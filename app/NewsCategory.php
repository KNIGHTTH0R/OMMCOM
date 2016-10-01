<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
{

    protected $table = 'news_categories';
    Protected $primaryKey = "id";
    protected $fillable = array('name', 'description','parent_id','slug','order','meta_desc','meta_keywords');
    
    public static function tree() {
        $newscatdata = NewsCategory::where('is_active',1)->OrderBy('order','asc')->get();
        $tree = array();
        foreach($newscatdata as $news){
            if((int)$news->parent_id == 0){
                $tree[$news->id]['id']              = $news->id;
                $tree[$news->id]['name']            = $news->name;
                $tree[$news->id]['slug']            = $news->slug;
                $tree[$news->id]['description']     = $news->description;
                $tree[$news->id]['order']           = $news->order;
                $tree[$news->id]['is_active']       = $news->is_active;
            }else{
                $tree[$news->parent_id]['child'][$news->order]['id']            = $news->id;
                $tree[$news->parent_id]['child'][$news->order]['name']          = $news->name;
                $tree[$news->parent_id]['child'][$news->order]['slug']          = $news->slug;
                $tree[$news->parent_id]['child'][$news->order]['description']   = $news->description;
                $tree[$news->parent_id]['child'][$news->order]['order']         = $news->order;
                $tree[$news->parent_id]['child'][$news->order]['is_active']     = $news->is_active;
            }
        }
        return $tree;
    }
    public static function treeAccordion() {
        $newscatdata = NewsCategory::OrderBy('order','asc')->get();
        $tree = array();
        foreach($newscatdata as $news){
            if((int)$news->parent_id == 0){
                $tree[$news->id]['id']              = $news->id;
                $tree[$news->id]['name']            = $news->name;
                $tree[$news->id]['slug']            = $news->slug;
                $tree[$news->id]['description']     = $news->description;
                $tree[$news->id]['order']           = $news->order;
                $tree[$news->id]['is_active']       = $news->is_active;
            }else{
                $tree[$news->parent_id]['child'][$news->order]['id']            = $news->id;
                $tree[$news->parent_id]['child'][$news->order]['name']          = $news->name;
                $tree[$news->parent_id]['child'][$news->order]['slug']          = $news->slug;
                $tree[$news->parent_id]['child'][$news->order]['description']   = $news->description;
                $tree[$news->parent_id]['child'][$news->order]['order']         = $news->order;
                $tree[$news->parent_id]['child'][$news->order]['is_active']     = $news->is_active;
            }
        }
        return $tree;
    } 	
    
   /*
    public function parent() {
        return $this->hasOne('NewsCategory', 'id', 'parent_id');

    }

    public function children() {

        return $this->hasMany('NewsCategory', 'parent_id', 'id');

    }  

    public static function tree() {

        return static::with(implode('.', array_fill(0, 100, 'children')))->where('parent_id', '=', NULL)->get();

    }*/   
}

