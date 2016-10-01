<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\UserType;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\CreateChangePasswordRequest;
use App\Common;
use App\News;
use App\Conference;
use App\CitizenCustomize;
use App\Question;
use App\Answer;
use App\BreakingNews;
use App\Footer;
use Mail;
use Input;
use App\Http\Requests\CreateFooterFormRequest;
use Cookie;
use App\TopNews;
class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        /*
         *Query for get the top news
         */
        /*
        $topNews = News::select('news.id','news.name','slug','featured_image','short_description','is_video','is_image','file_path','approved_date','position','user_id','users.name as username','meta_desc','meta_keywords','journalist_name','news_location')
        ->leftJoin('users','users.id','=','news.user_id')
        ->where(['is_approved'=>1,'is_hot'=>1,'is_featured'=>1,'is_archive'=>0,'is_enable'=>1,'news.is_trash'=>0])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')->OrderBy('position','ASC')->OrderBy('approved_date','DESC')->first();
        */
        $topNews = TopNews::select('news.id','news.name','news.slug','news.featured_image','news.short_description','news.is_video','news.is_image','news.file_path','news.approved_date','news.position','news.user_id','users.name as username','news.meta_desc','news.meta_keywords','news.journalist_name','news.news_location')
        ->leftJoin('news','news.id','=','top_news.news_id')
        ->leftJoin('users','users.id','=','news.user_id')
        ->where(['news.is_approved'=>1,'news.is_hot'=>1,'news.is_featured'=>1,'news.is_archive'=>0,'news.is_enable'=>1,'news.is_trash'=>0])
        ->OrderBy('top_news.position','ASC')->first();

        $metaDesc       = "Ommcom News Odisha's most popular Odia News site provides Latest News, Breaking News from Odisha. Get 24*7 News Updates Online on Ommcom News";
        $metaKeywords   = "odisha news, odisha live news, orissa news, odisha news bulletin, Latest odisha news, odisha news portal, odia news, ommcom news, odisha top news site, top odisha news website, orissa news, best orissa news, best odisha news, online odisha news, online orissa news, online news odisha, news in india, breaking news, today news, current news, indian news, news website, india news, world news, business news, bollywood news, cricket news, sports, lifestyle, gadgets, tech news, video news, online tv news, news on videos, latest news on videos, economy news, political news, celebrity news, international news, current affairs, top news, weekly news, local news, news headlines, news website, stock market, mutual funds, Hindi movies, India online shopping, ollywood news, bhubaneswar news, cuttack news, puri news, barahampur news, sambalpur news, Rourkela news, automobile news, property news, vacancy news, education news, odisha live tv, oriya live tv news, odia news videos, cinema news, best news website odisha, odisha no1 news site, odisha’s popular news site, odisha news headlines, odisha breaking news, 24*7 odisha news, 24x7 odisha news, popular oriya news site";
        $title          = "OMMCOM News : Latest Odisha News, Breaking News, Business, Bollywood, Cricket";
        /*
         *Query for get the conference
         */
        $conferenceNews = Conference::select('id','name','short_desc','long_desc','featured_image','conference_banner','started_at','start_time','end_time','slug')
        ->where(['is_enable'=>1,'is_archive'=>0,'is_trash'=>0,'is_close'=>0])
        ->OrderBy('started_at','ASC')->take(2)->get();
        /*
         *Query for get the top news now
         */
        /*
        $topNewsNow = News::select('news.id','news.name','slug','featured_image','is_image','file_path','is_video')
        ->where(['is_approved'=>1,'is_hot'=>1,'is_featured'=>1,'is_archive'=>0,'is_enable'=>1,'is_trash'=>0])
        ->where('news.id','!=',$topNews->id)
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')->OrderBy('position','ASC')->OrderBy('approved_date','DESC')->take(4)->get();
        */
        $topNewsNow = TopNews::select('news.id','news.name','news.slug','news.featured_image','news.is_image','news.file_path','news.is_video')
        ->leftJoin('news','news.id','=','top_news.news_id')
        ->where(['news.is_approved'=>1,'news.is_hot'=>1,'news.is_featured'=>1,'news.is_archive'=>0,'news.is_enable'=>1,'news.is_trash'=>0])
        ->where('news.id','!=',$topNews->id)
        ->OrderBy('top_news.position','ASC')->take(4)->get();
        /*
         *Query for get the questions and answers
         */
        $pollQuestions = Question::where(['is_active'=>1,'is_trash'=>0])->first();

        $answer = new Answer();

        return view('users.index', [
            'topNews'           => $topNews,
            'topNewsNow'        => $topNewsNow,
            'conferenceNews'    => $conferenceNews,
            'pollQuestions'     => $pollQuestions,
            'answer'            => $answer,
            'metaDesc'          => $metaDesc,
            'metaKeywords'      => $metaKeywords,
            'title'             => $title,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = new User;
        $usertypeList = [''=>'-Select User Type-'] + UserType::where(['is_active'=>1,'is_trash'=>0])->lists('name', 'id')->toArray();
        $opt = 0;
        return view('users.create', ['user' => $user,'usertypeList'=>$usertypeList,'title'=>'Add Users','opt'=>$opt ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterRequest $request)
    {
        $data = $request->all();
        $common = new Common();
        $data['password']   = bcrypt($data['password']);
        if(isset($data['dob']) && $data['dob'] != ''){
            $data['dob'] = $common->date2DB($data['dob']);
        }
        if(isset($data['id']) && (int)$data['id'] != 0){
            $user = User::find($data['id']);
        }else{
            $user = new User();
        }
        if ($request->hasFile('profile_image')){
            $filePath = public_path().'/file/profile/';
            $file = Input::file('profile_image');
            $timestamp = rand().time().rand();
            $extension  = $file->getClientOriginalExtension();
            $name = $timestamp.'.'.$extension;
            $user->profile_image = $name;
            $data['profile_image'] = $name;
            $file->move($filePath, $name);
            \File::copy($filePath.$name, $filePath.'thumb/'.$name);
            \Image::make($filePath.'thumb/'.$name)->resize(80,70)->save($filePath.'thumb/'.$name);
        }
        if($user->fill($data)->save()){
            \Session()->flash('flash_message', 'User created successfully!');
            return redirect('users/manage');
        }else{
             \Session()->flash('error_message', 'Invalid request, please try again!');
             return redirect('users/add');
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function useredit($id)
    {
        $usertypeList = [''=>'-Select User Type-'] + UserType::where(['is_active'=>1,'is_trash'=>0])->lists('name', 'id')->toArray();
        $user = User::findOrFail($id);
        $opt = 1;
        return view('users.create', ['user' => $user,'usertypeList'=>$usertypeList,'title'=>'Update User','opt'=>$opt ]);
    }
    public function userprofile()
    {
        $profile = DB::table('users as u')->select('u.*', 'ut.name as type_name')
        ->leftJoin('user_types as ut', 'ut.id', '=', 'u.user_type_id')
        ->where('u.id',\Session::get('user_id'))->get();
        $is_publish = DB::table('news')->where('is_publish',1)->where('user_id',\Session::get('user_id'))->count();
        $is_approve = DB::table('news')->where('is_approved',1)->where('user_id',\Session::get('user_id'))->count();
        $is_draft = DB::table('news')->where('is_draft',1)->where('user_id',\Session::get('user_id'))->count();
        return view('users.profile', ['title'=>'My Profile', 'profile'=>$profile, 'is_publish'=>$is_publish, 'is_approve'=>$is_approve, 'is_draft'=>$is_draft]);
    }
    public function addprofileimage(Request $request){
         $data = $request->all();
         $pic = $data['profile_image'];
        //$user = new User;
         return $datas = Input::all(); exit();

        if (Input::hasFile('profile_image'))
        {
            //return "hello"; exit();
            $filePath = public_path().'/file/profile/';
            $file = \Input::file('profile_image');
            $timestamp = rand().time().rand();
            $name = $timestamp. '-' .$file->getClientOriginalName();
            $data['profile_image'] = $name;
            $file->move($filePath, $name);
            $DB::table('users')->where('id',\Session::get('user_id'))
            ->update(['profile_image' => $name]);
        }
        //$user->save();
        return redirect('users/profile');
    }
    public function userdelete($id){
        $user = User::find($id);
        $user->is_trash = 1;
        $user->save();
        return redirect('users/manage');
    }
    public function action($id){
        $user = User::find($id);
        if(isset($user->is_active) && $user->is_active == 1){
            $user->is_active = 0;
        }else{
            $user->is_active = 1;
        }
        $user->save();
        return redirect('users/manage');
    }
    public function userlist(){

/*$advertisement = DB::table('advertisements')->
Select(['advertisements.id', 'advertisements.name as aname', 'sponsors.name as sname', 'advertisements.start_date', 'advertisements.end_date', 'advertisements.file_path', 'advertisements.is_enable', 'advertisements.is_publish', 'advertisements.publish_date'])
->leftJoin('sponsors', 'sponsors.id', '=', 'advertisements.sponsor_id')
->where('advertisements.is_trash', 0) ->paginate*/


        $datas = DB::table('users')->Select('users.id','users.name','users.email','users.contact_no','users.dob','users.age','users.city','users.is_active','user_types.name as usertypename')
        ->leftJoin('user_types','user_types.id','=','users.user_type_id')
        ->where('users.is_trash',0)->paginate(15);
        return view('users.userlist', ['title'=>'Manage Users','datas'=>$datas ]);
    }
    public function age(Request $request){
        $data = $request->all();
        if($data['started_at'] != ''){
            $common = new Common();
            $started_at = $common->date2DB($data['started_at']);
            $closed_at = date('Y-m-d');
            $age = round(abs(strtotime($started_at)-strtotime($closed_at))/31556926);
            echo $age;
        }else{
            echo '';
        }
    }
    /**
     * [storepoll description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function storepoll(Request $request){
        $data = $request->all();
        if(isset($data['answer'])){
            $answer = new Answer();
            $answer->question_id            = $data['question_id'];
            $answer->question_option_id     = $data['answer'];
            $answer->ip_address             = Common::getMac();//get_client_ip();
            $answer->user_agent             = $_SERVER['HTTP_USER_AGENT'];
            $answer->is_anonymous           = 0;
            if($answer->save()){
                \Session()->flash('flash_message', 'Your poll has been saved successfully!');
            }else{
                \Session()->flash('error_message', 'Invalid request, Please try again!');
            }
            return redirect('/')->withCookie(cookie('question', $data['question_id'], 3600));
        }else{
            return redirect('/');
        }
        //return response()->view('users.index')->withCookie(cookie('coupon', $data['question_id'], 3600));
    }



    public function forgotpassword()
    {
        return view('users.forgotpassword', ['title'=>'Forgot Password' ]);
    }
    public function forgotpasswordstore(Request $request){
        $res = $request->all();
        $mailid = $res['mail'];
        $chkmail = DB::table('users')->where('email', $mailid)->first();
        if(count($chkmail) > 0){
            $content = "Hi,".$chkmail->name."! Please click on the link below to change your password.";
            $id = $chkmail->id;
            $data = [
                'content' => $content,
                'id' => $id
            ];
            Mail::send('users.display',$data, function($message) use ($mailid){
                $message->to($mailid)->subject('Welcome!');
            });
        }
        return redirect('users/login');
    }
    public function changepassword($id = 0){
        $user = new User();
        if(base64_decode($id)){
            $id = base64_decode($id);
            $layoutpage = 'layout.passlayout';
            $user->id = $id;
        }else{
            $layoutpage = 'layout.default';
            $user->id = \Session::get('user_id');
        }
        return view('users.changepassword', [
            'user'          => $user,
            'title'         => 'Change Password',
            'layoutpage'    => $layoutpage,
        ]);
    }
    public function changepasswordstore(CreateChangePasswordRequest $request){
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $user = User::find($data['id']);
            if(\Hash::check($data['old_password'], $user->password)){
                $user->password = bcrypt($data['new_password']);
                array_forget($data, 'conf_password');
                array_forget($data, 'old_password');
                if($user->save()){
                    \Session()->flash('flash_message', 'Password changed successfully!');
                    if($data['pass'] == 'layout.passlayout'){
                        return redirect('users/redirectpage');
                    }else{
                        return redirect('users/changepassword');
                    }
                }else{
                    \Session()->flash('error_message', 'Invalid request, please try again!');
                }
            }else{
                \Session()->flash('error_message', 'Old password is mismatch!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
    }
    public function redirectpage(){
        return view('users.passredirect', [
            'title'         => 'Password Changed successfully',
        ]);        
    }
    public function manageFooter(){
        $footer = Footer::find(1);
        return view('users.footer', [
            'footer'        => $footer,
            'title'         => 'Manage Footer',
        ]);
    }
    public function footerstore(CreateFooterFormRequest $request){
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $footer = Footer::find($data['id']);
            if($footer->fill($data)->save()){
                \Session()->flash('flash_message', 'Footer details updated successfully!');
            }else{
                \Session()->flash('error_message', 'Invalid request, please try again!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('footer');
    }
}
