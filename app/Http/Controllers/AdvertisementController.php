<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Request;
use DB;
use App\Http\Requests;
use App\Advertisement;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAdvertisementFormRequest;
use Intervention\Image\Facades\Image; // Use this if you want facade style code
use App\Sponsor;
use App\NewsCategory;
use App\AdvertisementSection;
use App\AdvertisementType;
use DateTime;
use Input;
use Validator;
use Redirect;
use Session;
use Response;

class AdvertisementController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $advertisement = new Advertisement;
        $sponsorList = [''=>'-Select Sponsor-'] + Sponsor::where('is_enable',1)->lists('name', 'id')->toArray();
        $catList = [''=>'-Select News Category-'] + NewsCategory::where('is_active',1)->lists('name', 'id')->toArray();
        $advtTypeArr = AdvertisementType::where('is_enable',1)->lists('name','id')->toArray();
        $sectionArr = [''=>'--Select Advertisement Section--'] + AdvertisementSection::where(['is_enable'=>1,'advertisement_type_id'=>\Config::get('constants.ADVERTISEMENT_ON_PAGE')])->lists('name','id')->toArray();
        return view('advertisement.create', [
            'advertisement' => $advertisement,
            'sponsorList'   => $sponsorList,
            'catList'       => $catList, 
            'title'         => 'Add Advertisement',
            'advtTypeArr'   => $advtTypeArr, 
            'sectionArr'    => $sectionArr,
        ]);
    }

    //view function

    public function advertisementlist(Request $request)
    {
        $data = $request->all();
        /*$filter = Input::get('filter');

        if (isset($filter) && $filter == 'A'){
            $is_file = "Audio";
        }
        elseif(isset($filter) && $filter == 'I'){
            $is_file = "Image";
        }
        elseif(isset($filter) && $filter == 'V'){
            $is_file = "Video";
        }
        else{*/
        $advertisement = DB::table('advertisements as a')
        ->Select(['a.id', 'a.name as aname', 's.name as sname', 'c.name as cname', 'a.start_date', 'a.end_date', 'a.file_path', 'a.is_enable', 'a.is_publish', 'a.publish_date', 'a.file_type', 'd.name as sectionname'])
        ->leftJoin('sponsors as s', 's.id', '=', 'a.sponsor_id')
        ->leftJoin('news_categories as c', 'c.id', '=', 'a.cat_id')
        ->leftJoin('advertisement_sections as d', 'd.id', '=', 'a.advertisement_section_id')
        ->where('a.is_trash', 0)
        ->paginate(15);
        return view('advertisement.advertisementlist', ['advertisement' => $advertisement,'title'=>'Advertisement List' ]);   

/*        }
        $advertisement = DB::table('advertisements as a')
        ->Select(['a.id', 'a.name as aname', 's.name as sname', 'c.name as cname', 'a.start_date', 'a.end_date', 'a.file_path', 'a.is_enable', 'a.is_publish', 'a.publish_date', 'a.file_type'])
        ->leftJoin('sponsors as s', 's.id', '=', 'a.sponsor_id')
        ->leftJoin('news_categories as c', 'c.id', '=', 'a.cat_id')
        ->where('a.is_trash', 0)
        ->where('a.file_type', $is_file)
        ->paginate(15);

        return view('advertisement.advertisementlist', ['advertisement' => $advertisement,'title'=>'Advertisement List' ]);   
*/    } 

    //Edit function
    public function advertisementedit($id)
    {
        $advertisement = Advertisement::findOrFail($id);
        $sponsorList = [''=>'-Select Sponsor-'] + Sponsor::where('is_enable',1)->lists('name', 'id')->toArray();
        $catList = [''=>'-Select News Category-'] + NewsCategory::where('is_active',1)->lists('name', 'id')->toArray();
        $advtTypeArr = AdvertisementType::where('is_enable',1)->lists('name','id')->toArray();
        if(isset($advertisement->advertisement_type_id) && (int)$advertisement->advertisement_type_id != 0){
            $sectionArr = [''=>'--Select Advertisement Section--'] + AdvertisementSection::where(['is_enable'=>1,'advertisement_type_id'=>$advertisement->advertisement_type_id])->lists('name','id')->toArray();
        }else{
            $sectionArr = [''=>'--Select Advertisement Section--'];
        }
        if(isset($advertisement->start_date) && $advertisement->start_date != ''){
            $advertisement->start_date = date("d-m-Y", strtotime($advertisement->start_date));
        }
        if(isset($advertisement->end_date) && $advertisement->end_date != ''){
            $advertisement->end_date = date("d-m-Y", strtotime($advertisement->end_date));
        }                
        return view('advertisement.create', [
            'advertisement' => $advertisement,
            'title'         => 'Update Advertisement', 
            'sponsorList'   => $sponsorList, 
            'catList'       => $catList,
            'advtTypeArr'   => $advtTypeArr,
            'sectionArr'    => $sectionArr 
        ]);
    }

    //Delete function
    public function advertisementdelete($id)
    {
        $advertisement = Advertisement::find($id);
        if($advertisement) 
        {
            $advertisement->is_trash = '1';
            if($advertisement->save()){
                \Session()->flash('flash_message', 'Advertisement deleted successfully!');
            }else{
                \Session()->flash('error_message', 'Invalid request, please try again!');
            }
            return redirect('advertisement/list');
        }
    }

    //Enable/Disable Action
    public function advertisementaction($id){
        $advertisement = Advertisement::find($id);
        if(isset($advertisement->is_enable) && $advertisement->is_enable == 1){
            $advertisement['is_enable'] = 0;
        }else{
            $advertisement['is_enable'] = 1;
        }
        $advertisement->save();
        return redirect('advertisement/list');
    }

    //Publish/Unpublish 
    public function advertisementpublish($id){
        $advertisement = Advertisement::find($id);
        if(isset($advertisement->is_publish) && $advertisement->is_publish == 0){
            $advertisement['is_publish'] = 1;
            $advertisement['publish_date'] = date('Y-m-d H:i:s');
        }else{
            $advertisement['is_publish'] = 0;
        }
        $advertisement->save();
        return redirect('advertisement/list');
    }

    //Database submit function
    public function store(CreateAdvertisementFormRequest $request)
    {
        $data = $request->all();
        //Date format conversion
        if(isset($data['start_date']) && $data['start_date'] != ''){
            $data['start_date'] = date("Y-m-d", strtotime($data['start_date']));
        }

        if(isset($data['end_date']) && $data['end_date'] != ''){
            $data['end_date'] = date("Y-m-d", strtotime($data['end_date']));
        }
        //Check Publishing status
        $publish = Input::get('is_publish');
        if ($publish === 'yes') { 
            $data['is_publish'] = '1'; 
        }else{ 
            $data['is_publish'] = '0'; 
        }
        if(isset($data['id']) && (int)$data['id'] != 0){
            $advertisement = Advertisement::find($data['id']);
        }else { 
            $advertisement = new Advertisement(); 
        }
        if(isset($data['url_link']) && $data['url_link'] != ''){
            $data['is_url'] = 1;
        }else{
            $data['is_url'] = 0;
        }
        //File Upload
        if (Input::hasFile('file_path')){
            $filePath = public_path().'/file/advertisement/';
            $filedest = public_path().'/file/advertisement/original/';
            $file = \Input::file('file_path');
            $ffmpeg = "/usr/bin/ffmpeg";
            $size = "1280x720";
            $getFromSecond = 5;
            $timestamp = rand().time().rand();
            $name = $timestamp.'.'.pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $thumbName = $timestamp;
            $namewithoutext = pathinfo($thumbName, PATHINFO_FILENAME);
            $namewithnewext = $namewithoutext.'.jpg';
            $advertisement->file_path = $name;
            $data['file_path'] = $name;
            $cmd = "ffmpeg -i $file -an -ss $getFromSecond -s $size $filedest$namewithnewext";
            shell_exec($cmd);
            $file->move($filePath.'original/', $name);
        }
        $ex = $file->getClientOriginalExtension();

        if($ex === 'jpg' || $ex === 'jpeg' || $ex === 'png' || $ex === 'bmp'  || $ex === 'gif'){
            $data['file_type'] = 'Image';  
            $resizeImage = \Config::get('constants.ADVERTISEMENT_RESIZE_IMAGE');
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
        $advertisement->user_id = \Session::get('user_id');
        $advertisement->fill($data)->save();
        return redirect('advertisement/list');
    }
    public function getSection(Request $request){
        $data = $request->all();
        if(isset($data['advertisement_type_id']) && (int)$data['advertisement_type_id'] != 0){
            $typeArr = AdvertisementSection::where(['is_enable'=>1,'advertisement_type_id'=>$data['advertisement_type_id']])->lists('name','id')->toArray();
            if(is_array($typeArr) && count($typeArr)>0){
                echo "<option value=''>--Select Advertisement Section--</option>";
                foreach($typeArr as $typeKey=>$typeVal){
                    echo "<option value='".$typeKey."'>".$typeVal."</option>";
                }
            }else{
                echo '<option value="">--Select Advertisement Section--</option>'; 
            }
        }else{
            echo '<option value="">--Select Advertisement Section--</option>'; 
        }
    }
    public function showadd($id){
        if($id){
            $advertisement = Advertisement::find($id);
        }else{
            $advertisement = new Advertisement();
        }
        return view('advertisement.showadd', [
            'title'         => 'Advertisement',
            'id'            => $id,
            'advertisement' => $advertisement,
        ]);
    }
}
