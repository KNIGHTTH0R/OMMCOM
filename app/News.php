<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
	Protected $primaryKey = "id";
	protected $fillable = array('cat_id','sub_cat_id', 'name','slug','journalist_name','news_location','short_description','long_description','position','featured_image','tags','is_hot','allow_comment','meta_desc','meta_keywords','is_featured','is_enable','is_approved','is_archive','is_top_story','is_viral','approved_by','approved_date','news_count','file_path','attachment_file','is_top_video','is_trash','video_title');
    public function NewsImage() {
        return $this->hasMany('App\NewsImage','news_id')->orderBy('position');
    }
    public function NewsVideo() {
        return $this->hasMany('App\NewsVideo','news_id')->orderBy('position');
    }
    public function NewsCategory() {
        return $this->belongsTo('App\NewsCategory','cat_id');
    } 
    public static function breakingNews(){
        $news = News::select('id','name','short_description')
        ->where(['is_hot'=>1,'is_archive'=>0,'is_approved'=>1,'is_enable'=>1,'is_trash'=>0])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
        ->OrderBy('position','ASC')
        ->OrderBy('approved_date','DESC')
        ->take(10)->get();
        $tree = array();
        if($news){
            foreach($news as $newsItem){
                $tree[] = $newsItem->short_description;
            }
        }
        return implode(' ** ',$tree);        
    }
    public static function breakingNewsList(){
        $news = News::select('short_description')
        ->where(['is_hot'=>1,'is_archive'=>0,'is_approved'=>1,'is_enable'=>1,'is_trash'=>0])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
        ->OrderBy('position','ASC')
        ->OrderBy('approved_date','DESC')
        ->take(10)->get();
        return $news;   
    }    
    public static function mostViewed($catId = 0){
        $condition = array('is_archive'=>0,'is_approved'=>1,'is_enable'=>1,'is_trash'=>0);
        if($catId){
            $condition += array('cat_id'=>$catId);
        }
        $mostViewed = News::select('news.id','news.name','news.slug','news_count','news_categories.name as categoryname')
        ->leftJoin('news_categories', 'news.cat_id', '=', 'news_categories.id')
        ->where($condition)->where(['is_archive'=>0,'is_approved'=>1,'is_enable'=>1,'is_trash'=>0,'is_publish'=>1])->OrderBy('news_count','DESC')->take(10)->get();
        return $mostViewed;
    } 
    public static function latestNews(){
        $latestNews = News::select('id','name','slug','tags','short_description','featured_image','is_video','is_image','file_path')
        ->where(['is_archive'=>0,'is_approved'=>1,'is_enable'=>1,'is_hot'=>1,'is_trash'=>0,'is_publish'=>1])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')->OrderBy('position','ASC')->OrderBy('approved_date','DESC')
        ->take(10)->get();
        return $latestNews;
    }
    public static function frontCategoryNews(){
        /*$categoryNews = News::select('news.id','news.name','news.slug','news.featured_image','short_description','file_path','approved_date','position','news.user_id','users.name as username','news_categories.name as categoryname','news_categories.slug as categoryslug')
        ->leftJoin('users','users.id','=','news.user_id')
        ->leftJoin('news_categories','news_categories.id','=','news.cat_id')
        ->where(['is_approved'=>1,'is_hot'=>1,'is_featured'=>0,'is_top_story'=>0,'is_archive'=>0,'is_enable'=>1,'news_categories.is_active'=>1])
        ->GroupBy('cat_id')
        ->OrderBy('news.approved_date','DESC')->OrderBy('news.position','ASC')->OrderBy('news_categories.order','ASC')->get();  
        */
        $odisha_category_id = \Config::get('constants.ODISHA_CATEGORY_ID');
        $query = "SELECT  b.id,b.name,b.slug,b.tags,b.featured_image,b.short_description,b.cat_id,b.file_path,b.approved_date AS approved_date,
            b.position,c.name AS categoryname,b.is_video,b.is_image,b.file_path,c.slug AS categoryslug,d.name AS username, b.journalist_name
            FROM
            (SELECT a.cat_id AS catid, MAX(a.approved_date) AS appdate FROM news a 
            WHERE a.is_hot=1 AND a.is_approved=1 AND  a.is_featured=0 AND a.is_top_story=0 AND a.is_archive=0 AND a.is_enable=1 AND a.is_trash=0 AND a.is_trash=0
            AND a.cat_id != $odisha_category_id GROUP BY a.cat_id) AS x 
            INNER JOIN news b ON x.catid=b.cat_id AND x.appdate=b.approved_date
            LEFT JOIN news_categories c ON b.cat_id=c.id
            LEFT JOIN users d ON b.user_id=d.id
            WHERE c.is_active=1
            GROUP BY b.cat_id
            ORDER BY c.order ASC";
           // ORDER BY b.cat_id ASC,b.position ASC,c.order ASC";
        $categoryNews = DB::select(DB::raw($query));
        return $categoryNews;    
    } 
    public static function topStoryNews(){
        $topStories = News::select('news.id','news.name','news.journalist_name','slug','featured_image','short_description','approved_date','is_video','is_image','file_path','users.name as username')
        ->leftJoin('users','users.id','=','news.user_id')
        ->where(['is_approved'=>1,'is_hot'=>0,'is_top_story'=>1,'is_archive'=>0,'is_enable'=>1, 'news.is_trash'=>0])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
        ->OrderBy('position','ASC')
        ->OrderBy('approved_date','DESC')
        ->take(6)->get();    
        return $topStories;    
    }
    public static function topNewsNow(){
        $topNewsNow = News::select('news.id','news.name','slug','featured_image')
        ->where(['is_approved'=>1,'is_hot'=>1,'is_featured'=>1,'is_archive'=>0,'is_enable'=>1,'is_trash'=>0])
        ->where('news.id','!=',$topNews->id)
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
        ->OrderBy('position','ASC')
        ->OrderBy('approved_date','DESC')
        ->take(4)->get();  
        return $topNewsNow;    
    }
    public static function topVideo(){
        $topVideos = News::select('file_path','name','slug','is_video')->where(['is_video'=>1,'is_approved'=>1,'is_enable'=>1,'is_top_video'=>1,'is_trash'=>0])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
        ->OrderBy('position','ASC')
        ->OrderBy('approved_date','DESC')
        ->first();   
        return $topVideos;     
    }
    public static function viralVideo(){
        $viralVideos = News::select('file_path','name','slug','is_video')->where(['is_video'=>1,'is_approved'=>1,'is_enable'=>1,'is_viral'=>1,'is_trash'=>0])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
        ->OrderBy('position','ASC')
        ->OrderBy('approved_date','DESC')
        ->first();    
        return $viralVideos;     
    }
    public static function siteMapCategoryNews($cat_id){
        $datas = News::select('name','slug')->where(['cat_id'=>$cat_id,'is_hot'=>1,'is_publish'=>1,'is_archive'=>0,'is_approved'=>1,'is_enable'=>1,'is_trash'=>0])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
        ->OrderBy('position','ASC')
        ->OrderBy('approved_date','DESC')
        ->take(100)->get();
        return $datas;         
    }               
}
