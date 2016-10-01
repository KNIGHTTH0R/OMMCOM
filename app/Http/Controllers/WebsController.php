<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\News;
use App\NewsCategory;
use App\NewsComment;
use App\Conference;
use App\CitizenCustomize;
use App\Advertisement;
use App\Version;
use App\CitizenNews;
use App\Question;
use App\Answer;
use App\BreakingNews;
use App\RegisteredDevice;
use App\ConferencesUser;
use App\NewsTopVideo;
use App\TopNews;
use App\SocialUser;
use App\Common;
use App\Feedback;
class WebsController extends Controller{
    public function postsHome(){
        //$x = 'Subrata Kumar Rout';
        //return $x;
        $featured = array();
        /*
        $topNews = News::select('news.id','news.name','slug','featured_image','short_description','file_path','approved_date','position','user_id','users.name as username')
        ->leftJoin('users','users.id','=','news.user_id')
        ->where(['is_approved'=>1,'is_hot'=>1,'is_featured'=>1,'is_archive'=>0,'is_enable'=>1])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')->OrderBy('position','ASC')->OrderBy('approved_date','DESC')->first(); 
        */
        $topNews = TopNews::select('news.id','news.name','news.slug','news.featured_image','news.short_description','news.is_video','news.is_image','news.file_path','news.approved_date','news.position','news.user_id','users.name as username','news.meta_desc','news.meta_keywords','news.journalist_name','news.news_location')
        ->leftJoin('news','news.id','=','top_news.news_id')
        ->leftJoin('users','users.id','=','news.user_id')
        ->where(['news.is_approved'=>1,'news.is_hot'=>1,'news.is_featured'=>1,'news.is_archive'=>0,'news.is_enable'=>1,'news.is_trash'=>0])
        ->OrderBy('top_news.position','ASC')->first();

        /*
         *Query for get the conference details
         */
        $conferenceNews = Conference::select('id','name','conference_banner','started_at','start_time')
        ->where(['is_enable'=>1,'is_archive'=>0,'is_trash'=>0,'is_close'=>0])
        ->OrderBy('started_at','ASC')->first();
        /*
         *Query for get the top news now
         */
        /*
        $topNewsNow = News::select('news.id','slug','featured_image')
        ->where(['is_approved'=>1,'is_hot'=>1,'is_featured'=>1,'is_archive'=>0,'is_enable'=>1])
        ->where('news.id','!=',$topNews->id)
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')->OrderBy('position','ASC')->OrderBy('approved_date','DESC')->take(3)->get(); 
        */
        $topNewsNow = TopNews::select('news.id','news.name','news.slug','news.featured_image','news.is_image','news.file_path','news.is_video')
        ->leftJoin('news','news.id','=','top_news.news_id')
        ->where(['news.is_approved'=>1,'news.is_hot'=>1,'news.is_featured'=>1,'news.is_archive'=>0,'news.is_enable'=>1,'news.is_trash'=>0])
        ->where('news.id','!=',$topNews->id)
        ->OrderBy('top_news.position','ASC')->take(3)->get();          
        /*
         *Query for get the citizen details
         */
        $citizenCustomize = CitizenCustomize::select('name','file_path')->where(['is_enable'=>1,'is_trash'=>0])->first();
        /*
         *Query for getting the news of category wise
         
        $categoryNews = News::select('news.id','news.name','news.slug','news.featured_image','short_description','approved_date','users.name as username','news_categories.name as categoryname')
        ->leftJoin('users','users.id','=','news.user_id')
        ->leftJoin('news_categories','news_categories.id','=','news.cat_id')
        ->where(['is_approved'=>1,'is_hot'=>1,'is_featured'=>0,'is_top_story'=>0,'is_archive'=>0,'is_enable'=>1])
        ->GroupBy('cat_id')
        ->OrderBy('news.approved_date','DESC')->OrderBy('news.position','ASC')->OrderBy('news_categories.order','ASC')->get();  
        */
        $topStories = News::select('news.id','news.name','news.journalist_name','slug','featured_image','short_description','approved_date','is_video','is_image','file_path','users.name as username')
        ->leftJoin('users','users.id','=','news.user_id')
        ->where(['is_approved'=>1,'is_hot'=>0,'is_top_story'=>1,'is_archive'=>0,'is_enable'=>1, 'news.is_trash'=>0])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
        ->OrderBy('position','ASC')
        ->OrderBy('approved_date','DESC')
        ->take(1)->get();    
      
        $odisha_category_id = \Config::get('constants.ODISHA_CATEGORY_ID');
        $query = "SELECT  b.id,b.name,b.slug,b.tags,b.featured_image,b.short_description,b.cat_id,b.file_path,b.approved_date AS approved_date,
            b.position,c.name AS categoryname,b.is_video,b.is_image,b.file_path,c.slug AS categoryslug,d.name AS username
            FROM
            (SELECT a.cat_id AS catid, MAX(a.approved_date) AS appdate FROM news a 
            WHERE a.is_hot=1 AND a.is_approved=1 AND  a.is_featured=0 AND a.is_top_story=0 AND a.is_archive=0 AND a.is_enable=1 AND a.is_trash=0 AND a.cat_id != $odisha_category_id
            GROUP BY a.cat_id) AS x 
            INNER JOIN news b ON x.catid=b.cat_id AND x.appdate=b.approved_date
            LEFT JOIN news_categories c ON b.cat_id=c.id
            LEFT JOIN users d ON b.user_id=d.id
            WHERE c.is_active=1
            GROUP BY b.cat_id
            ORDER BY b.cat_id ASC,b.position ASC,c.order ASC";
        $categoryNews = DB::select(DB::raw($query));

        /*
         *
         */
        $newscatdata = Advertisement::select('name','advertisement_section_id','file_path','url_link')
        ->where(['is_enable'=>1,'is_publish'=>1,'advertisement_type_id'=>\Config::get('constants.ADVERTISEMENT_ON_PAGE')])
        ->where('end_date', '>=', date('Y-m-d'))
        ->get();   
        /*
         *Query for get the top video and viral video
         */  
        $topVideos = News::topVideo();  
        $viralVideos = News::viralVideo(); 
        /*
         *Code for return all objects
         */
        $featured['FEATUREDNEWS']                   = $topNews;
        $featured['TOPNEWSNOW']                     = $topNewsNow;
        $featured['CONFERENCE_NEWS']                = $conferenceNews;
        $featured['CITIZEN_CUSTOMIZE']              = $citizenCustomize;
        $featured['ODISHA_PLUS_NEWS']               = $topStories;
        $featured['CATEGORY_NEWS']                  = $categoryNews;
        $featured['ADVERTISEMENT']                  = $newscatdata;
        $featured['ADVERTISEMENT_HEADER']           = \Config::get('constants.ADVERTISEMENT_HEADER');
        $featured['ADVERTISEMENT_MIDDLE']           = \Config::get('constants.ADVERTISEMENT_MIDDLE');
        $featured['ADVERTISEMENT_FOOTER']           = \Config::get('constants.ADVERTISEMENT_FOOTER');
        $featured['TOP_VIDEO']                      = $topVideos;
        $featured['VIRAL_VIDEO']                    = $viralVideos;        
        return $featured;       
    }
    public function featuredNews(){
        $topNews = TopNews::select('news.id','news.name','news.slug','news.featured_image','news.short_description','news.is_video','news.is_image','news.file_path','news.approved_date','news.position','news.user_id','users.name as username','news.meta_desc','news.meta_keywords','news.journalist_name','news.news_location')
        ->leftJoin('news','news.id','=','top_news.news_id')
        ->leftJoin('users','users.id','=','news.user_id')
        ->where(['news.is_approved'=>1,'news.is_hot'=>1,'news.is_featured'=>1,'news.is_archive'=>0,'news.is_enable'=>1,'news.is_trash'=>0])
        ->OrderBy('top_news.position','ASC')->take(4)->get();  
        return $topNews;      
    }
    public function conferenceNews(){
        $conferenceNews = Conference::select('id','name','conference_banner','started_at','start_time')
        ->where(['is_enable'=>1,'is_archive'=>0,'is_trash'=>0,'is_close'=>0])
        ->OrderBy('started_at','ASC')->first();   
        return $conferenceNews;     
    }
    public function citizenCustomize(){
        $citizenCustomize = CitizenCustomize::select('name','file_path')->where(['is_enable'=>1,'is_trash'=>0])->first();
        return $citizenCustomize;
    }
    public function categoryNews(){
        $query = "SELECT  b.id,b.name,b.slug,b.tags,b.featured_image,b.short_description,b.cat_id,b.file_path,b.approved_date AS approved_date,
            b.position,c.name AS categoryname,b.is_video,b.is_image,b.file_path,c.slug AS categoryslug,d.name AS username
            FROM
            (SELECT a.cat_id AS catid, MAX(a.approved_date) AS appdate FROM news a 
            WHERE a.is_hot=1 AND a.is_approved=1 AND  a.is_featured=0 AND a.is_top_story=0 AND a.is_archive=0 AND a.is_enable=1
            GROUP BY a.cat_id) AS x 
            INNER JOIN news b ON x.catid=b.cat_id AND x.appdate=b.approved_date
            LEFT JOIN news_categories c ON b.cat_id=c.id
            LEFT JOIN users d ON b.user_id=d.id
            WHERE c.is_active=1
            GROUP BY b.cat_id
            ORDER BY b.cat_id ASC,b.position ASC,c.order ASC";
        $categoryNews = DB::select(DB::raw($query));
        return $categoryNews;        
    }
    public function advertisementPost(){
        $newsAdvtdata = Advertisement::select('name','advertisement_section_id','file_path','url_link')
        ->where(['is_enable'=>1,'is_publish'=>1,'file_type'=>'Image','advertisement_type_id'=>\Config::get('constants.ADVERTISEMENT_ON_PAGE')])
        ->where('end_date', '>=', date('Y-m-d'))
        ->get();  
        $featured['ADVERTISEMENT']                  = $newsAdvtdata;
        $featured['ADVERTISEMENT_HEADER']           = \Config::get('constants.ADVERTISEMENT_HEADER');
        $featured['ADVERTISEMENT_MIDDLE']           = \Config::get('constants.ADVERTISEMENT_MIDDLE');
        $featured['ADVERTISEMENT_FOOTER']           = \Config::get('constants.ADVERTISEMENT_FOOTER');        
        return $featured;       
    }
    public function topViralVideos(){
        $topVideos = News::topVideo();  
        $viralVideos = News::viralVideo();    
        $featured['TOP_VIDEO']    = $topVideos;
        $featured['VIRAL_VIDEO']  = $viralVideos;        
        return $featured;               
    }
    public function breakingNews(){
        $data = array();
        $data['BREAKINGNEWS']   = BreakingNews::breakingNews();
        $data['NEWSDETAILS']    = BreakingNews::newsUpdate();
        return $data;
    }
    public function versionDetails($ostype){
        if($ostype){
            $version = Version::select('os_type as ostype','version','code_name','major')
            ->where(['os_type'=>$ostype,'is_enable'=>1,'is_trash'=>0])
            ->OrderBy('created_at','DESC')
            ->first();
            return $version;
        }else{
            return 0;
        }
    }
    public function deviceRegister(Request $request){
        $registeredDevice = new RegisteredDevice();
        $data = $request->all();
        $datas = RegisteredDevice::whereUid($data['uid'])->first();
        if(isset($datas['id']) && (int)$datas['id'] != 0){
            return 1;
        }else{
            if(isset($data['uid'])){
                $registeredDevice->uid = $data['uid'];
            }
            if(isset($data['email'])){
                $registeredDevice->email = $data['email'];
            }
            if(isset($data['notification'])){
                $registeredDevice->notification = $data['notification'];
            }else{
                $registeredDevice->notification = 1;
            }
            if($registeredDevice->save()){
                return 1;
            }else{
                return 0;
            }            
        }   
    }
    public function postFeedback(Request $request){
        $data = $request->all();
        $feedback = new Feedback();
        $feedback->name = $data['name'];
        $feedback->email = $data['email'];
        $feedback->mobile = $data['mobile'];
        $feedback->message = $data['message'];
        if($feedback->save()){
            return 1;
        }else{
            return 0;
        }         
    }
    public function categories(){
        $data = NewsCategory::select('id','name','slug')->where(['parent_id'=>0,'is_active'=>1])->get();
        return $data;
    }
    public function subCategories($id){
        if($id){
            $data = NewsCategory::select('id','name','slug')->where(['parent_id'=>$id,'is_active'=>1])->get();
            return $data;            
        }else{
            return array();
        }
    }
    public function newsDetails($slug){
        if($slug != ''){
            $data = array();
            /*
             *Query for get the news details
             */
            $news = News::whereSlug(trim($slug))->first();
            if(isset($news->news_count) && (int)$news->news_count != 0){
                $news->news_count = $news->news_count + 1;
            }else{
                $news->news_count = 1;
            }
            $news->save();
            $news->NewsImage;
            $news->NewsVideo;              
            /*
             *Query for get the related news
             */
            $releatedNews = NEWS::select('id','name','slug','tags','short_description','featured_image')
            ->where(['is_archive'=>0,'is_approved'=>1,'is_enable'=>1])
            ->where('tags', 'like', '%' . $news->tags . '%')
            ->OrderBy('position','ASC')->take(10)->get();
            /*
             *Query for get the comment news
             */
            $newsComments = NewsComment::select('name','comment','user_id','verified_date','file_path','is_image','is_video','is_audio')
            ->where(['is_enable'=>1,'is_verified'=>1,'news_id'=>$news->id])
            ->OrderBy('id','desc')->get(); 


            $data['News'] = $news;
            $data['ReleatedNews'] = $releatedNews;
            $data['NewsComment'] = $newsComments;
            return $data;
        }else{
            return array();
        }
    }
    public function odishaPlus(){
        $topStories = DB::table('news')
        ->select('news.id','news.name','news.journalist_name','slug','featured_image','short_description','approved_date','is_video','is_image','file_path','users.name as username')
        ->leftJoin('users','users.id','=','news.user_id')
        ->where(['is_approved'=>1,'is_hot'=>0,'is_top_story'=>1,'is_archive'=>0,'is_enable'=>1, 'news.is_trash'=>0])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
        ->OrderBy('position','ASC')
        ->OrderBy('approved_date','DESC')
        ->paginate(6);  
        return $topStories;        
    }        
    public function category($slug){ 
        if($slug != ''){
            $newsCategory = NewsCategory::select('id','name')->where('slug',$slug)->first();
            $datas = DB::table('news')
            ->select('news.id','news.name','slug','short_description as shortdescription','featured_image','file_path','is_video','is_image','approved_date','users.name as username')
            ->leftJoin('users', 'news.user_id', '=', 'users.id')
            ->where(['news.cat_id'=>$newsCategory->id,'news.is_enable'=>1,'news.is_approved'=>1,'news.is_trash'=>0,'news.is_publish'=>1])
            ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
            ->OrderBy('position','ASC') 
            ->OrderBy('approved_date','DESC')
            ->take(10)->get();
            //return $datas;
            $data['data'] = $datas;
            $catAd = Advertisement::select('id','file_path','name','file_type')->where(['cat_id'=>$newsCategory->id,'is_trash'=>0])->get();
            $data['Advertisement'] = $catAd;   
            $advertisementPopup = Advertisement::getadvertisement(\Config::get('constants.ADVERTISEMENT_ON_POPUP'),\Config::get('constants.ADVERTISEMENT_CATEGORY_NEWS'));         
            $data['advertisementPopup'] = $advertisementPopup;
            return $data; 
        }else{
            return array();
        }     
    }
    public function nextCategory($slug){
        $newsCategory = NewsCategory::select('id','name')->where('slug',$slug)->first();
        $datas = DB::table('news')
        ->select('news.id','news.name','slug','short_description as shortdescription','featured_image','file_path','is_video','is_image','approved_date','users.name as username')
        ->leftJoin('users', 'news.user_id', '=', 'users.id')
        ->where(['news.cat_id'=>$newsCategory->id,'news.is_enable'=>1,'news.is_approved'=>1,'news.is_trash'=>0,'news.is_publish'=>1])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
        ->OrderBy('position','ASC') 
        ->OrderBy('approved_date','DESC')
        ->paginate(10); 
        return $datas;              
    }
    public function searchNewsList($slug){
        $datas = DB::table('news')
        ->select('news.id','news.name','slug','short_description as shortdescription','featured_image','file_path','is_video','is_image','approved_date','users.name as username')
        ->leftJoin('users', 'news.user_id', '=', 'users.id')
        ->where(['is_enable'=>1,'is_approved'=>1])
        ->where('news.name', 'like', '%'.$slug.'%')
        ->OrderBy('position','ASC') 
        ->OrderBy('approved_date','DESC')
        ->paginate(10); 
        return $datas;
    }   
    public function topNewsNow(){
        /*
         *Query for get the top news
         */
        /*
        $datas = DB::table('news')
        ->select('news.id','news.name','slug','short_description as shortdescription','featured_image','file_path','is_video','is_image','approved_date','users.name as username')
        ->leftJoin('users', 'news.user_id', '=', 'users.id')
        ->where(['is_approved'=>1,'is_hot'=>1,'is_featured'=>1,'is_archive'=>0,'is_enable'=>1])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')->OrderBy('position','ASC')->OrderBy('approved_date','DESC')->take(5)->get();
        */
        $datas = TopNews::select('news.id','news.name','news.slug','news.short_description as shortdescription','news.featured_image','news.file_path','news.is_video','news.is_image','news.approved_date','users.name as username','news.journalist_name')
        ->leftJoin('news','news.id','=','top_news.news_id')
        ->leftJoin('users', 'news.user_id', '=', 'users.id')
        ->where(['news.is_approved'=>1,'news.is_hot'=>1,'news.is_featured'=>1,'news.is_archive'=>0,'news.is_enable'=>1,'news.is_trash'=>0])
        ->OrderBy('top_news.position','ASC')->take(5)->get();

        $advertisementPopup = Advertisement::getadvertisement(\Config::get('constants.ADVERTISEMENT_ON_POPUP'),\Config::get('constants.ADVERTISEMENT_TOP5NEWS'));

        $topNews = NewsTopVideo::select('name','video_file')->where('is_enable',1)->OrderBy('updated_at','DESC')->first();
        
        $data['data']               = $datas;
        $data['topNews']            = $topNews;
        $data['advertisementPopup'] = $advertisementPopup;
        return $data;      
    }
    public function nextTopNews(){
        $datas = DB::table('top_news')
        ->select('news.id','news.name','news.slug','news.short_description as shortdescription','news.featured_image','news.file_path','news.is_video','news.is_image','news.approved_date','users.name as username','news.journalist_name','news.video_title')
        ->leftJoin('news','news.id','=','top_news.news_id')
        ->leftJoin('users', 'news.user_id', '=', 'users.id')        
        ->where(['news.is_approved'=>1,'news.is_hot'=>1,'news.is_featured'=>1,'news.is_archive'=>0,'news.is_enable'=>1,'news.is_trash'=>0])
        ->OrderBy('top_news.position','ASC')
        ->paginate(5); 
        return $datas;       
    }
    public function citizenLatest(){
        $datas = DB::table('citizen_news')
        ->select('id','name','file_path','file_type','description','slug','is_anonymous','updated_at')
        ->where(['is_enable'=>1,'is_trash'=>0])
        ->OrderBy('updated_at','DESC')
        ->OrderBy('position','ASC')      
        ->take(20)->get();
        //->paginate(16);
        return $datas; 
        /*
        $advertisementPopup = Advertisement::getadvertisement(\Config::get('constants.ADVERTISEMENT_ON_POPUP'),\Config::get('constants.ADVERTISEMENT_CITIZEN_JOURNALIST'));
        $data['data']                   = $datas;
        $data['advertisementPopup']     = $advertisementPopup;        
        return $data;*/  
    }
    public function mostviewed(){
        $mostVieweddatas = DB::table('citizen_news')
        ->select('id','name','file_path','file_type','description','slug','is_anonymous','news_count','updated_at')
        ->where(['is_enable'=>1,'is_trash'=>0])
        ->OrderBy('news_count','DESC')  
        ->take(20)->get();  
        //->paginate(16); 
        return $mostVieweddatas; 
        /*
        $advertisementPopup = Advertisement::getadvertisement(\Config::get('constants.ADVERTISEMENT_ON_POPUP'),\Config::get('constants.ADVERTISEMENT_CITIZEN_JOURNALIST'));
        $data['data']         = $mostVieweddatas;
        $data['advertisementPopup']     = $advertisementPopup;          
        return $data;  */
    }   
    public function citizenAdvPopup(){
        $advertisementPopup = Advertisement::getadvertisement(\Config::get('constants.ADVERTISEMENT_ON_POPUP'),\Config::get('constants.ADVERTISEMENT_CITIZEN_JOURNALIST'));
        return $advertisementPopup;
    }   
    public function citizenNewsDetails($slug){
        $datas = CitizenNews::select('id','name','file_path','file_type','description','long_description','slug','news_count')->where(['slug'=>$slug])->first();
        if(isset($datas->news_count) && (int)$datas->news_count != 0){
            $datas->news_count = $datas->news_count + 1;
        }else{
            $datas->news_count = 1;
        }        
        $datas->save();
        return $datas;
    }
    public function pollQuestion(){
        $questionAnswer = array();
        $answer = new Answer();
        $pollQuestions = Question::select('id','name','is_statitics_to_public')->where(['is_active'=>1,'is_trash'=>0])->first();
        $pollQuestions->QuestionOption;
        $answers = $answer->getMobileStatisticsReport($pollQuestions->id);
        $questionAnswer['QUESTION'] = $pollQuestions;
        $questionAnswer['ANSWER'] = $answers;
        $questionAnswer['COUNT'] = count($answers);
        return $questionAnswer;
    }
    public function pollSave(Request $request){
        $data = $request->all();
        $answer = new Answer();
        $answer->question_id            = $data['question_id']; 
        $answer->question_option_id     = $data['question_option_id'];
        $answer->ip_address             = $data['ip_address'];
        $answer->user_agent             = $data['user_agent'];
        $answer->is_anonymous           = 0;
        $answer->user_id                = 0;
        if($answer->save()){
            echo 1;
        }else{
            echo 0;
        }
    } 
    public function postComment(Request $request){
        $data = $request->all();
        $newsComment = new NewsComment();
        if(isset($data['news_id'])){
            $newsComment->news_id = $data['news_id'];
            if(isset($data['name'])){
                $newsComment->name = $data['name'];
            }
            if(isset($data['email'])){
                $newsComment->email = $data['email'];
            }
            if(isset($data['contact_no'])){
                $newsComment->contact_no = $data['contact_no'];
            }
            if(isset($data['comment'])){
                $newsComment->comment = $data['comment'];
            } 
            $resizeImage = \Config::get('constants.RESIZE_IMAGE');
            $filePath = public_path().'/file/news/';
            if($request->hasFile('file_path')) {
                $filelink   = $data['file_path'];
                $extension  = strtolower($filelink->getClientOriginalExtension());
                $timestamp  = rand().time().rand();
                $name = $timestamp.'.'.$extension;
                $newsComment->file_path     = $name;
                $filelink->move($filePath.'original/', $name);
                if(array_key_exists($extension, \Config::get('constants.IMAGE_EXTENSION'))){
                    $newsComment->is_image = 1;
                    if(is_array($resizeImage) && count($resizeImage)>0){
                        foreach($resizeImage as $resizeKey=>$resizeVal){
                            if(!is_dir($filePath.$resizeKey)){
                                mkdir($filePath.$resizeKey,0777);
                            }          
                            $resizeValArr = explode(',',$resizeVal);          
                            \File::copy($filePath.'original/'.$name, $filePath.$resizeKey.'/'.$name);
                            \Image::make($filePath.$resizeKey.'/'.$name)->resize($resizeValArr[0],$resizeValArr[1])->save($filePath.$resizeKey.'/'.$name);
                        }
                    }                 
                }else if(array_key_exists($extension, \Config::get('constants.VIDEO_EXTENSION'))){
                    $newsComment->is_video = 1;
                    if(array_key_exists($extension, \Config::get('constants.CONVERT_VIDEO'))){
                        $thumbName = $timestamp.'.jpg';
                        $newName = $timestamp.'.mp4';
                        $fileSet = public_path().'/file/news/original/'; 
                        $size = "1280x720";
                        $getFromSecond = 2;  
                        $thumbcmd = "ffmpeg -i $fileSet$name -an -ss $getFromSecond -s $size $fileSet$thumbName";
                        shell_exec($thumbcmd);
                        $cmd = "ffmpeg -i $fileSet$name -strict -2 -movflags faststart $fileSet$newName 2>&1";
                        if(shell_exec($cmd)){
                            $newsComment->file_path = $newName;
                        }
                    }                 
                }else if(array_key_exists($extension, \Config::get('constants.AUDIO_EXTENSION'))){
                    $newsComment->is_audio = 1;
                }
            }        
            if($newsComment->save()){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }                             
    }
    public function postCitizenNews(Request $request){
        $data = $request->all();
        $citizenNews = new CitizenNews();
        $common = new Common();
        if(isset($data['name'])){
            $citizenNews->name = $data['name'];
            $citizenNews->slug = $common->seoUrl($data['name']).'-'.time();
        }
        if(isset($data['email'])){
            $citizenNews->email = $data['email'];
        }
        if(isset($data['description'])){
            $citizenNews->description = $data['description'];
        }
        if(isset($data['long_description'])){
            $citizenNews->long_description = $data['long_description'];
        }
        if(isset($data['is_anonymous'])){
            $citizenNews->is_anonymous = $data['is_anonymous'];
        }
        $resizeImage = \Config::get('constants.RESIZE_IMAGE');
        $filePath = public_path().'/file/citizenNews/';        
        if($request->hasFile('file_path')) {
            $file   = $data['file_path'];
            $extension  = strtolower($file->getClientOriginalExtension());
            $timestamp  = rand().time().rand();
            $name = $timestamp.'.'.$extension;
            $citizenNews->file_path     = $name;
            $file->move($filePath.'original/', $name);
            if(array_key_exists($extension, \Config::get('constants.IMAGE_EXTENSION'))){
                $citizenNews->file_type = 'Image';
                if(is_array($resizeImage) && count($resizeImage)>0){
                    foreach($resizeImage as $resizeKey=>$resizeVal){
                        if(!is_dir($filePath.$resizeKey)){
                            mkdir($filePath.$resizeKey,0777);
                        }          
                        $resizeValArr = explode(',',$resizeVal);          
                        \File::copy($filePath.'original/'.$name, $filePath.$resizeKey.'/'.$name);
                        \Image::make($filePath.$resizeKey.'/'.$name)->resize($resizeValArr[0],$resizeValArr[1])->save($filePath.$resizeKey.'/'.$name);
                    }
                }                 
            }else if(array_key_exists($extension, \Config::get('constants.VIDEO_EXTENSION'))){
                $citizenNews->file_type = 'Video';
                if(array_key_exists($extension, \Config::get('constants.CONVERT_VIDEO'))){
                    $thumbName = $timestamp.'.jpg';
                    $newName = $timestamp.'.mp4';
                    $fileSet = public_path().'/file/citizenNews/original/'; 
                    $size = "1280x720";
                    $getFromSecond = 2;  
                    $thumbcmd = "ffmpeg -i $fileSet$name -an -ss $getFromSecond -s $size $fileSet$thumbName";
                    shell_exec($thumbcmd);
                    $cmd = "ffmpeg -i $fileSet$name -strict -2 -movflags faststart $fileSet$newName 2>&1";
                    if(shell_exec($cmd)){
                        $citizenNews->file_path = $newName;
                    }
                }                
            }else if(array_key_exists($extension, \Config::get('constants.AUDIO_EXTENSION'))){
                $citizenNews->file_type = 'Audio';
            }
        }
        if($citizenNews->save()){
            return 1;
        }else{
            return 0;
        }            
    }
    public function conferenceWebJoin(Request $request){
        $data = $request->all();
        $conferencesUser = new ConferencesUser();
        $conferencesUser->conference_id = $data['conference_id']; 
        $conferencesUser->name          = $data['name'];
        $conferencesUser->stream        = $data['conference_id'].time();
        $conferencesUser->user_type     = \Config::get('constants.VIEWER_USER');
        $conferencesUser->in_time       = date('Y-m-d H:i:s');
        $conferencesUser->is_enable     = 0;
        $conferencesUser->user_id       = 1;
        $data['stream'] = $conferencesUser->stream;  
        if($conferencesUser->save()){
            return $conferencesUser->stream;
        }else{
            return 0;
        }
    }
    public function conference_details($id){
        if($id){
            $conference_id = $id;
            $confs = ConferencesUser::where(['conference_id'=>$conference_id,'is_remove'=>0])->get();
            $cnt = 0;
            $data = array();
            foreach($confs as $conf){
                $data[$cnt]['id']               = $conf->id;
                $data[$cnt]['conference_id']    = $conf->conference_id;
                $data[$cnt]['name']             = $conf->name;
                $data[$cnt]['description']      = $conf->description;
                $data[$cnt]['stream']           = $conf->stream;
                $data[$cnt]['user_type']        = \Config::get('constants.CONFERENCE_USER')[$conf->user_type];
                $data[$cnt]['user_type_id']     = $conf->user_type;
                $data[$cnt]['is_trash']         = $conf->is_trash;
                $data[$cnt]['is_enable']        = $conf->is_enable;
                $data[$cnt]['is_mute']          = $conf->is_mute; 
                $data[$cnt]['is_main_screen']   = $conf->is_main_screen;        
                $cnt++;
            }
            if(is_array($data) && count($data)>0){
                $datas = (string)json_encode($data);
                return $datas; 
            }else{
                return 0;
            }
        }else{
            return 0;
        }         
    }
    public function showAnnouncement($id){
        if($id){
            $data = Conference::select('announcement')->where('id',$id)->first();
            if(isset($data->announcement) && $data->announcement != ''){
                return $data->announcement;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    public function conferenceStatus($id){
        if($id){
            $data = Conference::select('is_start')->where('id',$id)->first();
            if(isset($data->is_start)){
                return $data->is_start;
            }else{
                return 0;
            }            
        }else{
            return 0;
        }
    }
    public function topVideos(){
        $datas = DB::table('news')
        ->select('news.id','news.name','slug','short_description as shortdescription','file_path','is_video','approved_date','users.name as username','journalist_name')
        ->leftJoin('users', 'news.user_id', '=', 'users.id')
        ->where(['is_video'=>1,'is_approved'=>1,'is_enable'=>1,'is_top_video'=>1,'news.is_trash'=>0])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
        ->OrderBy('position','ASC') 
        ->OrderBy('approved_date','DESC')
        ->take(25)->get();
        //->paginate(15);
        $advertisementPopup = Advertisement::getadvertisement(\Config::get('constants.ADVERTISEMENT_ON_POPUP'),\Config::get('constants.ADVERTISEMENT_TOP_VIDEO'));
        $data['data']                   = $datas;
        $data['advertisementPopup']     = $advertisementPopup;        
        return $data;          
    }
    public function viralVideos(){
        $datas = DB::table('news')
        ->select('news.id','news.name','slug','short_description as shortdescription','file_path','is_video','approved_date','users.name as username','journalist_name')
        ->leftJoin('users', 'news.user_id', '=', 'users.id')
        ->where(['is_video'=>1,'is_approved'=>1,'is_enable'=>1,'is_viral'=>1,'news.is_trash'=>0])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
        ->OrderBy('position','ASC') 
        ->OrderBy('approved_date','DESC')
        ->take(25)->get();
        //->paginate(15); 
        $advertisementPopup = Advertisement::getadvertisement(\Config::get('constants.ADVERTISEMENT_ON_POPUP'),\Config::get('constants.ADVERTISEMENT_VIRAL_VIDEO'));
        $data['data']                   = $datas;
        $data['advertisementPopup']     = $advertisementPopup;        
        return $data;                
    } 
    public function postUserDetails(Request $request){
        $data = $request->all();
        if(isset($data['socialsite_id']) && $data['socialsite_id'] != '' && isset($data['name']) && $data['name'] != ''){

            $socialUser = SocialUser::whereSocialsiteId($data['socialsite_id'])->first();
            if(isset($socialUser->id) && (int)$socialUser->id != 0){
                $socialUser->socialsite_id = $data['socialsite_id'];
            }else{
                $socialUser = new SocialUser();
            }
            $socialUser->socialsite_id = $data['socialsite_id'];
            $socialUser->name = $data['name'];
            if(isset($data['email']) && $data['email'] != ''){
                $socialUser->email = $data['email'];
            }
            if(isset($data['socialsite']) && $data['socialsite'] != ''){
                $socialUser->socialsite = $data['socialsite'];
            }
            if(isset($data['avatar']) && $data['avatar'] != ''){
                $socialUser->avatar = $data['avatar'];
            } 
            if(isset($data['avatar_original']) && $data['avatar_original'] != ''){
                $socialUser->avatar_original = $data['avatar_original'];
            } 
            if(isset($data['longitude']) && $data['longitude'] != ''){
                $socialUser->longitude = $data['longitude'];
            }
            if(isset($data['latitude']) && $data['latitude'] != ''){
                $socialUser->latitude = $data['latitude'];
            }   
            if($socialUser->save()){
                return 1;
            }else{
                return 0;
            }                                                          
        }else{
            return 0;
        }
    }               
}
