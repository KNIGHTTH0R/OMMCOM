<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\UserType;
use App\SettingsMail;
use App\Medias;
use App\Menu;
use App\Sponsor;
use App\NewsCategory;
use App\Version;
use App\AdminMenu;
use App\RoleMenu;
use App\Page;
use App\Question;
use App\QuestionOption;
use App\Http\Requests\CreateSettingsMailFormRequest;
use App\Http\Requests\CreateUserTypeFormRequest;
use App\Http\Requests\CreateMediaFormRequest;
use App\Http\Requests\CreateMenuFormRequest;
use App\Http\Requests\CreateSponsorFormRequest;
use App\Http\Requests\CreateNewsCategoryFormRequest;
use App\Http\Requests\CreateVersionFormRequest;
use App\Http\Requests\CreateAdminMenuFormRequest;
use App\Http\Requests\CreateRoleFormRequest;
use App\Http\Requests\CreateQuestionFormRequest;
use App\Http\Requests\CreateQuestionOptionFormRequest;
use Session;
use Input;
use Validator;
use App\Common;


class MasterController extends Controller{

    public function index(){
        if(\Session::get('user_type_id') == \Config::get('constants.ADMIN_USERTYPE') || \Session::get('user_type_id') == \Config::get('constants.SUPERADMIN_USERTYPE')){
            $is_pending = DB::table('news')->where('is_approved',0)->where('is_publish',1)->count();
            $is_publish = DB::table('news')->where('is_publish',1)->count();
            $is_approve = DB::table('news')->where('is_approved',1)->count();
            $is_draft   = 0;
        }else{
            $is_pending = DB::table('news')->where('is_approved',0)->where('is_publish',1)->where('user_id',\Session::get('user_id'))->count();
            $is_publish = DB::table('news')->where('is_publish',1)->where('user_id',\Session::get('user_id'))->count();
            $is_draft   = DB::table('news')->where('is_draft',1)->where('is_publish',0)->where('user_id',\Session::get('user_id'))->count();
            $is_approve = DB::table('news')->where('is_approved',1)->where('user_id',\Session::get('user_id'))->count();
        }
        $tot_citizenJourn = DB::table('citizen_news')->where('is_trash',0)->count();
        $condition = array();
        if(\Session::get('user_type_id') == \Config::get('constants.ADMIN_USERTYPE') || \Session::get('user_type_id') == \Config::get('constants.SUPERADMIN_USERTYPE')){
            $condition += array(
                'news.is_publish'       => 1,
                'news.is_approved'      => 0,
            );
        }else{
            $condition += array(
                'news.user_id'      => \Session::get('user_id'),
                'news.is_publish'   => 0,
                'news.is_draft'     => 1,
            );
        }    

        $datas = DB::table('news')->select('news.*','news_categories.name as categoryname','users.name as username')
        ->where($condition)->leftJoin('news_categories','news_categories.id','=','news.cat_id')
        ->leftJoin('users','users.id','=','news.user_id')
        ->OrderBy('news.id','desc')->paginate(5); 
        return view('master.dashboard',['title'=>'Dashboard', 'datas'=> $datas , 'is_publish' => $is_publish, 'is_approve' => $is_approve, 'is_pending' => $is_pending, 'tot_citizenJourn' => $tot_citizenJourn, 'is_draft' => $is_draft, 'user_type_id'=>\Session::get('user_type_id')]);
    }
    
