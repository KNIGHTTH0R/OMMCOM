<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePageFormRequest;
use App\Page;
use App\Menu;
use Input;
use App\Feedback;
use App\NewsCategory;
use App\News;
use App\Http\Requests\CreateFeedbackFormRequest;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pagelist(Request $request){
        $filter = Input::get('filter');

        if (isset($filter) && $filter == 'A'){
            $type = 1;
        }
        elseif(isset($filter) && $filter == 'I'){
            $type = 0;
        }
        else{
        $page = DB::table('pages')
        ->Select(['pages.id', 'pages.name as pname', 'menus.name as mname', 'pages.meta_desc', 'pages.meta_key', 'pages.content', 'pages.slug', 'pages.is_enable'])
        ->leftJoin('menus', 'menus.id', '=', 'pages.menu_id')
        ->where('pages.is_trash', 0)
        ->paginate(15);
        return view('page.pagelist', ['page' => $page,'title'=>'Page List' ]);

        }

        $page = DB::table('pages')
        ->Select(['pages.id', 'pages.name as pname', 'menus.name as mname', 'pages.meta_desc', 'pages.meta_key', 'pages.content', 'pages.slug', 'pages.is_enable'])
        ->leftJoin('menus', 'menus.id', '=', 'pages.menu_id')
        ->where('pages.is_trash', 0)
        ->where('pages.is_enable', $type)
        ->paginate(15);
        return view('page.pagelist', ['page' => $page,'title'=>'Page List' ]);

    }

    public function pagecreate()
    {
        $page = new Page;
        $menuList = [''=>'-Select Menu-'] + Menu::where('is_trash',0)->lists('name', 'id')->toArray();
        return view('page.pagecreate', ['page' => $page,'menuList'=>$menuList,'title'=>'Add Page' ]);

    }

    public function pageedit($id){
        $page = Page::findOrFail($id);
        $menuList = [''=>'-Select Menu-'] + Menu::where('is_trash',0)->lists('name', 'id')->toArray();
        return view('page.pagecreate', ['page' => $page,'title'=>'Update Page', 'menuList'=>$menuList  ]);
    }
    
    public function pagedelete($id){
        $page = Page::find($id);
        if($page) 
        {
            $page->is_trash = '1';
            $page->save();
            return redirect('page/list');
        }
    }
    public function pageaction($id){
        $page = Page::find($id);
        if(isset($page->is_enable) && $page->is_enable == 1){
            $page['is_enable'] = 0;
        }else{
            $page['is_enable'] = 1;
        }
        $page->save();
        return redirect('page/list');
    }

    public function pagestore(CreatePageFormRequest $request)
    {
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $page = Page::find($data['id']);
        }else{
            $page = new Page();
        }
        $page->user_id = \Session::get('user_id');
        $page->fill($data)->save();
        return redirect('page/list');
    }

    public function editorUpload(Request $request)
    {
        $data = $request->all();
        $destinationPath = public_path()."/file/editorUpload/";
        $file = $data['file'];
        $fileName = rand(11111,99999).'-'.time(). '-' .$file->getClientOriginalName(); // getting image name
        $file->move($destinationPath, $fileName);
        echo asset('/')."file/editorUpload/".$fileName;
    } 
    public function pages($slug){
        if($slug != ''){
            $page = new Page();

            $pages = $page->select('id','name','slug','content','meta_desc','meta_key')
            ->where(['is_enable'=>1,'is_trash'=>0,'slug'=>$slug])
            ->first();
            $metaDesc       = $pages->meta_desc;
            $metaKeywords   = $pages->meta_key;
            return view('page.pages', [
                'pages'         => $pages,
                'title'         => 'About Us : Odisha News, Latest Odisha, Odia News, Onilne Odisha News',
                'metaDesc'      => $metaDesc,
                'metaKeywords'  => $metaKeywords,
             ]);           
        }else{
            return redirect()->route('/');
        }
    }  
    public function feedback(){
        $feedback = new Feedback();
        return view('page.feedback', ['feedback'=>$feedback ]); 
    }
    public function feedbackStore(CreateFeedbackFormRequest $request){
        $data = $request->all();
        $feedback = new Feedback();
        if($feedback->fill($data)->save()){
            \Session()->flash('flash_message', 'Your feedback has been saved successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, Please try again!');
        }
        return redirect('pages/feedback'); 
    }
    public function feedbacklist(Request $request){
        $start_date = '1970-01-01 00:00:00';
        $end_date = date('Y-m-d H:i:s');
        $firstDate  = '';
        $endDate    = '';
        $feedback = new Feedback();
        $records = $request->all();
        if(is_array($records) && count($records)>0){
            if(isset($records['start_date']) && $records['start_date'] != ''){  
                $start_date = date('Y-m-d H:i:s',strtotime($records['start_date']));
                $firstDate = $records['start_date'];
            }
            if(isset($records['end_date']) && $records['end_date'] != ''){  
                $end_date = date('Y-m-d H:i:s', strtotime($records['end_date']. ' +1 day'));
                $endDate = $records['end_date'];
            }
        }
        $datas = DB::table('feedbacks')
        ->select('id','name','email','message','mobile','is_trash')
        ->where('is_trash',0)
        ->where("created_at",'>=',$start_date)
        ->where("created_at",'<=',$end_date)
        ->OrderBy('id','desc')
        ->paginate(10);
        return view('page.feedbacklist', [
            'feedback'      => $feedback,
            'datas'         => $datas, 
            'title'         => 'Feedback List',
            'firstDate'     => $firstDate,
            'endDate'       => $endDate
        ]);                  
    }
    public function feedbackDelete($id){
        if($id){
            $feedback = Feedback::find($id);
            $feedback->is_trash = 1;
            if($feedback->save()){
                \Session()->flash('flash_message', 'Feedback deleted successfully!');
            }else{
                \Session()->flash('error_message', 'Invalid request, please try again!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }  
        return redirect('pages/feedbacklist');      
    }
    public function sitemap(){
        $datas = NewsCategory::where(['is_active'=>1])->OrderBy('order','asc')->get();
        return view('page.sitemap', ['datas' => $datas]);   
    }
}      