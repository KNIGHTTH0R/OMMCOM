<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\News;
use App\NewsImage;
use App\NewsVideo;
use App\NewsCategory;
use App\NewsComment;
use App\CitizenNews;
use App\Common;
use App\BreakingNews;
use App\NewsTopVideo;
use App\TopNews;
use App\Resize;
use App\Http\Requests\CreateBreakingNewsFormRequest;
use App\Http\Requests\CreateNewsFormRequest;
use App\Http\Requests\CreateNewsImageFormRequest;
use App\Http\Requests\CreateNewsVideoFormRequest;
use App\Http\Requests\CreateNewsCommentFormRequest;
use App\Http\Requests\CreateTopNewsVideoFormRequest;
use Intervention\Image\Facades\Image; // Use this if you want facade style code
//use Intervention\Image\ImageManager // Use this if you don't want facade style code
use GrahamCampbell\Markdown\Facades\Markdown;


class NewsController extends Controller
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

    public function create(){
        $news = new News;
        $newsCatList = [''=>'-Select News Category-'] + NewsCategory::where(['parent_id'=>0,'is_active'=>1])->OrderBy('name','asc')->lists('name', 'id')->toArray();
        $subCatList = array(''=>'--Select--');
        return view('news.create', ['news' => $news,'newsCatList'=>$newsCatList,'title'=>'Add News','user_type_id'=>\Session::get('user_type_id'),'subCatList'=>$subCatList ]);
    }

    public function store(CreateNewsFormRequest $request){
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $news = News::find($data['id']);
        }else{
            $news = new News();
            $news->is_approved = 0;
        }
        $resizeImage = \Config::get('constants.RESIZE_IMAGE');
        $filePath = public_path().'/file/news/';
        if($request->hasFile('featured_image')) {
            $file = \Input::file('featured_image');
            $timestamp = rand().time().rand();
            $name = $timestamp.'.'.strtolower($file->getClientOriginalExtension());
            $news->featured_image = $name;
            $data['featured_image'] = $name;
            $file->move($filePath.'original/', $name);
            //****Resizing Images **/////////
        if(file_exists($filePath.'original/'.$name)){

$ext = pathinfo($filePath.'original/'.$name, PATHINFO_EXTENSION);
if($ext == "jpg" || $ext == 'png' || $ext == 'jpeg' || $ext == 'gif'){
  $resizeObj = new resize($filePath.'original/'.$name);
  $resizeObj -> resizeImage(285, 170, 'exact');
  $resizeObj -> saveImage(public_path().'/file/news/exact_285_170/'.$name, 100);

  $resizeObj = new resize($filePath.'original/'.$name);
  $resizeObj -> resizeImage(560.875, 360, 'exact');
  $resizeObj -> saveImage(public_path().'/file/news/exact_560_360/'.$name, 100);

  $resizeObj = new resize($filePath.'original/'.$name);
  $resizeObj -> resizeImage(360, 180, 'exact');
  $resizeObj -> saveImage(public_path().'/file/news/exact_360_180/'.$name, 100);

  $resizeObj = new resize($filePath.'original/'.$name);
  $resizeObj -> resizeImage(93.6563, 73.1563, 'auto');
  $resizeObj -> saveImage(public_path().'/file/news/exact_93_73/'.$name, 100);

  $resizeObj = new resize($filePath.'original/'.$name);
  $resizeObj -> resizeImage(350, 224, 'exact');
  $resizeObj -> saveImage(public_path().'/file/news/exact_350_224/'.$name, 100);

  $resizeObj = new resize($filePath.'original/'.$name);
  $resizeObj -> resizeImage(375, 200, 'exact');
  $resizeObj -> saveImage(public_path().'/file/news/exact_375_200/'.$name, 100);

  $resizeObj = new resize($filePath.'original/'.$name);
  $resizeObj -> resizeImage(750, 360, 'exact');
  $resizeObj -> saveImage(public_path().'/file/news/exact_750_360/'.$name, 100);
}

        }

            //**Resizing Completed*////////
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
        if($request->hasFile('file_path')) {

            $filelink = \Input::file('file_path');
            $filePath = public_path().'/file/news/';
            $filedest = public_path().'/file/news/original/';

            $ffmpeg = "/usr/bin/ffmpeg";
            $size = "1280x720";
            $getFromSecond = 5;

            $extension  = strtolower($filelink->getClientOriginalExtension());
            $timestamp = rand().time().rand();
            $name = $timestamp.'.'.strtolower($extension);
            //$thumbName = $timestamp;
            //$namewithoutext = pathinfo($name, PATHINFO_FILENAME);
            $namewithnewext = $timestamp.'.jpg';

            $news->file_path = $name;
            $data['file_path']   = $name;

            $filelink->move($filePath.'original/', $name);
            /**New Image Resizing**/
            if(file_exists($filePath.'original/'.$name)){
              $ext = pathinfo($filePath.'original/'.$name, PATHINFO_EXTENSION);
              if($ext == "jpg" || $ext == 'png' || $ext == 'jpeg' || $ext == 'gif'){
              $resizeObj = new resize($filePath.'original/'.$name);
              $resizeObj -> resizeImage(285, 170, 'exact');
              $resizeObj -> saveImage(public_path().'/file/news/exact_285_170/'.$name, 100);

              $resizeObj = new resize($filePath.'original/'.$name);
              $resizeObj -> resizeImage(560.875, 360, 'exact');
              $resizeObj -> saveImage(public_path().'/file/news/exact_560_360/'.$name, 100);

              $resizeObj = new resize($filePath.'original/'.$name);
              $resizeObj -> resizeImage(360, 180, 'exact');
              $resizeObj -> saveImage(public_path().'/file/news/exact_360_180/'.$name, 100);

              $resizeObj = new resize($filePath.'original/'.$name);
              $resizeObj -> resizeImage(93.6563, 73.1563, 'auto');
              $resizeObj -> saveImage(public_path().'/file/news/exact_93_73/'.$name, 100);

              $resizeObj = new resize($filePath.'original/'.$name);
              $resizeObj -> resizeImage(350, 224, 'exact');
              $resizeObj -> saveImage(public_path().'/file/news/exact_350_224/'.$name, 100);

              $resizeObj = new resize($filePath.'original/'.$name);
              $resizeObj -> resizeImage(375, 200, 'exact');
              $resizeObj -> saveImage(public_path().'/file/news/exact_375_200/'.$name, 100);

              $resizeObj = new resize($filePath.'original/'.$name);
              $resizeObj -> resizeImage(750, 360, 'exact');
              $resizeObj -> saveImage(public_path().'/file/news/exact_750_360/'.$name, 100);
            }
          }

            /**Resizing Completed**/

            if(array_key_exists($extension, \Config::get('constants.IMAGE_EXTENSION'))){
                $news->is_image = 1;
                $news->is_video = 0;
                if(is_array($resizeImage) && count($resizeImage)>0){
                    foreach($resizeImage as $resizeKey=>$resizeVal){
                        if(!is_dir($filePath.$resizeKey)){
                            mkdir($filePath.$resizeKey,0777);
                        }
                        $resizeValArr = explode(',',$resizeVal);
                        \File::copy($filePath.'original/'.$name, $filePath.$resizeKey.'/'.$name);
                        ////////////////////////////////////
                        $resizeObj = new resize($filePath.'original/'.$name);
                        $resizeObj -> resizeImage(560.875, 360, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_560_360/'.$name, 100);

                        $resizeObj = new resize($filePath.'original/'.$name);
                        $resizeObj -> resizeImage(285, 170, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_285_170/'.$name, 100);

                        $resizeObj = new resize($filePath.'original/'.$name);
                        $resizeObj -> resizeImage(360, 180, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_360_180/'.$name, 100);

                        $resizeObj = new resize($filePath.'original/'.$name);
                        $resizeObj -> resizeImage(93.6563, 73.1563, 'auto');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_93_73/'.$name, 100);

                        $resizeObj = new resize($filePath.'original/'.$name);
                        $resizeObj -> resizeImage(350, 224, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_350_224/'.$name, 100);

                        $resizeObj = new resize($filePath.'original/'.$name);
                        $resizeObj -> resizeImage(375, 200, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_375_200/'.$name, 100);

                        $resizeObj = new resize($filePath.'original/'.$name);
                        $resizeObj -> resizeImage(750, 360, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_750_360/'.$name, 100);
                        //////////////////////////////////////
                        \Image::make($filePath.$resizeKey.'/'.$name)->resize($resizeValArr[0],$resizeValArr[1])->save($filePath.$resizeKey.'/'.$name);
                    }
                }
            }else if(array_key_exists(strtolower($extension), \Config::get('constants.VIDEO_EXTENSION'))){
                $cmd = "ffmpeg -i $filedest$name -an -ss $getFromSecond -s $size $filedest$namewithnewext";

                /////////////////////////////////
            /*    $resizeObj = new resize($filedest.$namewithnewext);
                $resizeObj -> resizeImage(560.875, 360, 'exact');
                $resizeObj -> saveImage(public_path().'/file/news/exact_560_360/'.$namewithnewext, 100);

                $resizeObj = new resize($filedest.$namewithnewext);
                $resizeObj -> resizeImage(285, 170, 'exact');
                $resizeObj -> saveImage(public_path().'/file/news/exact_285_170/'.$namewithnewext, 100);*/
                /////////////////////////////////
                shell_exec($cmd);
				
				$resizeObj = new resize($filePath.'original/'.$namewithnewext);
                        $resizeObj -> resizeImage(285, 170, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_285_170/'.$namewithnewext, 100);

$resizeObj = new resize($filePath.'original/'.$namewithnewext);
                        $resizeObj -> resizeImage(350, 224, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_350_224/'.$namewithnewext, 100);	

$resizeObj = new resize($filePath.'original/'.$namewithnewext);
                        $resizeObj -> resizeImage(360, 180, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_360_180/'.$namewithnewext, 100);	

$resizeObj = new resize($filePath.'original/'.$namewithnewext);
                        $resizeObj -> resizeImage(375, 200, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_375_200/'.$namewithnewext, 100);	

$resizeObj = new resize($filePath.'original/'.$namewithnewext);
                        $resizeObj -> resizeImage(553, 270, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_553_270/'.$namewithnewext, 100);		

$resizeObj = new resize($filePath.'original/'.$namewithnewext);
                        $resizeObj -> resizeImage(560, 360, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_560_360/'.$namewithnewext, 100);	

$resizeObj = new resize($filePath.'original/'.$namewithnewext);
                        $resizeObj -> resizeImage(750, 360, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_750_360/'.$namewithnewext, 100);

$resizeObj = new resize($filePath.'original/'.$namewithnewext);
                        $resizeObj -> resizeImage(93, 73, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_93_73/'.$namewithnewext, 100);
                if(array_key_exists($extension, \Config::get('constants.CONVERT_VIDEO'))){
                    $newName = $timestamp.'.mp4';
                    $cmd = "ffmpeg -i $filedest$name -strict -2 -movflags faststart $filedest$newName 2>&1";
                    if(shell_exec($cmd)){
                        $news->file_path        = $newName;
                        $data['file_path']      = $newName;

                    }
                }
                $news->is_image = 0;
                $news->is_video = 1;
            }
        }
        if($request->hasFile('attachment_file')) {
            $attachfile = \Input::file('attachment_file');
            $timestamp = rand().time().rand();
            $extension  = strtolower($attachfile->getClientOriginalExtension());
            $name = $timestamp.'.'.$extension;
            $news->attachment_file = $name;
            $data['attachment_file'] = $name;
            $attachfile->move($filePath.'original/', $name);

        }
        if(isset($data['id']) && (int)$data['id'] == 0){
            $news->user_id = \Session::get('user_id');
        }
        $news->is_draft     = 1;
        if(isset($news->is_approved) && (int)$news->is_approved == 0){
            if(isset($data['is_approved']) && $data['is_approved'] == 1){
                $news->approved_by   = \Session::get('user_id');
                $news->approved_date = date('Y-m-d H:i:s');

                $news->is_publish    = 1;
                $news->publish_date = date('Y-m-d H:i:s');
            }
        }
        if(isset($data['is_archive']) && $data['is_archive'] == 1){
            $news->archive_date = date('Y-m-d H:i:s');
        }
        if($news->fill($data)->save()){
            \Session()->flash('flash_message', 'News saved successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('news/list');
    }
    public function newslist(Request $request){
        $news = new News();
        $records = $request->all();
        $condition = array();
        $autoArr = array();
        $common = new Common();
        $start_date = date('Y-m-d H:i:s', strtotime('-12 months'));
        $end_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day'));
        $firstDate = '';
        $endDate = '';
        $news->news_id = 0;
        $news->news_name = '';
        if(\Session::get('user_type_id') == \Config::get('constants.ADMIN_USERTYPE') || \Session::get('user_type_id') == \Config::get('constants.SUPERADMIN_USERTYPE')){
            $condition += array(
                'news.is_trash'   => 0,
            );
        }else{
            $condition += array(
                'news.user_id'      => \Session::get('user_id'),
            );
        }
        if(is_array($records) && count($records)>0){
            if(isset($records['flag_column']) && $records['flag_column'] != '' && isset($records['flag_value']) && $records['flag_value'] != ''){
                if($records['flag_column'] == 'is_approved'){
                    $condition['news.is_publish']  = 1;
                    $condition['news.is_approved'] = $records['flag_value'];
                }else if($records['flag_column'] == 'is_publish'){
                    $condition['news.is_publish'] = $records['flag_value'];
                }else{
                    $condition += array(
                        'news.'.$records['flag_column']   => $records['flag_value']
                    );
                }
                $news->flag_column  = $records['flag_column'];
                $news->flag_value   = $records['flag_value'];
            }

            if(isset($records['cat_id']) && (int)$records['cat_id'] != 0){
                $condition += array(
                    'news.cat_id'   => $records['cat_id']
                );
                $news->cat_id = $records['cat_id'];
            }
            if(isset($records['news_id']) && (int)$records['news_id'] != 0 && isset($records['news_name']) && $records['news_name'] != ''){
                $condition += array(
                    'news.id'   => $records['news_id']
                );
                $news->news_id = $records['news_id'];
                $news->news_name = $records['news_name'];
                $autoArr = array(
                    'id'    => $records['news_id'],
                    'value' => $records['news_name']
                );
            }else if(isset($records['search_text']) && $records['search_text'] != ''){
                $condition += array(
                    'news.name'   => $records['search_text']
                );
                $news->search_text = $records['search_text'];
            }
            if(isset($records['start_date']) && $records['start_date'] != ''){
                $start_date = date('Y-m-d H:i:s',strtotime($records['start_date']));
                $firstDate = $records['start_date'];
            }
            if(isset($records['end_date']) && $records['end_date'] != ''){
                $end_date = date('Y-m-d H:i:s', strtotime($records['end_date'] . ' +1 day'));
                $endDate = $records['end_date'];
            }
        }else{
            if(\Session::get('user_type_id') == \Config::get('constants.ADMIN_USERTYPE') || \Session::get('user_type_id') == \Config::get('constants.SUPERADMIN_USERTYPE')){
                $condition += array(
                    'news.is_publish'   => 1,
                    'news.is_approved'   => 0
                );
            }else{
                $condition += array(
                    'news.is_publish'   => 0
                );
            }
        }

        //echo '<pre>';print_r($condition);exit;
        $datas = DB::table('news')
        ->select('news.*','news_categories.name as categoryname','users.name as username')
        ->leftJoin('news_categories','news_categories.id','=','news.cat_id')
        ->leftJoin('users','users.id','=','news.user_id')
        ->where($condition)
        ->where("news.created_at",'>=',$start_date)
        ->where("news.created_at",'<=',$end_date)
        //->whereBetween('approved_date', array($start_date, $end_date))
        ->OrderBy('news.id','desc')
        ->paginate(10);
        $newsCatList = [''=>'--All--'] + NewsCategory::where('parent_id',0)->OrderBy('name','asc')->lists('name', 'id')->toArray();

        if(\Session::get('user_type_id') == \Config::get('constants.ADMIN_USERTYPE') || \Session::get('user_type_id') == \Config::get('constants.SUPERADMIN_USERTYPE')){
            $statusArr = array('is_approved'=>'Approved','is_publish'=>'Published','is_hot'=>'Top News','is_featured'=>'Featured','is_top_story'=>'Top Story','is_enable'=>'Enable','is_archive'=>'Archive');
        }else{
            $statusArr = array('is_publish'=>'Published','is_approved'=>'Approved','is_hot'=>'Top News','is_featured'=>'Featured','is_top_story'=>'Top Story','is_enable'=>'Enable','is_archive'=>'Archive');
        }
        $user = DB::table('users')->where('is_trash',0)->get();
        return view('news.newslist', [
            'news'          => $news,
            'user'          => $user,
            'datas'         => $datas,
            'title'         => 'News List',
            'user_type_id'  => \Session::get('user_type_id'),
            'newsCatList'   => $newsCatList,
            'statusArr'     => $statusArr,
            'autoArr'       => json_encode($autoArr),
            'firstDate'     => $firstDate,
            'endDate'       => $endDate
        ]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($baseid){
        $id = base64_decode($baseid);
        if($id){
            $datas = News::find($id);
            return view('news.show', ['title'=>'View Details', 'datas' => $datas]);
        }else{
            return redirect('news/list');
        }
    }
    /**
     * [edit description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function edit($id){
        $news = News::findOrFail($id);
        $newsCatList = [''=>'-Select News Category-'] + NewsCategory::where(['parent_id'=>0,'is_active'=>1])->OrderBy('name','asc')->lists('name', 'id')->toArray();
        $subCatList = array(''=>'--Select--');
        if(isset($news->cat_id) && (int)$news->cat_id != 0){
            $subCatList += NewsCategory::where('parent_id',$news->cat_id)->OrderBy('name','asc')->lists('name', 'id')->toArray();
        }
        return view('news.create', ['news' => $news,'title'=>'Add News', 'newsCatList' => $newsCatList,'subCatList'=>$subCatList,'user_type_id'=>\Session::get('user_type_id')]);
    }
    public function publish($id){
        $news = News::find($id);
        $news->is_publish = 1;
        $news->publish_date = date('Y-m-d H:i:s');
        if($news->save()){
            \Session()->flash('flash_message', 'News published successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('news/list');
    }
    public function trash($id){
        $news = News::find($id);
        $news->is_trash = 1;
        if($news->save()){
            \Session()->flash('flash_message', 'News deleted successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('news/list');
    }
    /**
     * [action description]
     * @return [type] [description]
     */
    public function action($id){
        $news = News::find($id);
        if(isset($news->is_enable) && $news->is_enable == 1){
            $news->is_enable = 0;
        }else{
            $news->is_enable = 1;
        }
        $news->save();
        return redirect('news/list');
    }
    /**
     * [approve description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function approve($id){
        $news = News::find($id);
        $news->is_approved      = 1;
        $news->approved_by      = \Session::get('user_id');
        $news->approved_date    = date('Y-m-d H:i:s');
        $news->save();
        return redirect('news/list');
    }
    public function addimages($baseid){
        $id = base64_decode($baseid);
        if($id){
            $newsImage = new NewsImage();
            $newsImage->news_id = $id;
            $datas = DB::table('news_images')->where(['news_id'=>$id,'is_trash'=>0])->OrderBy('id', 'asc')->paginate(15);
            return view('news.addimages',['newsImage'=>$newsImage,'datas'=>$datas,'title'=>'Add Images']);
        }else{
            return redirect('news/list');
        }
    }
    public function storeimages(CreateNewsImageFormRequest $request){
        $data = $request->all();
        $savedata = array();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $newsImage = NewsImage::find($data['id']);
        }else{
            $newsImage = new NewsImage();
        }
        $news_id = $data['news_id'];

        $resizeImage = \Config::get('constants.RESIZE_IMAGE');
        $filePath = public_path().'/file/news/';
        if(isset($data['hidden_files']) && $data['hidden_files'] != ''){
            $hiddenFileArr = explode(',',$data['hidden_files']);
            if(is_array($hiddenFileArr) && count($hiddenFileArr)>0) {
                $cnt = 0;
                foreach($hiddenFileArr as $hiddenKey=>$hiddenVal){
                    if(file_exists($filePath.'original/'.$hiddenVal)){
                        if(is_array($resizeImage) && count($resizeImage)>0){
                            foreach($resizeImage as $resizeKey=>$resizeVal){
                                $resizeValArr = explode(',',$resizeVal);
                                \File::copy($filePath.'original/'.$hiddenVal, $filePath.$resizeKey.'/'.$hiddenVal);
                                \Image::make($filePath.$resizeKey.'/'.$hiddenVal)->resize($resizeValArr[0],$resizeValArr[1])->save($filePath.$resizeKey.'/'.$hiddenVal);
                            }
                        }
                        $savedata[$cnt]['news_id']      = $data['news_id'];
                        $savedata[$cnt]['name']         = $data['name'];
                        $savedata[$cnt]['image_link']   = $hiddenVal;
                        $savedata[$cnt]['updated_at']   = date('Y-m-d H:i:s');
                        $savedata[$cnt]['created_at']   = date('Y-m-d H:i:s');
                        $cnt++;
                    }
                }
            }
            if(is_array($savedata) && count($savedata)>0){
                if(DB::table('news_images')->insert($savedata)){
                    \Session()->flash('flash_message', 'Image was successful added!');
                }else{
                    \Session()->flash('error_message', 'Invalid request, please try again!');
                }
            }else{
                \Session()->flash('error_message', 'Invalid request, please try again!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('news/addimages/'.base64_encode($news_id));
    }
    public function imageAction($baseid){
        $baseidArr = explode('****',base64_decode($baseid));
        $news_id = $baseidArr[0];
        $id = $baseidArr[1];
        $newsImage = NewsImage::find($id);
        if(isset($newsImage->is_enable) && $newsImage->is_enable == 1){
            $newsImage->is_enable = 0;
        }else{
            $newsImage->is_enable = 1;
        }
        if($newsImage->save()){
            if($newsImage->is_enable == 0){
                \Session()->flash('flash_message', 'Image was disable successful!');
            }else{
                \Session()->flash('flash_message', 'Image was enable successful!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('news/addimages/'.base64_encode($news_id));
    }
    public function imageDelete($baseid){
        $baseidArr = explode('****',base64_decode($baseid));
        $news_id = $baseidArr[0];
        $id = $baseidArr[1];
        $newsImage = NewsImage::find($id);
        $newsImage->is_trash = 1;
        if($newsImage->save()){
            \Session()->flash('flash_message', 'Image was delete successful!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('news/addimages/'.base64_encode($news_id));
    }
    public function fileupload(Request $request){
        if($request->hasFile('file')){
            $data = $request->all();
            $file = $data['file'];
            $extension  = strtolower($file->getClientOriginalExtension());
            $filePath = public_path().'/file/news/original/';
            if(array_key_exists($extension, \Config::get('constants.IMAGE_EXTENSION'))){
                $timestamp  = rand().time().rand();
                $name = $timestamp.'-'.$file->getClientOriginalName();
                if($file->move($filePath, $name)){
                    echo $name;
                }else{
                    echo 'FAIL';
                }
            }else{
                echo 'FAIL';
            }
        }
    }
    public function removefile(Request $request){
        $data = $request->all();
        $fileList = $data['fileList'];
        $filePath = public_path().'/file/news/original/';
        if(isset($fileList)){
            unlink($filePath.$fileList);
        }
    }
    public function addVideos($baseid){
        $id = base64_decode($baseid);
        if($id){
            $newsVideo = new NewsVideo();
            $newsVideo->news_id = $id;
            $datas = DB::table('news_videos')->where(['news_id'=>$id,'is_trash'=>0])->OrderBy('position', 'asc')->paginate(15);
            return view('news.addVideos',['newsVideo'=>$newsVideo,'datas'=>$datas,'title'=>'Add Videos']);
        }else{
            return redirect('news/list');
        }
    }
    public function videoAction($baseid){
        $baseidArr = explode('****',base64_decode($baseid));
        $news_id = $baseidArr[0];
        $id = $baseidArr[1];
        $newsVideo = NewsVideo::find($id);
        if(isset($newsVideo->is_enable) && $newsVideo->is_enable == 1){
            $newsVideo->is_enable = 0;
        }else{
            $newsVideo->is_enable = 1;
        }
        if($newsVideo->save()){
            if($newsVideo->is_enable == 0){
                \Session()->flash('flash_message', 'Video was disable successful!');
            }else{
                \Session()->flash('flash_message', 'Video was enable successful!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('news/addvideos/'.base64_encode($news_id));
    }
    public function videoDelete($baseid){
        $baseidArr = explode('****',base64_decode($baseid));
        $news_id = $baseidArr[0];
        $id = $baseidArr[1];
        $newsVideo = NewsVideo::find($id);
        $newsVideo->is_trash = 1;
        if($newsVideo->save()){
            \Session()->flash('flash_message', 'Video was delete successful!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('news/addvideos/'.base64_encode($news_id));
    }
    public function videoupload(Request $request){
        if($request->hasFile('file')){
            $data = $request->all();
            $file = $data['file'];
            $extension  = strtolower($file->getClientOriginalExtension());
            $filePath = public_path().'/file/news/original/';
            if(array_key_exists(strtolower($extension), \Config::get('constants.VIDEO_EXTENSION')) || array_key_exists(strtolower($extension), \Config::get('constants.AUDIO_EXTENSION'))){
                $timestamp  = rand().time().rand();
                $name = $timestamp.'.'.strtolower($extension);
                if($file->move($filePath, $name)){
                    if(array_key_exists($extension, \Config::get('constants.CONVERT_VIDEO'))){
                        $newName = $timestamp.'.mp4';
                        $cmd = "ffmpeg -i $filePath$name -strict -2 -movflags faststart $filePath$newName 2>&1";
                        if(shell_exec($cmd)){
                            echo $newName;
                        }else{
                            echo 'FAIL';
                        }
                    }else{
                        echo $name;
                    }
                }else{
                    echo 'FAIL';
                }
            }else{
                echo 'FAIL';
            }
        }
    }
    public function removevideo(Request $request){
        $data = $request->all();
        $fileList = $data['fileList'];
        $filePath = public_path().'/file/news/original/';
        if(isset($fileList)){
            unlink($filePath.$fileList);
        }
    }
    public function storevideos(CreateNewsVideoFormRequest $request){
        $data = $request->all();
        $savedata = array();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $newsVideo = NewsVideo::find($data['id']);
        }else{
            $newsVideo = new NewsVideo();
        }
        $news_id = $data['news_id'];

        $ffmpeg = "/usr/bin/ffmpeg";
        $videoFile = $data['hidden_files'];
        $imageFile = "1.jpg";
        $size = "1280x720";
        $getFromSecond = 2;


        $filePath = public_path().'/file/news/original/';
        if(isset($data['hidden_files']) && $data['hidden_files'] != ''){
            $hiddenFileArr = explode(',',$data['hidden_files']);
            if(is_array($hiddenFileArr) && count($hiddenFileArr)>0) {
                $cnt = 0;
                foreach($hiddenFileArr as $hiddenKey=>$hiddenVal){
                    if(file_exists($filePath.$hiddenVal)){
                        $savedata[$cnt]['news_id']      = $data['news_id'];
                        $savedata[$cnt]['name']         = $data['name'];
                        $savedata[$cnt]['file_link']    = $hiddenVal;
                        $ext = substr(strtolower(strrchr($hiddenVal, '.')), 1);
                        if(array_key_exists($ext, \Config::get('constants.VIDEO_EXTENSION'))){
                            $namewithoutext = pathinfo($hiddenVal, PATHINFO_FILENAME);
                            $namewithnewext = $namewithoutext.'.jpg';

                            $cmd = "ffmpeg -i $filePath$hiddenVal -an -ss $getFromSecond -s $size $filePath$namewithnewext";
                            shell_exec($cmd);
							/////////////////////
							$resizeObj = new resize($filePath.$namewithnewext);
                        $resizeObj -> resizeImage(285, 170, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_285_170/'.$namewithnewext, 100);

$resizeObj = new resize($filePath.$namewithnewext);
                        $resizeObj -> resizeImage(350, 224, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_350_224/'.$namewithnewext, 100);	

$resizeObj = new resize($filePath.$namewithnewext);
                        $resizeObj -> resizeImage(360, 180, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_360_180/'.$namewithnewext, 100);	

$resizeObj = new resize($filePath.$namewithnewext);
                        $resizeObj -> resizeImage(375, 200, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_375_200/'.$namewithnewext, 100);	

$resizeObj = new resize($filePath.$namewithnewext);
                        $resizeObj -> resizeImage(553, 270, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_553_270/'.$namewithnewext, 100);		

$resizeObj = new resize($filePath.$namewithnewext);
                        $resizeObj -> resizeImage(560, 360, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_560_360/'.$namewithnewext, 100);	

$resizeObj = new resize($filePath.$namewithnewext);
                        $resizeObj -> resizeImage(750, 360, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_750_360/'.$namewithnewext, 100);

$resizeObj = new resize($filePath.$namewithnewext);
                        $resizeObj -> resizeImage(93, 73, 'exact');
                        $resizeObj -> saveImage(public_path().'/file/news/exact_93_73/'.$namewithnewext, 100);						
							//////////////////////
                            $savedata[$cnt]['is_video'] = 1;
                            $savedata[$cnt]['is_audio'] = 0;
                        }else if(array_key_exists($ext, \Config::get('constants.AUDIO_EXTENSION'))){
                            $savedata[$cnt]['is_video'] = 0;
                            $savedata[$cnt]['is_audio'] = 1;
                        }
                        $savedata[$cnt]['updated_at'] = date('Y-m-d H:i:s');
                        $savedata[$cnt]['created_at'] = date('Y-m-d H:i:s');
                        $cnt++;
                    }
                }
            }
            if(is_array($savedata) && count($savedata)>0){
                if(DB::table('news_videos')->insert($savedata)){
                    \Session()->flash('flash_message', 'Video was successful added!');
                }else{
                    \Session()->flash('error_message', 'Invalid request, please try again!');
                }
            }else{
                \Session()->flash('error_message', 'Invalid request, please try again!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('news/addvideos/'.base64_encode($news_id));
    }
    public function saveaction(Request $request){
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $news = News::find($data['id']);
            $news_approved = $news->is_approved;
            if(isset($data['is_approved']) && (int)$data['is_approved'] == 1){
                if(isset($news->is_approved) && (int)$news->is_approved == 0){
                    $data['approved_by']   = \Session::get('user_id');
                    $data['approved_date'] = date('Y-m-d H:i:s');
                }else if(isset($data['re_approved']) && (int)$data['re_approved'] == 1){
                    $data['approved_by']   = \Session::get('user_id');
                    $data['approved_date'] = date('Y-m-d H:i:s');
                }
            }
            if($news->fill($data)->save()){
                if((int)$data['is_approved'] == 1 && (int)$data['is_enable'] == 1 && (int)$data['is_archive'] == 0 && (int)$data['is_hot'] == 1 && (int)$data['is_featured'] == 1 && (int)$data['position'] != 0 && (int)$data['position'] <= 5){
                    $topNews = TopNews::where('news_id',$data['id'])->first();
                    if(isset($topNews->news_id) && (int)$topNews->news_id != 0 && $topNews->position != $data['position']){
                        $topNews->position = $data['position'];
                        $topNews->save();
                        DB::update(DB::raw("UPDATE top_news SET position=position+1 where position>=".$data['position']." AND news_id !=".$data['id']));
                    }else if(!isset($topNews->news_id)){
                        $topNews = new TopNews();
                        $topNews->news_id = $data['id'];
                        $topNews->position = $data['position'];
                        $topNews->save();
                        DB::update(DB::raw("UPDATE top_news SET position=position+1 where position>=".$data['position']." AND news_id !=".$data['id']));
                    }
                }
                //if($news_approved == 0 && isset($data['is_approved']) && (int)$data['is_approved'] == 1){
                    echo $news->name.'*****#####*****'.$news->slug;
               /* }else{
                    echo 'SUCC';
                }*/
            }else{
                echo 'FAIL';
            }
        }else{
            echo 'FAIL';
        }
    }
    public function getNewsSubCategory(Request $request){
        $data = $request->all();
        if(isset($data['cat_id']) && (int)$data['cat_id'] != 0){
            $subCatList = NewsCategory::where('parent_id',$data['cat_id'])->OrderBy('name','asc')->lists('name', 'id')->toArray();
            if(is_array($subCatList) && count($subCatList)>0){
                echo "<option value=''>--Select--</option>";
                foreach($subCatList as $subKey=>$subVal){
                    echo "<option value='".$subKey."'>".$subVal."</option>";
                }
            }else{
                echo '<option value="">--Select--</option>';
            }
        }else{
            echo '<option value="">--Select--</option>';
        }
    }
    public function newsDetails($slug){
        if($slug != ''){
            $sessionvalue = '';
            $newsComment = new NewsComment();
            /*
             *Query for get the news details
             */
            $news = News::whereSlug(trim($slug))->first();

            if(\Session::get('keycnt'.$news->id) != ''){
                $interval  = abs(strtotime(date('Y-m-d H:i:s')) - strtotime(\Session::get('keycnt'.$news->id)));
                $minutes   = round($interval / 60);
                if($minutes >= 3){
                    \Session::forget('keycnt'.$news->id);
                }
            }
            if(\Session::get('keycnt'.$news->id) == ''){
                if(isset($news->news_count) && (int)$news->news_count != 0){
                    $news->news_count = $news->news_count + 1;
                }else{
                    $news->news_count = 1;
                }
                $news->save(); 
                \Session::put('keycnt'.$news->id, date('Y-m-d H:i:s'));
            }
            /*
             *Query for get the related news
             */
            $releatedNews = NEWS::select('id','name','slug','tags','short_description','featured_image','is_video','is_image','file_path')
            ->where(['is_archive'=>0,'is_approved'=>1,'is_enable'=>1,'is_trash'=>0,'is_publish'=>1])
            ->where('tags', 'like', '%'.$news->tags.'%')
            ->where('id','!=',$news->id)
            ->OrderBy('position','ASC')->take(10)->get();
            /*
             *Query for get the comment of each news
             */
            $newsComments = NewsComment::select('id','name','comment','user_id','verified_date','file_path','is_image','is_video','is_audio')
            ->where(['is_enable'=>1,'is_verified'=>1,'is_trash'=>0,'news_id'=>$news->id])
            ->OrderBy('id','desc')->get();

            $metaDesc       = $news->meta_desc;
            $metaKeywords   = $news->meta_keywords;

            $news_desc     = $news->long_description;
            $news_location  = $news->news_location;
            $name           = $news->name;

            $socialImage = '';
            $socialVideo = '';
            if($news->featured_image != ''){
                $socialImage = $news->featured_image;
            }else if($news->file_path != '' && (int)$news->is_image == 1){
                $socialImage = $news->file_path;
            }else if($news->file_path != '' && (int)$news->is_video == 1){
                $namewithoutext = pathinfo($news->file_path, PATHINFO_FILENAME);
                $namewithnewext = $namewithoutext.'.jpg';
                $socialImage    = $namewithnewext;
                $socialVideo    = $news->file_path;
            }

            return view('news.newsDetails', [
                'newsComment'       => $newsComment,
                'news'              => $news,
                'releatedNews'      => $releatedNews,
                'newsComments'      => $newsComments,
                'metaDesc'          => $metaDesc,
                'metaKeywords'      => $metaKeywords,
                'news_desc'         => $news_desc,
                'news_location'     => $news_location,
                'name'              => $name,
                'socialImage'       => $socialImage,
                'socialVideo'       => $socialVideo,
            ]);
        }else{
            return redirect()->route('/');
        }
    }
    /**
     * [category description]
     * @return [type] [description]
     */
    public function category($slug){
        /*
         *Query for get the news category details
         */
        $newsCategory   = NewsCategory::select('id','name','meta_desc','meta_keywords','description')->where('slug',$slug)->first();
        $metaDesc       = $newsCategory->meta_desc;
        $metaKeywords   = $newsCategory->meta_keywords;
        $title          = $newsCategory->description;
        /*
         *Query for get the top news details
         */
        $topNews = TopNews::select('news.id','news.name','news.slug','news.short_description as shortdescription','news.featured_image','news.file_path','news.is_video','news.is_image','news.approved_date','users.name as username','news.journalist_name')
        ->leftJoin('news', 'top_news.news_id', '=', 'news.id')
        ->leftJoin('users', 'news.user_id', '=', 'users.id')
        ->leftJoin('news_categories','news.cat_id', '=', 'news_categories.id')
        ->where('news_categories.id',$newsCategory->id)
        ->where('top_news.position','<=', 5)
        ->OrderBy('top_news.position','ASC')->first();
        if(!isset($topNews->id)){        
            $topNews = News::select('news.id','news.name','slug','short_description as shortdescription','featured_image','file_path','is_video','is_image','approved_date','users.name as username','journalist_name')
            ->leftJoin('users', 'news.user_id', '=', 'users.id')
            ->where(['news.cat_id'=>$newsCategory->id,'news.is_enable'=>1,'news.is_approved'=>1,'news.is_hot'=>1,'news.is_featured'=>0,'news.is_trash'=>0,'news.is_publish'=>1])
            ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
            ->OrderBy('position','ASC')
            ->OrderBy('approved_date','DESC')
            ->first();
        }
        if(isset($topNews->id) && (int)$topNews->id != 0){
            $topNewsId = $topNews->id;
        }else{
            $topNewsId = 0;
        }
        /*
         *Query for get the category wise news details
         */
        $datas = DB::table('news')
        ->select('news.id','news.name','slug','short_description as shortdescription','featured_image','file_path','is_video','is_image','approved_date','users.name as username','journalist_name')
        ->leftJoin('users', 'news.user_id', '=', 'users.id')
        ->where(['news.cat_id'=>$newsCategory->id,'news.is_enable'=>1,'news.is_approved'=>1,'news.is_trash'=>0,'news.is_publish'=>1])
        ->where('news.id','!=',$topNewsId)
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
        ->OrderBy('position','ASC')
        ->OrderBy('approved_date','DESC')
        ->paginate(16);

        $catAds = DB::table('advertisements')
        ->select('id','file_path','name','file_type')
        ->where(['cat_id'=>$newsCategory->id,'is_trash'=>0,'is_publish'=>1])->where('end_date', '>=', date('Y-m-d'))->get();
        $advt = array();
        if($catAds){
            $catCnt = 2;
            foreach($catAds as $catAd){
                $advt[$catCnt]['id']           = $catAd->id;
                $advt[$catCnt]['file_path']    = $catAd->file_path;
                $advt[$catCnt]['name']         = $catAd->name;
                $advt[$catCnt]['file_type']    = $catAd->file_type;
                $catCnt = $catCnt + 2;
            }
        }
        return view('news.category', [
            'title'         => 'Category Details',
            'datas'         => $datas,
            'newsCategory'  => $newsCategory,
            'topNews'       => $topNews,
            'advt'          => $advt,
            'metaDesc'      => $metaDesc,
            'metaKeywords'  => $metaKeywords,
            'title'         => $title,
        ]);
    }
    /**
     * [topNewsNow description]
     * @return [type] [description]
     */
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
        $datas = TopNews::select('news.id','news.name','news.slug','news.short_description as shortdescription','news.featured_image','news.file_path','news.is_video','news.is_image','news.approved_date','users.name as username','news.journalist_name','news.video_title')
        ->leftJoin('news','news.id','=','top_news.news_id')
        ->leftJoin('users', 'news.user_id', '=', 'users.id')
        ->where(['news.is_approved'=>1,'news.is_hot'=>1,'news.is_featured'=>1,'news.is_archive'=>0,'news.is_enable'=>1,'news.is_trash'=>0])
        ->OrderBy('top_news.position','ASC')->take(5)->get();

        $metaDesc       = "OMMCOM Latest News: Get the latest and breaking news from Odisha. Find all current news, breaking events from Odisha, news headlines from politics and sports.";
        $metaKeywords   = "Odisha news, Odisha live news, Odisha news, Odisha news bulletin, Latest odisha news, Odisha news portal, odia news, ommcom news, odisha top news site, top odisha news website, orissa news, best orissa news, best odisha news, online odisha news, online orissa news, online news odisha, news in india, breaking news, today news, current news, indian news, news website, india news, world news, business news, bollywood news, cricket news, sports, lifestyle, gadgets, tech news, video news, online tv news, news on videos, latest news on videos, economy news, political news, celebrity news, international news, current affairs, top news, weekly news, local news, news headlines, news website, stock market, mutual funds, Hindi movies, India online shopping, ollywood news, bhubaneswar news, cuttack news, puri news, barahampur news, sambalpur news, Rourkela news, automobile news, property news, vacancy news, education news, odisha live tv, oriya live tv news, odia news videos, cinema news, best news website odisha, odisha no1 news site, odisha’s popular news site, odisha news headlines, odisha breaking news, 24*7 odisha news, 24x7 odisha news, popular oriya news site";
        $title          = "OMMCOM News : Latest Odisha News, Odisha Breaking News, Top Odisha News";

        return view('news.topNewsNow', [
            'datas'         => $datas,
            'metaDesc'      => $metaDesc,
            'metaKeywords'  => $metaKeywords,
            'title'         => $title,
        ]);
        /*
         *Query for get the top news video
         */
        //$topNews = NewsTopVideo::select('name','video_file')->where('is_enable',1)->OrderBy('updated_at','DESC')->first();
        //return view('news.topNewsNow', ['title'=>'Top News Details','datas'=>$datas,'topNews'=>$topNews]);
    }
    public function nextTopNews(){
        $datas = DB::table('top_news')
        ->select('news.id','news.name','news.slug','news.short_description as shortdescription','news.featured_image','news.file_path','news.is_video','news.is_image','news.approved_date','users.name as username','news.journalist_name','news.video_title')
        ->leftJoin('news','news.id','=','top_news.news_id')
        ->leftJoin('users', 'news.user_id', '=', 'users.id')
        ->where(['news.is_approved'=>1,'news.is_hot'=>1,'news.is_featured'=>1,'news.is_archive'=>0,'news.is_enable'=>1,'news.is_trash'=>0])
        ->OrderBy('top_news.position','ASC')
        ->paginate(5);
        //return $datas;
        return view('news.nextTopNews', [
            'datas'         => $datas,
        ]);
    }
    public function commentStore(CreateNewsCommentFormRequest $request){
        $data = $request->all();
        $newsComments = new NewsComment();
        $newsComments->name             = $data['name'];
        $newsComments->email            = $data['email'];
        $newsComments->contact_no       = $data['contact_no'];
        $newsComments->comment          = $data['comment'];
        $newsComments->news_id          = $data['news_id'];
        $newsComments->is_verified      = 1;
        $newsComments->is_enable        = 1;
        if(isset($data['is_anonymous'])){
            $newsComments->is_anonymous = $data['is_anonymous'];
        }else{
            $newsComments->is_anonymous = 0;
        }

        $resizeImage = \Config::get('constants.RESIZE_IMAGE');
        $filePath = public_path().'/file/news/';
        if(isset($data['hidden_files']) && $data['hidden_files'] != ''){
            $extension = substr(strtolower(strrchr($data['hidden_files'], '.')), 1);
            if(file_exists($filePath.'original/'.$data['hidden_files'])){
                $newsComments->file_path = $data['hidden_files'];
                if(array_key_exists($extension, \Config::get('constants.IMAGE_EXTENSION'))){
                    if(is_array($resizeImage) && count($resizeImage)>0){
                        foreach($resizeImage as $resizeKey=>$resizeVal){
                            $resizeValArr = explode(',',$resizeVal);
                            \File::copy($filePath.'original/'.$data['hidden_files'], $filePath.$resizeKey.'/'.$data['hidden_files']);
                            \Image::make($filePath.$resizeKey.'/'.$data['hidden_files'])->resize($resizeValArr[0],$resizeValArr[1])->save($filePath.$resizeKey.'/'.$data['hidden_files']);
                        }
                    }
                    $newsComments->is_image = 1;
                }else if(array_key_exists($extension, \Config::get('constants.VIDEO_EXTENSION'))){
                    $ffmpeg = "/usr/bin/ffmpeg";
                    $imageFile = "1.jpg";
                    $size = "1280x720";
                    $getFromSecond = 2;
                    $namewithoutext = pathinfo($data['hidden_files'], PATHINFO_FILENAME);
                    $namewithnewext = $namewithoutext.'.jpg';
                    $videoFile = $filePath.'original/'.$data['hidden_files'];
                    $destFile = $filePath.'original/'.$namewithnewext;
                    $cmd = "ffmpeg -i $videoFile -an -ss $getFromSecond -s $size $destFile";
                    shell_exec($cmd);
                    $newsComments->is_video = 1;
                }else if(array_key_exists($extension, \Config::get('constants.AUDIO_EXTENSION'))){
                    $newsComments->is_audio = 1;
                }
            }
        }
        if($newsComments->save()){
            \Session()->flash('flash_message', 'Your comment has been saved successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, Please try again!');
        }
        return redirect('/'.$data['slug']);
    }
    /**
     * [newscommentlist description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    /*
    public function newscommentlist(Request $request)
    {
        $newscomment = new NewsComment();
        $records = $request->all();
        $condition = array();
        $autoArr = array();
        if(is_array($records) && count($records)>0){
            if(isset($records['news_id']) && (int)$records['news_id'] != 0 && isset($records['news_name']) && $records['news_name'] != ''){
                $condition += array(
                    'news.id'   => $records['news_id']
                );
                $autoArr = array(
                    'id'    => $records['news_id'],
                    'value' => $records['news_name']
                );
            }
        }
        $db = DB::table('news_comments')
        ->select('news_comments.id','news_comments.name as cname','comment','news_comments.news_id', 'news.name as newsname', 'news_comments.is_verified', 'news_comments.is_enable', 'news_comments.verified_date')
        ->leftJoin('news', 'news.id', '=', 'news_comments.news_id')
        ->where($condition)
        ->OrderBy('news_comments.id','ASC')
        ->paginate(15);
        //var_dump($db); exit();

        return view('news.newscommentlist', ['newscomment'=>$newscomment, 'db'=>$db, 'title'=>'News Comment List','autoArr'=>json_encode($autoArr) ]);
    }*/
    /**
     * [newscommentverify description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    /*
    public function newscommentverify(Request $request){
        $data = $request->all();
        $is_verify = $data['bulkverify'];
        if (array_key_exists('bulkverify', $data)) {

            for($i=0; $i < count($is_verify); $i++) {
                NewsComment::where('id', '=', $is_verify[$i])
                ->update(['is_verified' => 1, 'verified_date' => date('Y-m-d H:i:s')]);
            }
        }
        return redirect('news/commentlist');
    }*/
    /**
     * [newscommentaction description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    /*
    public function newscommentaction($id){
        $newscomment = NewsComment::find($id);
        if(isset($newscomment->is_enable) && $newscomment->is_enable == 1){
            $newscomment['is_enable'] = 0;
            $newscomment['verified_date'] = date('Y-m-d H:i:s');
        }else{
            $newscomment['is_enable'] = 1;
        }
        $newscomment->save();
        return redirect('news/commentlist');
    } */

    public function autocomplete(){
        if($_GET['q'] != ''){
            $query = $_GET['q'];
            $responseArr = NEWS::select('id','name as value')
            ->where(['is_archive'=>0,'is_approved'=>1,'is_enable'=>1])
            ->where('name', 'like', '%' . $query . '%')
            ->OrderBy('name','ASC')->take(50)->get();
            echo $responseArr;
        }else{
            $responseArr = array();
            echo json_encode($responseArr);
        }
    }
    public function updateimagetitle(Request $request){
        $data = $request->all();
        if($data['pk']){
            $newsImage = NewsImage::find($data['pk']);
            $newsImage->name = $data['value'];
            //echo $newsImage->name
            if($newsImage->name != ''){
                if(strlen($newsImage->name) <= 250){
                    if($newsImage->save()){
                        echo 'SUCC';
                    }else{
                        echo 'Invalid request, please try again';
                    }
                }else{
                    echo 'Title must not be greater than 250 character';
                }
            }else{
                echo 'Please enter title';
            }
        }else{
            echo 'Invalid request, please try again';
        }
    }
    public function updateimageposition(Request $request){
        $data = $request->all();
        if($data['pk']){
            $newsImage = NewsImage::find($data['pk']);
            $newsImage->position = $data['value'];
            //echo $newsImage->name
            if($newsImage->position != ''){
                if(is_numeric($newsImage->position)){
                    if(strlen($newsImage->position) <= 2){
                        if($newsImage->save()){
                            echo 'SUCC';
                        }else{
                            echo 'Invalid request, please try again';
                        }
                    }else{
                        echo 'Position must not be greater than 2 character';
                    }
                }else{
                    echo 'Position must be numeric';
                }
            }else{
                echo 'Please enter position';
            }
        }else{
            echo 'Invalid request, please try again';
        }
    }    
    public function updatevideotitle(Request $request){
        $data = $request->all();
        if($data['pk']){
            $newsVideo = NewsVideo::find($data['pk']);
            $newsVideo->name = $data['value'];
            //echo $newsImage->name
            if($newsVideo->name != ''){
                if(strlen($newsVideo->name) <= 250){
                    if($newsVideo->save()){
                        echo 'SUCC';
                    }else{
                        echo 'Invalid request, please try again';
                    }
                }else{
                    echo 'Title must not be greater than 250 character';
                }
            }else{
                echo 'Please enter title';
            }
        }else{
            echo 'Invalid request, please try again';
        }
    }
    public function updatevideoposition(Request $request){
        $data = $request->all();
        if($data['pk']){
            $newsVideo = NewsVideo::find($data['pk']);
            $newsVideo->position = $data['value'];
            //echo $newsImage->name
            if($newsVideo->position != ''){
                if(is_numeric($newsVideo->position)){
                    if(strlen($newsVideo->position) <= 2){
                        if($newsVideo->save()){
                            echo 'SUCC';
                        }else{
                            echo 'Invalid request, please try again';
                        }
                    }else{
                        echo 'Position must not be greater than 2 character';
                    }
                }else{
                    echo 'Position must be numeric';
                }
            }else{
                echo 'Please enter position';
            }
        }else{
            echo 'Invalid request, please try again';
        }        
    }
    public function commentFileUpload(Request $request){
        if($request->hasFile('file')){
            $data = $request->all();
            $file = $data['file'];
            $extension  = strtolower($file->getClientOriginalExtension());
            $filePath = public_path().'/file/news/original/';
            $timestamp  = rand().time().rand();
            $name = $timestamp.'.'.$extension;
            $namewithnewext = $timestamp.'.jpg';
            if($file->move($filePath, $name)){
                if(array_key_exists(strtolower($extension), \Config::get('constants.VIDEO_EXTENSION'))){
                    if(array_key_exists($extension, \Config::get('constants.CONVERT_VIDEO'))){
                        $newName = $timestamp.'.mp4';
                        $cmd = "ffmpeg -i $filePath$name -strict -2 -movflags faststart $filePath$newName 2>&1";
                        if(shell_exec($cmd)){
                            echo $newName;
                        }else{
                            echo $name;
                        }
                    }else{
                        echo $name;
                    }
                }else{
                    echo $name;
                }
            }else{
                echo 'FAIL';
            }
        }else{
            echo 'FAIL';
        }
    }
    public function commentRemovefile(Request $request){
        $data = $request->all();
        $fileList = $data['fileList'];
        $filePath = public_path().'/file/news/original/';
        if(isset($fileList)){
            unlink($filePath.$fileList);
        }
    }
    public function newscommentlist(Request $request)
    {
         $data = $request->all();
        $condition = array('news_comments.is_trash' => 0);
        $autoArr = array();
        if(is_array($data) && count($data)>0){
            if(isset($data['news_id']) && (int)$data['news_id'] != 0 && isset($data['news_name']) && $data['news_name'] != ''){
                $condition += array(
                    'news.id'   => $data['news_id']
                );
                $autoArr = array(
                    'id'    => $data['news_id'],
                    'value' => $data['news_name']
                );
            }

            if (array_key_exists('bulkverify', $data)) {

                for($i=0; $i < count($data['bulkverify']); $i++) {
                    NewsComment::where('id', '=', $data['bulkverify'][$i])
                    ->update(['is_verified' => 1, 'is_enable' => 1, 'verified_date' => date('Y-m-d H:i:s')]);
                }
            }
        }else{
            $condition += array(
                'news.is_approved'   => 0
            );
        }
        $db = DB::table('news_comments')
        ->select('news_comments.id','news_comments.name as cname','comment','news_comments.news_id', 'news.name as newsname', 'news_comments.is_verified', 'news_comments.is_enable', 'news_comments.verified_date')
        ->leftJoin('news', 'news.id', '=', 'news_comments.news_id')
        ->where($condition)
        ->OrderBy('news_comments.id','ASC')
        ->paginate(15);

        $chkVer = DB::table('news_comments')->where('is_verified',0)->get();

        return view('news.newscommentlist', ['db'=>$db, 'chkVer'=>$chkVer, 'title'=>'News Comment List','autoArr'=>json_encode($autoArr) ]);
    }

    public function newscommentverify($id){
        $newscomment = NewsComment::find($id);
        if(isset($newscomment->is_verified) && $newscomment->is_verified == 1){
            $newscomment['is_verified'] = 0;
        }else{
            $newscomment['is_verified'] = 1;
            $newscomment['verified_date'] = date('Y-m-d H:i:s');
            $newscomment['is_enable'] = 1;
        }
        $newscomment->save();
        return redirect('news/commentlist');
    }
    /**
     * [newscommentaction description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function newscommentaction($id){
        $newscomment = NewsComment::find($id);
        if(isset($newscomment->is_enable) && $newscomment->is_enable == 1){
            $newscomment['is_enable'] = 0;
            $newscomment['verified_date'] = date('Y-m-d H:i:s');
        }else{
            $newscomment['is_enable'] = 1;
        }
        $newscomment->save();
        return redirect('news/commentlist');
    }
    /**
     * [newscommentdelete description]
     * @return [type] [description]
     */
    public function newscommentdelete($id){
        if($id){
            $newscomment = NewsComment::find($id);
            $newscomment['is_trash'] = 1;
            $newscomment->save();
        }else{

        }
        return redirect('news/commentlist');
    }
    public function breakingnewslist(){
        $datas = DB::table('breaking_news')
        ->where('is_trash', 0)
        ->OrderBy(DB::raw('DATE(updated_at)'),'DESC')
        ->OrderBy('position','ASC')
        ->orderBy('updated_at', 'DESC')
        ->paginate(15);

        return view('news.breakingnewslist', ['datas' => $datas,'title'=>'Breaking News List' ]);
    }
    public function breakingnewsedit($id){
        $breakingNews = BreakingNews::findOrFail($id);
        return view('news.breakingnewscreate', ['breakingNews' => $breakingNews,'title'=>'Update Breaking News' ]);
    }
    public function breakingnewsdelete($id){
        $breakingNews = BreakingNews::find($id);
        if($breakingNews)
        {
            $breakingNews->is_trash = '1';
        }
        if($breakingNews->save()){
            \Session()->flash('flash_message', 'Breaking News deleted successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('breakingnews/list');
    }
    public function breakingnewscreate(){
        $breakingNews = new BreakingNews;
        return view('news.breakingnewscreate', ['breakingNews' => $breakingNews,'title'=>'Add Breaking News' ]);
    }
    public function breakingnewsaction(Request $request){
        $data = $request->all();
        if($data['id']){
            $id = $data['id'];
            $breakingNews = BreakingNews::find($id);
            if(isset($breakingNews->is_enable) && $breakingNews->is_enable == 1){
                $breakingNews['is_enable'] = 0;
            }else{
                $breakingNews['is_enable'] = 1;
            }
            if($breakingNews->save()){
                echo $breakingNews->title;
            }else{
                echo 'FAIL';
            }
        }else{
            echo 'FAIL';
        }
    }
    public function addtobreakingnews($id){
        if($id){
            $breakingNews = BreakingNews::find($id);
            $breakingNews->updated_at = date('Y-m-d H:i:s');
            if($breakingNews->save()){
                \Session()->flash('flash_message', 'News added to breaking news successfully!');
            }else{
                \Session()->flash('error_message', 'Invalid request, please try again!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('breakingnews/list');
    }
    public function breakingnewsChangePosition(Request $request){
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0 && isset($data['position']) && (int)$data['position'] != 0){
            $breakingNews = BreakingNews::find($data['id']);
            $breakingNews->position = $data['position'];
            if($breakingNews->save()){
                echo 'SUCC';
            }else{
                echo 'FAIL';
            }
        }else{
            echo 'FAIL';
        }
    }
    public function breakingnewstore(CreateBreakingNewsFormRequest $request){
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $breakingNews = BreakingNews::find($data['id']);
        }else{
            $breakingNews = new BreakingNews();
        }
        if($breakingNews->fill($data)->save()){
            //BreakingNews::pushNotification($data['title']);
            \Session()->flash('flash_message', 'Breaking News saved successfully!');
        }else{
             \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('breakingnews/list');
    }
    public function addtopnewsvideo(){
        $newsTopVideo = new NewsTopVideo;
        return view('news.addtopnewsvideo', ['newsTopVideo' => $newsTopVideo,'title'=>'Add Top News Video' ]);
    }
    public function topnewsvideostore(CreateTopNewsVideoFormRequest $request){
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $newsTopVideo = NewsTopVideo::find($data['id']);
        }else{
            $newsTopVideo = new NewsTopVideo();
        }
        $newsTopVideo->name = $data['name'];
        if($request->hasFile('video_file')) {
            $file = \Input::file('video_file');
            $filePath = public_path().'/file/topnewsvideo/';
            $ffmpeg = "/usr/bin/ffmpeg";
            $size = "1280x720";
            $getFromSecond = 5;
            //if(in_array($file->getMimeType(),\Config::get('constants.VIDEO_EXTENSION'))){
                $extension  = strtolower($file->getClientOriginalExtension());
                $timestamp = rand().time().rand();
                $name = $timestamp.'.'.$extension;
                $namewithnewext = $timestamp.'.jpg';
                $newsTopVideo->video_file = $name;
                if($file->move($filePath, $name)){
                    $cmd = "ffmpeg -i $filePath$name -an -ss $getFromSecond -s $size $filePath$namewithnewext";
                    shell_exec($cmd);
                    if(array_key_exists($extension, \Config::get('constants.CONVERT_VIDEO'))){
                        $newName = $timestamp.'.mp4';
                        $convertcmd = "ffmpeg -i $filePath$name -strict -2 -movflags faststart $filePath$newName 2>&1";
                        if(shell_exec($convertcmd)){
                            $newsTopVideo->video_file = $newName;
                        }
                    }
                    if($newsTopVideo->save()){
                        \Session()->flash('flash_message', 'Top news video saved successfully!');
                    }else{
                        \Session()->flash('error_message', 'Invalid request, please try again!');
                    }
                }else{
                    \Session()->flash('error_message', 'Invalid request, please try again!');
                }
           /* }else{
                \Session()->flash('error_message', 'Corrupt file!');
            }*/
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('news/managetopnewsvideo');
    }
    public function managetopnewsvideo(){
        $datas = DB::table('news_top_videos')->where('is_trash', 0)->orderBy('updated_at', 'DESC')->paginate(15);
        return view('news.managetopnewsvideo', ['datas' => $datas,'title'=>'Manage Top News Videos' ]);
    }
    public function topnewsvideoaction($id){
        if($id){
            $newsTopVideo = NewsTopVideo::find($id);
            if(isset($newsTopVideo->is_enable) && $newsTopVideo->is_enable == 1){
                $newsTopVideo->is_enable = 0;
            }else{
                $newsTopVideo->is_enable = 1;
            }
            if($newsTopVideo->save()){
                if($newsTopVideo->is_enable == 0){
                    \Session()->flash('flash_message', 'Top news video disabled successfully!');
                }else{
                    DB::table('news_top_videos')->where('id','!=', $id)->whereRaw('date(updated_at) = ?', [date('Y-m-d')])->update(['is_enable' =>0]);
                    \Session()->flash('flash_message', 'Top news video enabled successfully!');
                }
            }else{
                \Session()->flash('error_message', 'Invalid request, please try again!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('news/managetopnewsvideo');
    }
    public function edittopnewsvideo($id){
        $newsTopVideo = NewsTopVideo::findOrFail($id);
        return view('news.addtopnewsvideo', ['newsTopVideo' => $newsTopVideo,'title'=>'Update Top News Videos' ]);
    }
    public function deletetopnewsvideo($id){
        if($id){
            $newsTopVideo = NewsTopVideo::find($id);
            $newsTopVideo->is_trash = 1;
            if($newsTopVideo->save()){
                \Session()->flash('flash_message', 'Top news video deleted successfully!');
            }else{
                \Session()->flash('error_message', 'Invalid request, please try again!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('news/managetopnewsvideo');
    }
    public function dashboardlist($status){
        $news = new News();
        $condition = array();
        $autoArr = array();
        $common = new Common();
        $start_date = '';
        $end_date = '';
        $firstDate = '';
        $endDate = '';
        if(\Session::get('user_type_id') != \Config::get('constants.ADMIN_USERTYPE') && \Session::get('user_type_id') != \Config::get('constants.SUPERADMIN_USERTYPE')){
            $condition += array(
                'news.user_id'      => \Session::get('user_id'),
            );
        }
        if($status == 'publish'){
            $condition += array(
                'news.is_publish'   => 1,
            );
            $news->flag_column = 'is_publish';
            $news->flag_value = 1;
        }else if($status == 'approved'){
            $condition += array(
                'news.is_approved'   => 1,
            );
            $news->flag_column = 'is_approved';
            $news->flag_value = 1;
        }else if($status == 'pending'){
            $condition += array(
                'news.is_publish'   => 1,
                'news.is_approved'  => 0,
            );
            $news->flag_column = 'is_approved';
            $news->flag_value = 0;
        }else{
            return redirect('dashboard');
        }
        $datas = DB::table('news')
        ->select('news.*','news_categories.name as categoryname','users.name as username')
        ->leftJoin('news_categories','news_categories.id','=','news.cat_id')
        ->leftJoin('users','users.id','=','news.user_id')
        ->where($condition)
        ->OrderBy('news.id','desc')
        ->paginate(15);
        $newsCatList = [''=>'--All--'] + NewsCategory::where('parent_id',0)->OrderBy('name','asc')->lists('name', 'id')->toArray();

        if(\Session::get('user_type_id') == \Config::get('constants.ADMIN_USERTYPE') || \Session::get('user_type_id') == \Config::get('constants.SUPERADMIN_USERTYPE')){
            $statusArr = array('is_approved'=>'Approved','is_publish'=>'Published','is_hot'=>'Top News','is_featured'=>'Featured','is_top_story'=>'Other Story','is_enable'=>'Enable','is_archive'=>'Archive');
        }else{
            $statusArr = array('is_publish'=>'Published','is_approved'=>'Approved','is_hot'=>'Top News','is_featured'=>'Featured','is_top_story'=>'Other Story','is_enable'=>'Enable','is_archive'=>'Archive');
        }
        $user = DB::table('users')->where('is_trash',0)->get();
        return view('news.newslist', [
            'news'          => $news,
            'user'          => $user,
            'datas'         => $datas,
            'title'         => 'News List',
            'user_type_id'  => \Session::get('user_type_id'),
            'newsCatList'   => $newsCatList,
            'statusArr'     => $statusArr,
            'autoArr'       => json_encode($autoArr),
            'firstDate'     => $firstDate,
            'endDate'       => $endDate
        ]);
    }
    public function thumbnailStore(Request $request){
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            if(isset($data['video_title']) && $data['video_title'] != ''){
                $news = News::find($data['id']);
                $news->video_title = $data['video_title'];
                $news->save();
            }
            $filePath = public_path().'/file/news/original/';
            if($request->hasFile('file_path')){
                $file = \Input::file('file_path');
                $extension = strtolower($file->getClientOriginalExtension());
                if($extension == 'jpg'){
                    $namewithoutext = pathinfo($data['file_name'], PATHINFO_FILENAME);
                    $name = $namewithoutext.'.'.$extension;
                    $file->move($filePath, $name);
                    $resizeObj = new resize($filePath.$name);
                    $resizeObj -> resizeImage(360, 180, 'exact');
                    $resizeObj -> saveImage(public_path().'/file/news/exact_360_180/'.$name, 100);

                    $resizeObj = new resize($filePath.$name);
                    $resizeObj -> resizeImage(375, 200, 'exact');
                    $resizeObj -> saveImage(public_path().'/file/news/exact_375_200/'.$name, 100);
					
					$resizeObj = new resize($filePath.$name);
                    $resizeObj -> resizeImage(285, 170, 'exact');
                    $resizeObj -> saveImage(public_path().'/file/news/exact_285_170//'.$name, 100);
					
					$resizeObj = new resize($filePath.$name);
                    $resizeObj -> resizeImage(350, 224, 'exact');
                    $resizeObj -> saveImage(public_path().'/file/news/exact_350_224/'.$name, 100);
					
					$resizeObj = new resize($filePath.$name);
                    $resizeObj -> resizeImage(553, 270, 'exact');
                    $resizeObj -> saveImage(public_path().'/file/news/exact_553_270/'.$name, 100);
					
					$resizeObj = new resize($filePath.$name);
                    $resizeObj -> resizeImage(560, 360, 'exact');
                    $resizeObj -> saveImage(public_path().'/file/news/exact_560_360/'.$name, 100);
					
					$resizeObj = new resize($filePath.$name);
                    $resizeObj -> resizeImage(750, 360, 'exact');
                    $resizeObj -> saveImage(public_path().'/file/news/exact_750_360/'.$name, 100);
					
					$resizeObj = new resize($filePath.$name);
                    $resizeObj -> resizeImage(93, 73, 'exact');
                    $resizeObj -> saveImage(public_path().'/file/news/exact_93_73/'.$name, 100);
					
                    \Session()->flash('flash_message', 'Thumbnail image change successfully!');
                }else{
                    \Session()->flash('error_message', 'Upload only jpg file!');
                }
            }else if(isset($data['video_id']) && is_array($data['video_id']) && count($data['video_id'])>0){
                foreach($data['video_id'] as $videoKey=>$videoVal){
                    if($request->hasFile('filelink_'.$videoVal)) {
                        $file = \Input::file('filelink_'.$videoVal);
                        $extension = strtolower($file->getClientOriginalExtension());
                        if($extension == 'jpg'){
                            $namewithoutext = pathinfo($data['filelinkname_'.$videoVal], PATHINFO_FILENAME);
                            $name = $namewithoutext.'.'.strtolower($file->getClientOriginalExtension());
                            $file->move($filePath, $name);
                            $resizeObj = new resize($filePath.$name);
                            $resizeObj -> resizeImage(360, 180, 'exact');
                            $resizeObj -> saveImage(public_path().'/file/news/exact_360_180/'.$name, 100);

                            $resizeObj = new resize($filePath.$name);
                            $resizeObj -> resizeImage(375, 200, 'exact');
                            $resizeObj -> saveImage(public_path().'/file/news/exact_375_200/'.$name, 100);

                            $resizeObj = new resize($filePath.$name);
                            $resizeObj -> resizeImage(750, 360, 'exact');
                            $resizeObj -> saveImage(public_path().'/file/news/exact_750_360/'.$name, 100);

                            \Session()->flash('flash_message', 'Thumbnail image change successfully!');
                        }else{
                            \Session()->flash('error_message', 'Upload only jpg file!');
                        }
                    }
                }
            }else{
                //\Session()->flash('error_message', 'Invalid request, please try again!');
            }
            $id = base64_encode($data['id']);
            return redirect('news/show/'.$id);
        }else{
            return redirect('news/list');
        }
    }
    public function topvideos(){
        $datas = DB::table('news')
        ->select('news.id','news.name','slug','short_description as shortdescription','file_path','is_video','approved_date','users.name as username','journalist_name')
        ->leftJoin('users', 'news.user_id', '=', 'users.id')
        ->where(['is_video'=>1,'is_approved'=>1,'is_enable'=>1,'is_top_video'=>1,'news.is_trash'=>0])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
        ->OrderBy('position','ASC')
        ->OrderBy('approved_date','DESC')
        ->paginate(15);

        $metaDesc       = "Online Videos Odisha News and Watch Videos, News Video Online for free. Visit OmmcomNews.com News Headlines video News Clips, Watch TV News.";
        $metaKeywords   = "Online Odisha News Videos, News Videos Clips, Watch News Videos, odisha news, odisha live news, orissa news, odisha news bulletin, Latest odisha news, odisha news portal, odia news, ommcom news, odisha top news site, top odisha news website, orissa news, best orissa news, best odisha news, online odisha news, online orissa news, online news odisha, news in india, breaking news, today news, current news, indian news, news website, india news, world news, business news, bollywood news, cricket news, sports, lifestyle, gadgets, tech news, video news, online tv news, news on videos, latest news on videos, economy news, political news, celebrity news, international news, current affairs, top news, weekly news, local news, news headlines, news website, stock market, mutual funds, Hindi movies, India online shopping, ollywood news, bhubaneswar news, cuttack news, puri news, barahampur news, sambalpur news, Rourkela news, automobile news, property news, vacancy news, education news, odisha live tv, oriya live tv news, odia news videos, cinema news, best news website odisha, odisha no1 news site, odisha’s popular news site, odisha news headlines, odisha breaking news, 24*7 odisha news, 24x7 odisha news, popular oriya news site";
        $title          = "OMMCOMNews.com : Odisha News Videos, News Videos Clips, Watch News Videos";

        return view('news.topvideos', [
            'datas'             => $datas,
            'metaDesc'          => $metaDesc,
            'metaKeywords'      => $metaKeywords,
            'title'             => $title,
        ]);
    }
    public function viralvideos(){
        $datas = DB::table('news')
        ->select('news.id','news.name','slug','short_description as shortdescription','file_path','is_video','approved_date','users.name as username','journalist_name')
        ->leftJoin('users', 'news.user_id', '=', 'users.id')
        ->where(['is_video'=>1,'is_approved'=>1,'is_enable'=>1,'is_viral'=>1,'news.is_trash'=>0])
        ->OrderBy(DB::raw('DATE(approved_date)'),'DESC')
        ->OrderBy('position','ASC')
        ->OrderBy('approved_date','DESC')
        ->paginate(15);
        return view('news.viralvideos', [
            'datas'          => $datas,
        ]);
    }
    public function topvideothumbnailStore(Request $request){
        $data = $request->all();
        if($request->hasFile('file_path') && isset($data['video_file']) && $data['video_file'] != ''){
            $filePath = public_path().'/file/topnewsvideo/';
            $file = \Input::file('file_path');
            $extension = strtolower($file->getClientOriginalExtension());
            if($extension == 'jpg'){
                $namewithoutext = pathinfo($data['video_file'], PATHINFO_FILENAME);
                $name = $namewithoutext.'.'.$extension;
                if($file->move($filePath, $name)){
                    \Session()->flash('flash_message', 'Thumbnail image change successfully!');
                }else{
                    \Session()->flash('error_message', 'Upload only jpg file!');
                }
            }else{
                \Session()->flash('error_message', 'Upload only jpg file!');
            }
        }else{
            \Session()->flash('error_message', 'Upload only jpg file!');
        }
        return redirect('news/managetopnewsvideo');
    }
    public function removefeaturedimage(Request $request){
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0 && isset($data['param']) && $data['param'] != ''){
            $news = News::find($data['id']);
            $resizeImage = \Config::get('constants.RESIZE_IMAGE');
            $filePath = public_path().'/file/news/';
            $featured_image = $news->featured_image;
            $file_path = $news->file_path;
            if($data['param'] == 'featured_image'){
                $news->featured_image = '';
            }else if($data['param'] == 'is_image'){
                $news->file_path = '';
                $news->is_image  = 0;
            }else if($data['param'] == 'is_video'){
                $news->file_path = '';
                $news->is_video  = 0;
            }
            if($news->save()){
                if($data['param'] == 'featured_image'){
                    if(file_exists($filePath.'original/'.$featured_image)){
                        unlink($filePath.'original/'.$featured_image);
                    }
                    if(is_array($resizeImage) && count($resizeImage)>0){
                        foreach($resizeImage as $resizeKey=>$resizeVal){
                            if(file_exists($filePath.$resizeKey.'/'.$featured_image)){
                                unlink($filePath.$resizeKey.'/'.$featured_image);
                            }
                        }
                    }
                }
                if($data['param'] == 'is_image'){
                    if(file_exists($filePath.'original/'.$file_path)){
                        unlink($filePath.'original/'.$file_path);
                    }
                    if(is_array($resizeImage) && count($resizeImage)>0){
                        foreach($resizeImage as $resizeKey=>$resizeVal){
                            if(file_exists($filePath.$resizeKey.'/'.$file_path)){
                                unlink($filePath.$resizeKey.'/'.$file_path);
                            }
                        }
                    }
                }
                if($data['param'] == 'is_video'){
                    if(file_exists($filePath.'original/'.$file_path)){
                        unlink($filePath.'original/'.$file_path);
                    }
                }
                echo 'SUCC';
            }else{
                echo 'FAIL';
            }
        }else{
            echo 'FAIL';
        }
    }
    public function removeOtherImages(Request $request){
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $newsImage = NewsImage::find($data['id']);
            if($newsImage->delete()){
                echo 'SUCC';
            }else{
                echo 'FAIL';
            }
        }else{
            echo 'FAIL';
        }
    }
    public function removeOtherVideos(Request $request){
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $newsVideo = NewsVideo::find($data['id']);
            if($newsVideo->delete()){
                echo 'SUCC';
            }else{
                echo 'FAIL';
            }
        }else{
            echo 'FAIL';
        }
    }
    public function sentMail(Request $request){
        $data = $request->all();
        $toMail = $data['to_mail'];
        //return $toMail;
        $fromMail = $data['from_mail'];
        Mail::send('emails.newsmail', ['content' => $data['content'],'url' =>$data['url'],'name' => $fromMail ], function ($m) use ($toMail,$fromMail){
            $m->to($toMail, 'Ommcom News')->subject('Ommcom News');
        }); 
        echo 'SUCC';
    }
}