    public function usertypelist(){
        $datas = DB::table('user_types')->where('is_trash',0)->paginate(15);
        return view('master.usertypelist', ['datas' => $datas ,'title'=>'User Type List']);
    }
    /**
     * [usertypedit description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function usertypedit($id){
        $UserType = UserType::findOrFail($id);
        return view('master.usertypecreate', ['UserType' => $UserType,'title'=>'Update User Type' ]); 
    }
    
    public function usertypedelete($id){
        $UserType = UserType::find($id);
        $UserType->is_trash = 1;
        $UserType->save();
        if($UserType->save()){
            \Session()->flash('flash_message', 'User Type deleted successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('usertypelist');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function usertypecreate()
    {
        $UserType = new UserType;
        return view('master.usertypecreate', ['UserType' => $UserType,'title'=>'Add User Type' ]); 
    }
    //Active/Inactive Action
    public function usertypeaction($id){
        $UserType = UserType::find($id);
        if(isset($UserType->is_active) && $UserType->is_active == 1){
            $UserType['is_active'] = 0;
        }else{
            $UserType['is_active'] = 1;
        }
        if($UserType->save()){
            if($UserType->is_active == 0){
                \Session()->flash('flash_message', 'User Type de-activated successfully!');
            }else{
                \Session()->flash('flash_message', 'User Type activated successfully!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('usertypelist');
    }  

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function usertypestore(CreateUserTypeFormRequest $request)
    {
        //return $request->user();
            $data = $request->all();
            if(isset($data['id']) && (int)$data['id'] != 0){
                $UserType = UserType::find($data['id']);
            }else{
                $UserType = new UserType();
            }
            if($UserType->fill($data)->save()){
                \Session()->flash('flash_message', 'User Types saved successfully!');
            }else{
                \Session()->flash('error_message', 'Invalid request, please try again!');
            }
            return redirect('usertypelist');
    }
    /**
     * [maillist description]
     * @return [type] [description]
     */
    public function maillist(){
        $datas = SettingsMail::all();
        return view('master.maillist', ['datas' => $datas,'title'=>'Mail List' ]);
    }
    /**
     * [mailedit description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function mailedit($id){
        $SettingsMail = SettingsMail::findOrFail($id);
        return view('master.mailcreate', ['SettingsMail' => $SettingsMail,'title'=>'Update Mail Setting' ]); 
    }
    /**
     * [maildelete description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function maildelete($id){
        $SettingsMail = SettingsMail::find($id)->delete();
        return redirect('maillist');
    }
    /**
     * [mailcreate description]
     * @return [type] [description]
     */
    public function mailcreate()
    {
        $SettingsMail = new SettingsMail;
        return view('master.mailcreate', ['SettingsMail' => $SettingsMail,'title'=>'Add Mail Setting' ]); 
    }
    /**
     * [mailstore description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function mailstore(CreateSettingsMailFormRequest $request)
    {
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $SettingsMail = SettingsMail::find($data['id']);
        }else{
            $SettingsMail = new SettingsMail();
        }
        if($SettingsMail->fill($data)->save()){
            \Session()->flash('flash_message', 'Mail Settings saved successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('maillist');
    }
    /**
     * [medialist description]
     * @return [type] [description]
     */
    public function medialist(){
        //$datas = Medias::all();
        $datas = DB::table('medias')->paginate(15);
        return view('master.medialist', ['datas' => $datas,'title'=>'Media List' ]);
    }
    /**
     * [mediaedit description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function mediaedit($id){
        $Media = Medias::findOrFail($id);
        return view('master.mediacreate', ['Media' => $Media,'title'=>'Update Media' ]); 
    }
    /**
     * [mediadelete description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function mediadelete($id){
        $Media = Medias::find($id)->delete();
        return redirect('medialist');
    }
    /**
     * [mediacreate description]
     * @return [type] [description]
     */
    public function mediacreate()
    {
        $Media = new Medias;
        return view('master.mediacreate', ['Media' => $Media,'title'=>'Add Media' ]); 
    }
    /**
     * [mediastore description]
     * @param  CreateMediaFormRequest $request [description]
     * @return [type]                          [description]
     */

