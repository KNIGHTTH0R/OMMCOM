<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsComment extends Model
{
	Protected $primaryKey = "id";
	protected $fillable = array('name', 'comment', 'is_enable', 'news_id', 'is_verified', 'verified_by', 'verified_date','is_trash');

	public static function tree() {
        $commentdata = NewsComment::select('news_comments.id','news_categories.name as cat_name', 'news_comments.name as cname','comment','news_comments.file_path','comment','news_comments.is_image','comment','news_comments.is_video','news_comments.is_audio','news_comments.news_id', 'news.name as newsname', 'news_comments.is_verified', 'news_comments.is_enable', 'news_comments.verified_date')
        ->leftJoin('news', 'news.id', '=', 'news_comments.news_id')
        ->leftJoin('news_categories', 'news.cat_id', '=', 'news_categories.id')
        ->where('news_comments.is_trash',0)
        ->OrderBy('news_comments.verified_date','DESC')
        ->get();
        $tree = array();
        foreach($commentdata as $comment){
                $tree[$comment->news_id]['news_id'] = $comment->news_id;
                $tree[$comment->news_id]['newsname'] = $comment->newsname;
                $tree[$comment->news_id]['cat_name'] = $comment->cat_name;
                $tree[$comment->news_id]['child'][$comment->id]['cname'] = $comment->cname;
                $tree[$comment->news_id]['child'][$comment->id]['comment'] = $comment->comment;
                $tree[$comment->news_id]['child'][$comment->id]['is_verified'] = $comment->is_verified;
                $tree[$comment->news_id]['child'][$comment->id]['verified_date'] = $comment->verified_date;
                $tree[$comment->news_id]['child'][$comment->id]['is_enable'] = $comment->is_enable;
                $tree[$comment->news_id]['child'][$comment->id]['id'] = $comment->id;
                $tree[$comment->news_id]['child'][$comment->id]['file_path'] = $comment->file_path;
                $tree[$comment->news_id]['child'][$comment->id]['is_image'] = $comment->is_image;
                $tree[$comment->news_id]['child'][$comment->id]['is_video'] = $comment->is_video;
                $tree[$comment->news_id]['child'][$comment->id]['is_audio'] = $comment->is_audio;                
        }
        return $tree;
    }	
}
