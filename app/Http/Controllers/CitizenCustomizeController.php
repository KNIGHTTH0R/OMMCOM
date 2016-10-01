<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image; // Use this if you want facade style code
use App\CitizenCustomize;
use App\CitizenNews;
use App\News;
use Input;
use App\Http\Requests\CreateCitizenNewsFormRequest;
use App\Http\Requests\CreateCitizenCustomizeFormRequest;
use GrahamCampbell\Markdown\Facades\Markdown;

class CitizenCustomizeController extends Controller
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
        $citizenCustomize = new CitizenCustomize;
        return view('citizenJournalist.create', ['citizenCustomize' => $citizenCustomize, 'title'=>'Add Citizen Customization' ]);
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
        $citizenCustomize = CitizenCustomize::findOrFail($id);
        return view('citizenJournalist.create', ['citizenCustomize' => $citizenCustomize,'title'=>'Update Citizen Customization']);
    }

    //Delete function
    public function ccdelete($id)
    {
        $citizenCustomize = CitizenCustomize::find($id);
        if($citizenCustomize) 
        {
            $citizenCustomize->is_trash = '1';
            $citizenCustomize->save();
            return redirect('citizencustomize/list');
        }
    }

    //Enable/Disable Action
    public function ccaction($id){
        $citizenCustomize = CitizenCustomize::find($id);
        if(isset($citizenCustomize->is_enable) && $citizenCustomize->is_enable == 1){
            $citizenCustomize['is_enable'] = 0;
        }else{
            $citizenCustomize['is_enable'] = 1;
        }
        $citizenCustomize->save();
        return redirect('citizencustomize/list');
    }

    //Database submit function
    public function cusstore(CreateCitizenCustomizeFormRequest $request)
    {
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $citizenCustomize = CitizenCustomize::find($data['id']);
        }else { 
            $citizenCustomize = new CitizenCustomize(); 
        }

        //File Upload
        if (Input::file('file_path')->isValid()) 
        {
            $filePath = public_path().'/file/citizenCustomize/';
            if($request->hasFile('file_path')) {
                $file = \Input::file('file_path');
                $timestamp = rand().time().rand();
                $name = $timestamp. '-' .$file->getClientOriginalName();
                $citizenCustomize->file_path = $name;
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

        }else { 
            return Redirect::to('citizencustomize/add'); 
        }

        $citizenCustomize->user_id = \Session::get('user_id');
        $citizenCustomize->fill($data)->save();
        return redirect('citizencustomize/list');
    }

    public function cnewscreate()
    {
        $citizenNews = new CitizenNews;
        return view('citizenJournalist.citizenewscreate', ['citizenNews' => $citizenNews, 'title'=>'Add Citizen News' ]);
    }

    //view function
    public function cnewslist(Request $request)
    {
        $datas = DB::table('citizen_news')->where('is_trash', 0)->paginate(15);
        return view('citizenJournalist.citizenewslist', ['datas' => $datas,'title'=>'Citizen News List' ]);   
    }

    //Edit function
    public function cnewsedit($id)
    {
        $citizenNews = CitizenNews::findOrFail($id);
        $fil = $citizenNews['file_path'];
        //return $fil; exit();
        //$unlink = unlink(public_path('file/to/delete'));  
        //$delFile = File::delete('/citizenNews/original/' . $fil);
        return view('citizenJournalist.citizenewscreate', ['citizenNews' => $citizenNews,'title'=>'Update Citizen News']);
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
        if (Input::file('file_path')->isValid()) 
        {
            $filePath = public_path().'/file/citizenNews/';
            if($request->hasFile('file_path')) {
                $file = \Input::file('file_path');
                $timestamp = rand().time().rand();
                $name = $timestamp. '-' .$file->getClientOriginalName();
                $citizenNews->file_path = $name;
                $data['file_path'] = $name;
                $file->move($filePath.'original/', $name);
            }
            $ex = $file->getClientOriginalExtension();
            if($ex === 'jpg' || $ex === 'jpeg' || $ex === 'png' || $ex === 'bmp'  || $ex === 'gif'){
                $data['file_type'] = 'Image';  
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
            elseif($ex === 'wav' || $ex === 'mp3' || $ex === 'mpga') {
                $data['file_type'] = 'Audio';  
            }
            elseif($ex === 'mp4' || $ex === 'mov' || $ex === 'ogg') {
                $data['file_type'] = 'Video';  
            }
            else {
                return "error";
            }

        }else { 
            return Redirect::to('citizenews/add'); 
        }

        $citizenNews->user_id = \Session::get('user_id');
        $citizenNews->fill($data)->save();
        return redirect('citizenews/list');
    }

}