    //Enable/Disable Action
    public function mediaction($id){
        $Media = Medias::find($id);
        if(isset($Media->is_enable) && $Media->is_enable == 1){
            $Media['is_enable'] = 0;
        }else{
            $Media['is_enable'] = 1;
        }
        if($Media->save()){
            if($Media->is_enable == 0){
                \Session()->flash('flash_message', 'Media de-activated successfully!');
            }else{
                \Session()->flash('flash_message', 'Media activated successfully!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('medialist');
    }

    public function mediastore(CreateMediaFormRequest $request)
    {
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $Media = Medias::find($data['id']);
        }else{
            $Media = new Medias();
        }
        $Media->fill($data)->save();
        if($Media->fill($data)->save()){
            \Session()->flash('flash_message', 'Media saved successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('medialist');
    }
    /**
     * [menulist description]
     * @return [type] [description]
     */
    public function menulist(){
        //$datas = Menu::tree();
        return view('master.menulist', ['title'=>'Menu List' ]);
    }   
    /**
     * [menuedit description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function menuedit($id){
        $Menu = Menu::findOrFail($id);
        $parentList = [''=>'-Select Parent-'] + $Menu->where('parent_id',0)->lists('name', 'id')->toArray();
        return view('master.menucreate', ['Menu' => $Menu,'title'=>'Update Menu', 'parentList'=>$parentList  ]); 
    }
    /**
     * [menudelete description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function menudelete($id){
        $Menu = Menu::find($id)->delete();
        return redirect('menulist');
    }
    /**
     * [menucreate description]
     * @return [type] [description]
     */
    public function menucreate()
    {
        $Menu = new Menu;
        $parentList = [''=>'-Select Parent-'] + $Menu->where(['parent_id'=>0,'is_enable'=>1])->lists('name', 'id')->toArray();
        return view('master.menucreate', ['Menu' => $Menu,'title'=>'Add Menu','parentList'=>$parentList]); 
    }
    /**
     * [menustore description]
     * @param  CreateMediaFormRequest $request [description]
     * @return [type]                          [description]
     */
    //Enable/Disable Action
    public function menuaction($id){
        $menu = Menu::find($id);
        if(isset($menu->is_enable) && $menu->is_enable == 1){
            $menu['is_enable'] = 0;
            $isActive = 0;
        }else{
            $menu['is_enable'] = 1;
            $isActive = 1;
        }
        if($menu->save()){
            if((int)$menu->parent_id == 0 && (int)$isActive == 0){
                DB::table('menus')->where('parent_id', $id)->update(['is_enable' =>0]);
            }             
            if($menu->is_enable == 0){
                \Session()->flash('flash_message', 'Menu de-activated successfully!');
            }else{
                \Session()->flash('flash_message', 'Menu activated successfully!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('menulist');
    }

    public function menustore(CreateMenuFormRequest $request)
    {
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $Menu = Menu::find($data['id']);
        }else{
            $Menu = new Menu();
        }
        if($Menu->fill($data)->save()){
            \Session()->flash('flash_message', 'Menu saved successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('menulist');
    }
    /**
     * [sponsorlist description]
     * @return [type] [description]
     */
    

    public function sponsorlist(){
        //$datas = Sponsor::all();
        $datas = DB::table('sponsors')->paginate(15);
        return view('master.sponsorlist', ['datas' => $datas,'title'=>'Sponsor List' ]);
    }
    /**
     * [sponsoredit description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function sponsoredit($id){
        $Sponsor = Sponsor::findOrFail($id);
        return view('master.sponsorcreate', ['Sponsor' => $Sponsor,'title'=>'Update Sponsor' ]); 
    }
    /**
     * [sponsordelete description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function sponsordelete($id){
        $Sponsor = Sponsor::find($id);
        if($Sponsor) 
        {
            $Sponsor->is_trash = '1';
        }
        if($Sponsor->save()){
            \Session()->flash('flash_message', 'Sponsor deleted successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('sponsorlist');
    }
    /**
     * [sponsorcreate description]
     * @return [type] [description]
     */
    public function sponsorcreate(){
        $Sponsor = new Sponsor;
        return view('master.sponsorcreate', ['Sponsor' => $Sponsor,'title'=>'Add Sponsor' ]); 
    }
    /**
     * [sponsorstore description]
     * @param  CreateSponsorFormRequest $request [description]
     * @return [type]                            [description]
     */
    public function sponsoraction($id){
        $Sponsor = Sponsor::find($id);
        if(isset($Sponsor->is_enable) && $Sponsor->is_enable == 1){
            $Sponsor['is_enable'] = 0;
        }else{
            $Sponsor['is_enable'] = 1;
        }
        if($Sponsor->save()){
            if($Sponsor->is_enable == 0){
                \Session()->flash('flash_message', 'Sponsor disabled successfully!');
            }else{
                \Session()->flash('flash_message', 'Sponsor enabled successfully!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('sponsorlist');
    }

    public function sponsorstore(CreateSponsorFormRequest $request){
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $Sponsor = Sponsor::find($data['id']);
        }else{
            $Sponsor = new Sponsor();
        }
        if($Sponsor->fill($data)->save()){
            \Session()->flash('flash_message', 'Sponsor saved successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('sponsorlist');
    }
    /**
     * [newscatlist description]
     * @return [type] [description]
     */
    public function newscatlist(){
        /* $datas = DB::table('news_categories as n')
        ->Select(['n.id','n.is_active', 'n.parent_id', 'c.name as parentname', 'n.description', 'n.name as newsname', 'n.slug', 'n.order'])
        ->leftJoin('news_categories as c', 'c.id', '=', 'n.parent_id')
        ->orderBy('id','DESC')->paginate(15); */
        return view('master.newscatlist', ['title'=>'News Category List']);        
    }
    /**
     * [newscatedit description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function newscatedit($id){
        $NewsCategory = NewsCategory::findOrFail($id);
        $parentList = [''=>'-Select Parent-'] + $NewsCategory->where('parent_id',0)->lists('name', 'id')->toArray();
        return view('master.newscatcreate', ['NewsCategory' => $NewsCategory,'title'=>'Update News Category','parentList'=>$parentList ]); 
    }
    /**
     * [newscatdelete description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */

   /* public function newscatdelete($id){
        $NewsCategory = NewsCategory::find($id);
        if($NewsCategory) 
        {
            $NewsCategory->is_trash = '1';
            $NewsCategory->save();
            return redirect('newscatlist');
        }
    }*/
    
    /**
     * [newscatcreate description]
     * @return [type] [description]
     */
    public function newscatcreate()
    {
        $NewsCategory = new NewsCategory;
        $parentList = [''=>'-Select Parent-'] + $NewsCategory->where(['parent_id'=>0,'is_active'=>1])->lists('name', 'id')->toArray();
        return view('master.newscatcreate', ['NewsCategory' => $NewsCategory,'title'=>'Add News Category','parentList'=>$parentList ]); 
    }
    /**
     * [newscatstore description]
     * @param  CreateMediaFormRequest $request [description]
     * @return [type]                          [description]
     */

    public function newscataction($id){
        $NewsCategory = NewsCategory::find($id);
        if(isset($NewsCategory->is_active) && $NewsCategory->is_active == 1){
            $NewsCategory['is_active'] = 0;
            $isActive = 0;
        }else{
            $NewsCategory['is_active'] = 1;
            $isActive = 1;
        }
        if($NewsCategory->save()){
            if((int)$NewsCategory->parent_id == 0 && (int)$isActive == 0){
                DB::table('news_categories')->where('parent_id', $id)->update(['is_active' =>0]);
            }
            if($NewsCategory->is_active == 0){
                \Session()->flash('flash_message', 'News Category de-activated successfully!');
            }else{
                \Session()->flash('flash_message', 'News Category activated successfully!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('newscatlist');
    }

    public function newscatstore(CreateNewsCategoryFormRequest $request)
    {
        $data = $request->all(); 
        if(isset($data['id']) && (int)$data['id'] != 0){
            $NewsCategory = NewsCategory::find($data['id']);
        }else{
            $NewsCategory = new NewsCategory();
        }
        if (Input::hasFile('images'))
        {
            $destinationPath = public_path().'/file/';
            $file = Input::file('images');
            $fileName = rand(11111,99999).'-'.time(). '-' .$file->getClientOriginalName(); // getting image name

            $NewsCategory->images = $fileName;
            $data['images'] = $fileName;
            $file->move($destinationPath, $fileName); // uploading file to given path
        }
         
        if(Input::hasFile('featured_image'))
        {
            $destinationPath = public_path().'/file/';
            $file = Input::file('featured_image');
            $fileName = rand(11111,99999).'-'.time(). '-' .$file->getClientOriginalName(); // getting image name

            $NewsCategory->featured_image = $fileName;
            $data['featured_image'] = $fileName;
            $file->move($destinationPath, $fileName); // uploading file to given path
        } 
        if($NewsCategory->fill($data)->save()){
            \Session()->flash('flash_message', 'News Category saved successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('newscatlist');
    }

    public function versionlist(){
        $datas = DB::table('versions')->where('is_trash', 0)->paginate(15);
        return view('master.versionlist', ['datas' => $datas,'title'=>'Version List' ]);
    }
    
    public function versioncreate()
    {
        $version = new Version;
        return view('master.versioncreate', ['version' => $version,'title'=>'Add Version']); 
    }

    public function versionedit($id){
        $version = Version::findOrFail($id);
        return view('master.versioncreate', ['version' => $version,'title'=>'Update Version' ]); 
    }
    
    public function versiondelete($id){
        $version = Version::find($id);
        if($version) 
        {
            $version->is_trash = '1';
        }
        if($version->save()){
            \Session()->flash('flash_message', 'Version deleted successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('version/list');    
    }

     public function versionaction($id){
        $version = Version::find($id);
        if(isset($version->is_enable) && $version->is_enable == 1){
            $version['is_enable'] = 0;
        }else{
            $version['is_enable'] = 1;
        }
        if($version->save()){
            if($version->is_enable == 0){
                \Session()->flash('flash_message', 'Version disabled successfully!');
            }else{
                \Session()->flash('flash_message', 'Version enabled successfully!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('version/list');
    }

    public function versionstore(CreateVersionFormRequest $request)
    {
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $version = Version::find($data['id']);
        }else{
            $version = new Version();
        }
        if($version->fill($data)->save()){
            \Session()->flash('flash_message', 'Version saved successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('version/list');
    }

    public function adminmenulist(){
        $datas = DB::table('admin_menus')->where('is_trash', 0)->orderBy('created_at', 'DESC')->paginate(15);
        return view('master.adminmenulist', ['datas' => $datas ,'title'=>'Admin Menu List']);
    }

    public function adminmenuedit($id){
        $adminMenu = AdminMenu::findOrFail($id);
        $parentList = [''=>'-Select Parent-'] + $adminMenu->where('parent_id',0)->lists('name', 'id')->toArray();
        return view('master.adminmenucreate', ['adminMenu' => $adminMenu, 'parentList' => $parentList, 'title'=>'Update Admin Menu' ]); 
    }

    public function adminmenudelete($id){
        $adminMenu = AdminMenu::find($id);
        if($adminMenu) 
        {
            $adminMenu->is_trash = '1';
        }
        if($adminMenu->save()){
            \Session()->flash('flash_message', 'Admin Menu deleted successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('adminmenu/list');
    }

    public function adminmenucreate()
    {
        $adminMenu = new AdminMenu;
        $parentList = [''=>'-Select Parent-'] + $adminMenu->where(['parent_id'=>0,'is_enable'=>1])->lists('name', 'id')->toArray();
        return view('master.adminmenucreate', ['adminMenu' => $adminMenu, 'parentList' => $parentList, 'title'=>'Add Admin Menu' ]); 
    }

    //Active/Inactive Action
    public function adminmenuaction($id){
        $adminMenu = AdminMenu::find($id);
        if(isset($adminMenu->is_enable) && $adminMenu->is_enable == 1){
            $adminMenu['is_enable'] = 0;
            $isActive = 0;
        }else{
            $adminMenu['is_enable'] = 1;
            $isActive = 1;
        }
        if($adminMenu->save()){
            if((int)$adminMenu->parent_id == 0 && (int)$isActive == 0){
                DB::table('admin_menus')->where('parent_id', $id)->update(['is_enable' =>0]);
            }            
            if($adminMenu->is_enable == 0){
                \Session()->flash('flash_message', 'Admin Menu disabled successfully!');
            }else{
                \Session()->flash('flash_message', 'Admin Menu enabled successfully!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('adminmenu/list');
    }  

    public function adminmenustore(CreateAdminMenuFormRequest $request)
    {
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $adminMenu = AdminMenu::find($data['id']);
        }else{
            $adminMenu = new AdminMenu();
        }
        if($adminMenu->fill($data)->save()){
                \Session()->flash('flash_message', 'Admin Menu saved successfully!');
            }else{
                \Session()->flash('error_message', 'Invalid request, please try again!');
            }
        return redirect('adminmenu/list');
    }

    // public function rolecreate()
    // {
    //     $role = new RoleMenu;
    //     //$exist = DB::table('role_menus')->where('admin_menu_id', $admin_menu_id[$i])->where('user_type_id', )->first();
    //     //$checked = ($exist) ? 'checked = "checked"' : '';

    //     $usertypeList = [''=>'-Select User Type-'] + UserType::where('is_active',1)->lists('name', 'id')->toArray();
    //     return view('master.rolecreate', ['role' => $role,'title'=>'Add Role', 'usertypeList'=>$usertypeList]); 
    // }
    public function roleshow($id)
    {     
        $role = RoleMenu::find($id);
        $user_type_id = $role['id'];
        $getData = RoleMenu::where('user_type_id', '=', $id)->get();
        return view('master.rolecreate', ['id' => $id, 'getData' => $getData,'role' => $role, 'title'=>'Role Menu' ]); 
    }    

    public function rolestore(CreateRoleFormRequest $request)
    {
        $data = $request->all();
        if(isset($data['id']) && (int)$data['id'] != 0){
            $admin_sub_menu_id = Input::get('admin_sub_menu_id');

            DB::table('role_menus')->where('user_type_id',$data['id'])->delete();
            if (array_key_exists('admin_menu_id', $data)) {
                $admin_menu_id = $data['admin_menu_id'];
                for($i=0; $i < count($admin_menu_id); $i++) {
                    $role = new RoleMenu;
                    $role->user_type_id = $data['id'];
                    $role->admin_menu_id = $admin_menu_id[$i];
                    $role->admin_sub_menu_id = 0;
                    $role->save();
                } 
            }
            if (array_key_exists('admin_sub_menu_id', $data)) {
                for($i=0; $i < count($admin_sub_menu_id); $i++ ) {
                    $role = new RoleMenu;
                    $sub_explode = explode('-',$admin_sub_menu_id[$i]); 
                    $role->user_type_id = $data['id'];
                    $role->admin_menu_id = $sub_explode[1];
                    $role->admin_sub_menu_id = $sub_explode[0];
                    $role->save();
                }
            }
        }
        if($role->save()){
            \Session()->flash('flash_message', 'Role wise permission saved successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('usertypelist');        
    }

    public function questionlist()
    {
        $datas = DB::table('questions as q')
            ->select(DB::raw("q.name as question,q.is_statitics_to_public as public, qo.question_id,q.is_active,q.id,GROUP_CONCAT(qo.name SEPARATOR ',') as `option`"))
            ->leftJoin('question_options as qo', 'q.id', '=', 'qo.question_id')
            ->groupBy('qo.question_id')->where('q.is_trash', 0)
            ->orderBy('q.id', 'DESC')->get();

        return view('master.questionlist', ['datas' => $datas ,'title'=>'Vote Polling List']);
    }
    
    public function questioncreate()
    {
        $question = new Question;
        return view('master.questioncreate', ['question' => $question, 'title'=>'Add Vote Polling' ]); 
    }

    public function questionedit($id){
        $question = Question::findOrFail($id);
        $qId = $question->id;
        $result = QuestionOption::where('question_id', '=', $qId)->get();
        return view('master.questionedit', ['question' => $question, 'result' => $result, 'title'=>'Update Polling Questions' ]); 
    }

    public function questiondelete($id){
        $question = Question::find($id);
        if($question) 
        {
            $question->is_trash = '1';
        }
        if($question->save()){
            \Session()->flash('flash_message', 'Polling deleted successfully!');
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('question/list');
    }

     public function questionaction($id){
        $question = Question::find($id);
        if(isset($question->is_active) && $question->is_active == 1){
            $question['is_active'] = 0;
        }else{
            $question['is_active'] = 1;
        }
        if($question->save()){
            if($question->is_active == 0){
                \Session()->flash('flash_message', 'Question de-activated successfully!');
            }else{
                \Session()->flash('flash_message', 'Question activated successfully!');
            }
        }else{
            \Session()->flash('error_message', 'Invalid request, please try again!');
        }
        return redirect('question/list');
    }

    public function questionstore(CreateQuestionFormRequest $request)
    {
        $data = $request->all();
        //return count($data['id']); exit();
        if(isset($data['id']) && count($data['id']) > 0){
            DB::table('questions')->where('id', $data['id'])->delete();
            DB::table('question_options')->where('question_id', $data['id'])->delete();
            $question = new Question();
            $question->name=$data['name'];
            $question->save();
            $insertedId = $question->id;
            for($i = 0; $i < count($data['option']); $i++)
            {
                $qOption = new QuestionOption();
                $qOption->question_id = $insertedId;
                $qOption->name = $data['option'][$i];
                $qOption->position = $data['position'][$i];
                $qOption->save();
            }
        }
        else {
            $question = new Question();
            $question->name=$data['name'];
            $question->save();
            $insertedId = $question->id;
            for($i = 0; $i < count($data['option']); $i++)
            {
                $qOption = new QuestionOption();
                $qOption->question_id = $insertedId;
                $qOption->name = $data['option'][$i];
                $qOption->position = $data['position'][$i];
                $qOption->save();
            }
        }
        if($qOption->save()){
                \Session()->flash('flash_message', 'Vote Polling saved successfully!');
            }else{
                \Session()->flash('error_message', 'Invalid request, please try again!');
            }
        return redirect('question/list');
    }

    
}
