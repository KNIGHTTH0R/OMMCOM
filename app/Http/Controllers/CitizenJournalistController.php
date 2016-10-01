<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image; // Use this if you want facade style code
use App\CitizenCustomize;
use App\CitizenNews;
use Input;
use App\Http\Requests\CreateCitizenNewsFormRequest;
use App\Http\Requests\CreateCitizenCustomizeFormRequest;
use App\Http\Requests\CreateCitizenFormRequest;
use GrahamCampbell\Markdown\Facades\Markdown;

class CitizenJournalistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function create()
    {
        $CitizenCustomize = new CitizenCustomize;
        return view('citizenJournalist.create', ['CitizenCustomize' => $CitizenCustomize, 'title'=>'Add Citizen Customization' ]);
    }

    //view function
    public function cclist(Request $request)
    {
        $datas = DB::table('citizen_customizes')->where('is_trash', 0)->paginate(15);
        return view('citizenJournalist.citizencustomizelist', ['datas' => $datas,'title'=>'Citizen Customized List' ]);   
    } 

    //Edit function
    public function ccedit($id)
    {
        $CitizenCustomize = CitizenCustomize::findOrFail($id);
        return view('citizenJournalist.create', ['CitizenCustomize' => $CitizenCustomize,'title'=>'Update Citizen Customization']);
    }

    //Delete function
    public function ccdelete($id)
    {
        $CitizenCustomize = CitizenCustomize::find($id);
        if($CitizenCustomize) 
        {
            $CitizenCustomize->is_trash = '1';
            $CitizenCustomize->save();
            return redirect('citizencustomize/list');
        }
    }

    //Enable/Disable Action
    public function ccaction($id){
        $CitizenCustomize = CitizenCustomize::find($id);
        if(isset($CitizenCustomize->is_enable) && $CitizenCustomize->is_enable == 1){
            $CitizenCustomize['is_enable'] = 0;
        }else{
            $CitizenCustomize['is_enable'] = 1;
            
            DB::table('citizen_customizes')
            ->where('id','<>', $id)
            ->update(['is_enable' => 0]);
        }
        $CitizenCustomize->save();
        return redirect('citizencustomize/list');
    }

    //Database submit function
    public function cusstore(CreateCitizenCustomizeFormRequest $request)
    {
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $CitizenCustomize = CitizenCustomize::find($data['id']);
        }else { 
            $CitizenCustomize = new CitizenCustomize(); 
        }

        //File Upload
        if (Input::hasFile('file_path'))
        {
            $filePath = public_path().'/file/citizenCustomize/';
            if($request->hasFile('file_path')) {
                $file = \Input::file('file_path');
                $timestamp = rand().time().rand();
                $name = $timestamp. '-' .$file->getClientOriginalName();
                $CitizenCustomize->file_path = $name;
                $data['file_path'] = $name;
                $file->move($filePath.'original/', $name);
            }
            $resizeImage = \Config::get('constants.CITIZEN_RESIZE_IMAGE');
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

        }

        $CitizenCustomize->user_id = \Session::get('user_id');
        $CitizenCustomize->fill($data)->save();
        return redirect('citizencustomize/list');
    }
    public function thumbnailStore(Request $request){
        $data = $request->all();
        if(isset($data['filename']) && $data['filename'] != ''){
            $filePath = public_path().'/file/citizenNews/original/';
            if($request->hasFile('file_path')){
                $file = \Input::file('file_path');
                $extension = strtolower($file->getClientOriginalExtension());
                if($extension == 'jpg'){
                    if(file_exists($filePath.$data['filename'])){
                        unlink($filePath.$data['filename']);
                    }
                    if($file->move($filePath, $data['filename'])){
                        \Session()->flash('flash_message', 'Thumbnail image change successfully!');
                    }else{
                        \Session()->flash('error_message', 'Thumbnail image not uploaded!');
                    }
                }else{
                    \Session()->flash('error_message', 'Please upload only .jpg file!');
                }
            }else{
                \Session()->flash('error_message', 'Please upload only .jpg file!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('citizenews/list');
    }
    public function cnewscreate()
    {
        $citizenNews = new CitizenNews;
        return view('citizenJournalist.citizenewscreate', ['citizenNews' => $citizenNews, 'title'=>'Add Citizen Journalist' ]);
    }

    //view function
    public function cnewslist(Request $request)
    {   
        $records = $request->all();
        $firstDate = '';
        $endDate = '';
        //if(is_array($records) && count($records)>0){
            if(isset($records['start_date']) && $records['start_date'] != ''){  
                $start_date = date('Y-m-d',strtotime($records['start_date']));
                $firstDate = $records['start_date'];
            }
            else {
                $start_date = '1990-01-01';
            }
            
            if(isset($records['end_date']) && $records['end_date'] != ''){  
                $end_date = date('Y-m-d',strtotime($records['end_date']));
                $end_date = date('Y-m-d H:i:s', strtotime($end_date . ' +1 day'));
                $endDate = $records['end_date'];
            }
            else{
                $end_date = date('Y-m-d');
                $end_date = date('Y-m-d H:i:s', strtotime($end_date . ' +1 day'));
            } 
        //}
        $datas = DB::table('citizen_news')
        ->where('is_trash', 0)
        ->whereBetween('created_at', array($start_date, $end_date))
        ->OrderBy('created_at', 'DESC')
        ->paginate(30);
        return view('citizenJournalist.citizenewslist', ['datas' => $datas,'title'=>'Citizen Journalist List','firstDate'=>$firstDate,'endDate'=>$endDate ]);   
    }

    //Edit function
    public function cnewsedit($id)
    {
        $citizenNews = CitizenNews::findOrFail($id);
        $fil = $citizenNews['file_path'];
        //return $fil; exit();
        //$unlink = unlink(public_path('file/to/delete'));  
        //$delFile = File::delete('/citizenNews/original/' . $fil);
        return view('citizenJournalist.citizenewscreate', ['citizenNews' => $citizenNews,'title'=>'Update Citizen Journalist']);
    }
    /*
     *Function for download video
     */
    /*
    public function download($filename){
        $file = Storage::disk('local')->get($entry->filename);
        return (new Response($file, 200))
              ->header('Content-Type', $entry->mime);
    }*/
    public function download($filename) {
        $file_path = public_path('file/citizenNews/original/'.$filename);
        return response()->download($file_path);
    }    
    //Delete function
    public function cnewsdelete($id)
    {
        $citizenNews = CitizenNews::find($id);
        if($citizenNews) 
        {
            $citizenNews->is_trash = '1';
            $citizenNews->save();
            return redirect('citizenews/list');
        }
    }

    //Enable/Disable Action
    public function cnewsaction($id){
        $citizenNews = CitizenNews::find($id);
        if(isset($citizenNews->is_enable) && $citizenNews->is_enable == 1){
            $citizenNews['is_enable'] = 0;
        }else{
            $citizenNews['is_enable'] = 1;
        }
        $citizenNews->save();
        return redirect('citizenews/list');
    }

    //Database submit function
    public function cnewsstore(CreateCitizenNewsFormRequest $request)
    {
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $citizenNews = CitizenNews::find($data['id']);
        }else { 
            $citizenNews = new CitizenNews(); 
        }

        //File Upload
        if ($request->hasFile('file_path')){
            $filePath = public_path().'/file/citizenNews/';
            
            $file = \Input::file('file_path');
            $timestamp = rand().time().rand();
            $ex = strtolower($file->getClientOriginalExtension());
            $name = $timestamp.'.'.$ex;
            $citizenNews->file_path = $name;
            $data['file_path'] = $name;
            $file->move($filePath.'original/', $name);
            
            
            if(array_key_exists($ex, \Config::get('constants.IMAGE_EXTENSION'))){
                $data['file_type'] = 'Image';  
                $resizeImage = \Config::get('constants.RESIZE_IMAGE');
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
            }else if(array_key_exists($ex, \Config::get('constants.AUDIO_EXTENSION'))) {
                $data['file_type'] = 'Audio';  
            }else if(array_key_exists($ex, \Config::get('constants.VIDEO_EXTENSION'))) {
                $data['file_type'] = 'Video';  

                $ffmpeg = "/usr/bin/ffmpeg";
                $imageFile = "1.jpg";
                $size = "1280x720";
                $getFromSecond = 5;  
                $namewithoutext = pathinfo($name, PATHINFO_FILENAME);
                $namewithnewext = $namewithoutext.'.jpg';
                $videoFile = $filePath.'original/'.$name;
                $destFile = $filePath.'original/'.$namewithnewext;
                $cmd = "ffmpeg -i $videoFile -an -ss $getFromSecond -s $size $destFile";
                shell_exec($cmd); 
                if(array_key_exists($ex, \Config::get('constants.CONVERT_VIDEO'))){ 
                    $filedest = $filePath.'original/';
                    $newName = $timestamp.'.mp4';
                    $cmd = "ffmpeg -i $videoFile -strict -2 -movflags faststart $filedest$newName 2>&1";
                    if(shell_exec($cmd)){
                        $citizenNews->file_path = $newName;
                        $data['file_path']      = $newName;
                    }
                }                               
            }
        }
        $citizenNews->user_id = \Session::get('user_id');
        $citizenNews->fill($data)->save();
        return redirect('citizenews/list');
    }
    public function news(){
        $citizenNews = new CitizenNews();
        /*
         *Query for get the 
         */        
        $datas = DB::table('citizen_news')
        ->select('id','name','file_path','file_type','description','slug','is_anonymous','news_count','updated_at')
        ->where(['is_enable'=>1,'is_trash'=>0])
        ->OrderBy('updated_at','DESC')
        ->OrderBy('position','ASC')        
        ->paginate(20);
        
        $mostVieweddatas = DB::table('citizen_news')
        ->select('id','name','file_path','file_type','description','slug','is_anonymous','news_count','updated_at')
        ->where(['is_enable'=>1,'is_trash'=>0])
        ->OrderBy('news_count','DESC')    
        ->paginate(20); 

        $metaDesc       = "The OMMCOM is Odisha's first independent online news Website. We offer a combination of news and views, with behind-the-news latest videos news.";
        $metaKeywords   = "Odisha news, Odisha live news, Odisha news, Odisha news bulletin, Latest Odisha news, odisha news portal, odia news, ommcom news, odisha top news site, top odisha news website, orissa news, best orissa news, best odisha news, online odisha news, online orissa news, online news odisha, news in india, breaking news, today news, current news, indian news, news website, india news, world news, business news, bollywood news, cricket news, sports, lifestyle, gadgets, tech news, video news, online tv news, news on videos, latest news on videos, economy news, political news, celebrity news, international news, current affairs, top news, weekly news, local news, news headlines, news website, stock market, mutual funds, Hindi movies, India online shopping, ollywood news, bhubaneswar news, cuttack news, puri news, barahampur news, sambalpur news, Rourkela news, automobile news, property news, vacancy news, education news, odisha live tv, oriya live tv news, odia news videos, cinema news, best news website odisha, odisha no1 news site, odisha’s popular news site, odisha news headlines, odisha breaking news, 24*7 odisha news, 24x7 odisha news, popular oriya news site";
        $title          = "OMMCOM News : Odisha Citizen News, OMMCOM Citizen News, Online Odisha News";  

        return view('citizenJournalist.news', [
            'title'             => 'Citizen Journalist',
            'datas'             => $datas,
            'citizenNews'       => $citizenNews, 
            'mostVieweddatas'   => $mostVieweddatas,
            'metaDesc'          => $metaDesc,
            'metaKeywords'      => $metaKeywords,
            'title'             => $title,
        ]);       
    } 
    public function newstore(CreateCitizenFormRequest $request){
        $data = $request->all();
        $citizenNews = new CitizenNews();
        $resizeImage = \Config::get('constants.RESIZE_IMAGE');
        $filePath = public_path().'/file/citizenNews/';

        if($request->hasFile('file_path')) {
            $filelink   = \Input::file('file_path');
            $extension  = strtolower($filelink->getClientOriginalExtension());
            $timestamp  = rand().time().rand();
            $name = $timestamp.'.'.$extension;
            $citizenNews->file_path     = $name;
            $data['file_path']   = $name;
            
            $filelink->move($filePath.'original/', $name);
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
                $ffmpeg = "/usr/bin/ffmpeg";
                $imageFile = "1.jpg";
                $size = "1280x720";
                $getFromSecond = 2;  
                $namewithoutext = pathinfo($name, PATHINFO_FILENAME);
                $namewithnewext = $namewithoutext.'.jpg';
                $videoFile = $filePath.'original/'.$name;
                $destFile = $filePath.'original/'.$namewithnewext;
                $cmd = "ffmpeg -i $videoFile -an -ss $getFromSecond -s $size $destFile";
                shell_exec($cmd);
                if(array_key_exists($extension, \Config::get('constants.CONVERT_VIDEO'))){
                    $newName = $timestamp.'.mp4';
                    $filedest = $filePath.'original/';
                    $cmd = "ffmpeg -i $videoFile -strict -2 -movflags faststart $filedest$newName 2>&1";
                    if(shell_exec($cmd)){
                        $citizenNews->file_path     = $newName;
                        $data['file_path']          = $newName;                        
                    }
                }  
                $citizenNews->file_type = 'Video';
            }else if(array_key_exists($extension, \Config::get('constants.AUDIO_EXTENSION'))){
                $citizenNews->file_type = 'Audio';
            }
        }

        $data['slug'] = $data['slug'].'-'.time().'-'.rand();

        if($citizenNews->fill($data)->save()){
            \Session()->flash('flash_message', 'Your news submitted successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('citizen/news');
    }
    public function citizenNews($slug){
        $datas = CitizenNews::select('id','name','file_path','file_type','description','long_description','slug','news_count')->where(['slug'=>$slug])->first();
        if(isset($datas->news_count) && (int)$datas->news_count != 0){
            $datas->news_count = $datas->news_count + 1;
        }else{
            $datas->news_count = 1;
        }
        $datas->save();       
        $socialImage = '';
        $socialVideo = '';  
        
        $news_desc = $datas->long_description;
        $name      = $datas->name;
        if(isset($datas->file_type) && $datas->file_type == 'Video' && isset($datas->file_path) && $datas->file_path != ''){
            $namewithoutext = pathinfo($datas->file_path, PATHINFO_FILENAME);
            $namewithnewext = $namewithoutext.'.jpg';
            $socialImage    = $namewithnewext;
            $socialVideo    = $datas->file_path;
        }else if(isset($datas->file_type) && $datas->file_type == 'Image' && isset($datas->file_path) && $datas->file_path != ''){
            $socialImage    = $datas->file_path;
        }
        return view('citizenJournalist.citizenNews', [
            'title'         => 'Citizen Journalist Details',
            'datas'         => $datas,
            'socialImage'   => $socialImage,
            'socialVideo'   => $socialVideo,
            'news_desc'     => $news_desc,
            'name'          => $name,
            'citizenTwitter'=> 1,
        ]);
    }    
}