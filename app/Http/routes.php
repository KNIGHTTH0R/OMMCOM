<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('/', 'UsersController@index');
/* User Authentication */
Route::get('users/login', 'Auth\AuthController@getLogin');
Route::post('users/login', 'Auth\AuthController@postLogin');
Route::get('users/logout', 'Auth\AuthController@getLogout');
Route::get('users/redirectpage', 'UsersController@redirectpage');
//Route::get('users/register', 'Auth\AuthController@getRegister');
//Route::post('users/register', 'Auth\AuthController@postRegister');

/* Authenticated users */
Route::group(['middleware' => 'auth'], function()
{
    Route::get('users/changepassword/{id}', 'UsersController@changepassword');
    Route::post('users/changepasswordstore', 'UsersController@changepasswordstore');
    Route::get('users/dashboard',[
        'as'    => 'dashboard',
        'uses'  => 'MasterController@index'
    ]);

    Route::get('dashboard','MasterController@index');
    Route::get('usertypelist','MasterController@usertypelist');
    Route::get('usertypecreate','MasterController@usertypecreate');
    Route::post('usertypestore','MasterController@usertypestore');
    Route::get('usertypedit/{id}',[
        'as' => 'master.usertypedit',
        'uses' => 'MasterController@usertypedit'
    ]);
    Route::get('usertypedelete/{id}',[
        'as' => 'master.usertypedelete',
        'uses' => 'MasterController@usertypedelete'
    ]);
    Route::get('usertypeaction/{id}',[
        'as' => 'master.usertypeaction',
        'uses' => 'MasterController@usertypeaction'
    ]);

    Route::get('maillist','MasterController@maillist');
    Route::get('mailcreate','MasterController@mailcreate');
    Route::post('mailstore','MasterController@mailstore');
    Route::get('mailedit/{id}',[
        'as' => 'master.mailedit',
        'uses' => 'MasterController@mailedit'
    ]);
    Route::get('maildelete/{id}',[
        'as' => 'master.maildelete',
        'uses' => 'MasterController@maildelete'
    ]);
    Route::get('sponsorlist','MasterController@sponsorlist');
    Route::get('sponsorcreate','MasterController@sponsorcreate');
    Route::post('sponsorstore','MasterController@sponsorstore');
    Route::get('sponsoredit/{id}',[
        'as' => 'master.sponsoredit',
        'uses' => 'MasterController@sponsoredit'
    ]);
    Route::get('sponsordelete/{id}',[
        'as' => 'master.sponsordelete',
        'uses' => 'MasterController@sponsordelete'
    ]);
    Route::get('sponsoraction/{id}',[
        'as' => 'master.sponsoraction',
        'uses' => 'MasterController@sponsoraction'
    ]); 

    Route::get('adminmenu/list','MasterController@adminmenulist');
    Route::get('adminmenu/add','MasterController@adminmenucreate');
    Route::post('adminmenu/store','MasterController@adminmenustore');
    Route::get('adminmenu/edit/{id}',[
        'as' => 'master.adminmenuedit',
        'uses' => 'MasterController@adminmenuedit'
    ]);
    Route::get('adminmenu/delete/{id}',[
        'as' => 'master.adminmenudelete',
        'uses' => 'MasterController@adminmenudelete'
    ]);
    Route::get('adminmenu/action/{id}',[
        'as' => 'master.adminmenuaction',
        'uses' => 'MasterController@adminmenuaction'
    ]);
    Route::get('question/list','MasterController@questionlist');
    Route::get('question/add','MasterController@questioncreate');
    Route::post('question/store','MasterController@questionstore');
    Route::get('question/edit/{id}',[
        'as' => 'master.questionedit',
        'uses' => 'MasterController@questionedit'
    ]);
    Route::get('question/delete/{id}',[
        'as' => 'master.questiondelete',
        'uses' => 'MasterController@questiondelete'
    ]);
    Route::get('question/action/{id}',[
        'as' => 'master.questionaction',
        'uses' => 'MasterController@questionaction'
    ]);

    Route::get('news/add','NewsController@create');
    Route::post('news/store','NewsController@store');
    Route::any('news/list','NewsController@newslist');
    Route::get('news/edit/{id}',[
        'as' => 'news.edit',
        'uses' => 'NewsController@edit'
    ]);
    Route::get('news/publish/{id}',[
        'as' => 'news.publish',
        'uses' => 'NewsController@publish'
    ]);    
    Route::get('news/action/{id}',[
        'as'    => 'news.action',
        'uses'  => 'NewsController@action'
    ]);
    Route::get('news/approve/{id}',[
        'as'    => 'news.approve',
        'uses'  => 'NewsController@approve'
    ]);
    Route::get('news/addimages/{id}',[
        'as'    => 'news.addimages',
        'uses'  => 'NewsController@addimages'
    ]);
    Route::get('news/imagedelete/{id}',[
        'as'    => 'news.imagedelete',
        'uses'  => 'NewsController@imageDelete'
    ]);
    Route::get('news/addvideos/{id}',[
        'as'    => 'news.addvideos',
        'uses'  => 'NewsController@addVideos'
    ]);
    Route::get('news/imageaction/{id}',[
        'as'    => 'news.imageaction',
        'uses'  => 'NewsController@imageAction'
    ]);
    Route::get('news/videoaction/{id}',[
        'as'    => 'news.videoaction',
        'uses'  => 'NewsController@videoAction'
    ]);
    Route::get('news/videodelete/{id}',[
        'as'    => 'news.videodelete',
        'uses'  => 'NewsController@videoDelete'
    ]);
    Route::get('news/show/{id}',[
        'as'    => 'news.show',
        'uses'  => 'NewsController@show'
    ]);
    Route::post('news/storeimages','NewsController@storeimages');
    Route::post('news/fileupload','NewsController@fileupload');
    Route::post('news/removefile','NewsController@removefile');
    Route::post('news/videoupload','NewsController@videoupload');
    Route::post('news/removevideo','NewsController@removevideo');
    Route::post('news/storevideos','NewsController@storevideos');
    Route::post('news/saveaction','NewsController@saveaction');
    Route::post('news/get-news-sub-category','NewsController@getNewsSubCategory');
    Route::any('news/autocomplete/{query?}','NewsController@autocomplete');
    Route::get('news/addtopnewsvideo','NewsController@addtopnewsvideo');
    Route::post('news/topnewsvideostore','NewsController@topnewsvideostore');  
    Route::get('news/managetopnewsvideo','NewsController@managetopnewsvideo');
    Route::get('news/topnewsvideoaction/{id}',[
        'as'    => 'news.topnewsvideoaction',
        'uses'  => 'NewsController@topnewsvideoaction'
    ]);
    Route::get('news/edittopnewsvideo/{id}',[
        'as' => 'news.edittopnewsvideo',
        'uses' => 'NewsController@edittopnewsvideo'
    ]);    
    Route::get('news/deletetopnewsvideo/{id}',[
        'as'    => 'news.deletetopnewsvideo',
        'uses'  => 'NewsController@deletetopnewsvideo'
    ]);    
    Route::get('news/dashboardlist/{status}','NewsController@dashboardlist');
    Route::post('news/thumbnailStore','NewsController@thumbnailStore');
    Route::post('news/topvideothumbnailStore','NewsController@topvideothumbnailStore');
    Route::get('news/trash/{id}',[
        'as' => 'news.trash',
        'uses' => 'NewsController@trash'
    ]); 


    Route::get('medialist','MasterController@medialist');
    Route::get('mediacreate','MasterController@mediacreate');
    Route::post('mediastore','MasterController@mediastore');
    Route::get('mediaedit/{id}',[
        'as' => 'master.mediaedit',
        'uses' => 'MasterController@mediaedit'
    ]);
    Route::get('mediadelete/{id}',[
        'as' => 'master.mediadelete',
        'uses' => 'MasterController@mediadelete'
    ]);
    Route::get('mediaction/{id}',[
        'as' => 'master.mediaction',
        'uses' => 'MasterController@mediaction'
    ]);

    Route::get('menulist','MasterController@menulist');
    Route::get('menucreate','MasterController@menucreate');
    Route::post('menustore','MasterController@menustore');
    Route::get('menuedit/{id}',[
        'as' => 'master.menuedit',
        'uses' => 'MasterController@menuedit'
    ]);
    Route::get('menudelete/{id}',[
        'as' => 'master.menudelete',
        'uses' => 'MasterController@menudelete'
    ]);
    Route::get('menuaction/{id}',[
        'as' => 'master.menuaction',
        'uses' => 'MasterController@menuaction'
    ]);


    Route::any('page/list','PageController@pagelist');
    Route::get('page/add','PageController@pagecreate');
    Route::post('page/store','PageController@pagestore');
    Route::get('page/edit/{id}',[
        'as' => 'page.pageedit',
        'uses' => 'PageController@pageedit'
    ]);
    Route::get('page/delete/{id}',[
        'as' => 'page.pagedelete',
        'uses' => 'PageController@pagedelete'
    ]);
    Route::get('page/action/{id}',[
        'as' => 'page.pageaction',
        'uses' => 'PageController@pageaction'
    ]);
    Route::post('page/editorUpload','PageController@editorUpload');

    Route::get('newscatlist','MasterController@newscatlist');
    Route::get('newscatcreate','MasterController@newscatcreate');
    Route::post('newscatstore','MasterController@newscatstore');
    Route::get('newscatedit/{id}',[
        'as' => 'master.newscatedit',
        'uses' => 'MasterController@newscatedit'
    ]);
    Route::get('newscatdelete/{id}',[
        'as' => 'master.newscatdelete',
        'uses' => 'MasterController@newscatdelete'
    ]);
    Route::get('newscataction/{id}',[
        'as' => 'master.newscataction',
        'uses' => 'MasterController@newscataction'
    ]);
    Route::get('advertisement/add','AdvertisementController@create');
    Route::post('advertisement/store','AdvertisementController@store');
    Route::post('advertisement/getSection','AdvertisementController@getSection');
    Route::get('advertisement/edit/{id}',[
        'as' => 'advertisement.advertisementedit',
        'uses' => 'AdvertisementController@advertisementedit'
    ]);
    Route::get('advertisement/delete/{id}',[
        'as' => 'advertisement.advertisementdelete',
        'uses' => 'AdvertisementController@advertisementdelete'
    ]);
    Route::get('advertisement/action/{id}',[
        'as' => 'advertisement.advertisementaction',
        'uses' => 'AdvertisementController@advertisementaction'
    ]);
    Route::get('advertisement/publish/{id}',[
        'as' => 'advertisement.advertisementpublish',
        'uses' => 'AdvertisementController@advertisementpublish'
    ]);
    Route::any('advertisement/list',[
        'as' => 'advertisement.advertisementlist',
        'uses' => 'AdvertisementController@advertisementlist'
    ]);
    
    Route::get('citizencustomize/add','CitizenJournalistController@create');
    Route::post('citizencustomize/cusstore','CitizenJournalistController@cusstore');
    Route::get('citizencustomize/cusedit/{id}',[
        'as' => 'CitizenCustomize.ccedit',
        'uses' => 'CitizenJournalistController@ccedit'
    ]);
    Route::get('citizencustomize/cusdelete/{id}',[
        'as' => 'CitizenCustomize.ccdelete',
        'uses' => 'CitizenJournalistController@ccdelete'
    ]);
    Route::get('citizencustomize/cusaction/{id}',[
        'as' => 'CitizenCustomize.ccaction',
        'uses' => 'CitizenJournalistController@ccaction'
    ]);
    Route::any('citizencustomize/list',[
        'as' => 'CitizenCustomize.citizencustomizelist',
        'uses' => 'CitizenJournalistController@cclist'
    ]);

    Route::get('citizenews/add','CitizenJournalistController@cnewscreate');
    Route::post('citizenews/store','CitizenJournalistController@cnewsstore');
    Route::post('citizenews/thumbnailStore','CitizenJournalistController@thumbnailStore');
    Route::get('citizenews/edit/{id}',[
        'as' => 'citizenews.cnewsedit',
        'uses' => 'CitizenJournalistController@cnewsedit'
    ]);
    Route::get('citizenews/download/{filename}',[
        'as' => 'citizenews.download',
        'uses' => 'CitizenJournalistController@download'
    ]);    
    Route::get('citizenews/delete/{id}',[
        'as' => 'citizenews.cnewsdelete',
        'uses' => 'CitizenJournalistController@cnewsdelete'
    ]);
    Route::get('citizenews/action/{id}',[
        'as' => 'citizenews.cnewsaction',
        'uses' => 'CitizenJournalistController@cnewsaction'
    ]);
    Route::any('citizenews/list',[
        'as' => 'citizenews.cnewslist',
        'uses' => 'CitizenJournalistController@cnewslist'
    ]);

    Route::get('citizen/news','CitizenJournalistController@news');
    
    Route::get('version/add','MasterController@versioncreate');
    Route::post('version/store','MasterController@versionstore');
    Route::get('version/list','MasterController@versionlist');
    Route::get('version/edit/{id}',[
        'as' => 'master.versionedit',
        'uses' => 'MasterController@versionedit'
    ]);
    Route::get('version/delete/{id}',[
        'as' => 'master.versiondelete',
        'uses' => 'MasterController@versiondelete'
    ]);
    Route::get('version/action/{id}',[
        'as' => 'master.versionaction',
        'uses' => 'MasterController@versionaction'
    ]);
    Route::get('users/add','UsersController@create');
    Route::post('users/store','UsersController@store');
    Route::get('users/manage','UsersController@userlist');
    Route::post('users/age','UsersController@age');
    Route::get('users/edit/{id}',[
        'as' => 'users.useredit',
        'uses' => 'UsersController@useredit'
    ]);
    Route::any('users/profile',[ 
        'as' => 'users.userprofile',
        'uses' => 'UsersController@userprofile'
    ]);
    Route::get('userdelete/{id}',[
        'as' => 'users.userdelete',
        'uses' => 'UsersController@userdelete'
    ]);
    Route::get('users/action/{id}',[
        'as'    => 'users.action',
        'uses'  => 'UsersController@action'
    ]);

    //Route::any('role/add','MasterController@rolecreate');
    Route::any('role/add/{id}',[
        'as'    => 'role.show',
        'uses'  => 'MasterController@roleshow'
    ]);
    Route::post('role/store','MasterController@rolestore');

    Route::get('conference/add','ConferenceController@create');
    Route::post('conference/store','ConferenceController@store');
    Route::get('conference/list','ConferenceController@conferencelist');
    Route::post('conference/durationindate','ConferenceController@durationindate');
    Route::get('conference/adduser/{id}',[
        'as'    => 'conference.adduser',
        'uses'  => 'ConferenceController@adduser'
    ]);
    Route::post('conference/storeuser','ConferenceController@storeuser');
    Route::post('conference/check','ConferenceController@checkconference');
    Route::post('conference/start','ConferenceController@startConference');
    Route::post('conference/sendAnnouncement','ConferenceController@sendAnnouncement');
    Route::post('conference/pushAnnouncement','ConferenceController@pushAnnouncement');    
    Route::post('conference/close','ConferenceController@close');
    Route::post('conference/push','ConferenceController@pushConference');     
    Route::get('conference/edituser/{id}',[
        'as'    => 'conference.edituser',
        'uses'  => 'ConferenceController@edituser'
    ]);
    Route::get('conference/edit/{id}',[
        'as'    => 'conference.edit',
        'uses'  => 'ConferenceController@edit'
    ]);
    Route::get('conference/delete/{id}',[
        'as'    => 'conference.delete',
        'uses'  => 'ConferenceController@delete'
    ]);
    Route::get('conference/action/{id}',[
        'as'    => 'conference.action',
        'uses'  => 'ConferenceController@action'
    ]); 
    Route::any('news/commentverify/{id}',[
        'as'    => 'news.newscommentverify',
        'uses'  => 'NewsController@newscommentverify'
    ]);
    Route::get('news/commentaction/{id}',[
        'as'    => 'news.newscommentaction',
        'uses'  => 'NewsController@newscommentaction'
    ]);
    Route::get('news/newscommentdelete/{id}',[
        'as'    => 'news.newscommentdelete',
        'uses'  => 'NewsController@newscommentdelete'
    ]);    
    Route::post('news/updateimagetitle','NewsController@updateimagetitle');
    Route::post('news/updateimageposition','NewsController@updateimageposition');
    Route::post('news/updatevideotitle','NewsController@updatevideotitle');
    Route::post('news/updatevideoposition','NewsController@updatevideoposition');
    
    Route::get('videoconferenceusers','ConferenceController@videoconferenceusers');
    Route::post('videoenable','ConferenceController@videoenable');
    Route::post('videoblack','ConferenceController@videoblack');
    Route::post('videoscreen','ConferenceController@videoscreen');
    Route::post('videomainscreen','ConferenceController@videomainscreen'); 
    Route::post('removeconferenceuser','ConferenceController@removeconferenceuser'); 
    /*
     *breaking news details
     */
    Route::get('breakingnews/list','NewsController@breakingnewslist');
    Route::get('breakingnews/create','NewsController@breakingnewscreate');
    Route::post('breakingnews/store','NewsController@breakingnewstore');
    Route::post('breakingnews/breakingnewsChangePosition','NewsController@breakingnewsChangePosition');
    Route::get('breakingnews/edit/{id}',[
        'as'    => 'news.breakingnewsedit',
        'uses'  => 'NewsController@breakingnewsedit'
    ]);
    Route::get('breakingnews/delete/{id}',[
        'as'    => 'news.breakingnewsdelete',
        'uses'  => 'NewsController@breakingnewsdelete'
    ]);
    Route::post('news/breakingnewsaction','NewsController@breakingnewsaction'); 

    Route::get('breakingnews/addtobreakingnews/{id}',[
        'as'    => 'news.addtobreakingnews',
        'uses'  => 'NewsController@addtobreakingnews'
    ]);    
    Route::get('footer','UsersController@manageFooter'); 
    Route::post('footerstore','UsersController@footerstore');  

    Route::post('news/removefeaturedimage','NewsController@removefeaturedimage');     
    Route::post('news/removeOtherVideos','NewsController@removeOtherVideos');    
    Route::post('news/removeOtherImages','NewsController@removeOtherImages');
    Route::any('pages/feedbacklist','PageController@feedbacklist');
    Route::get('pages/feedbackDelete/{id}',[
        'as' => 'pages.feedbackDelete',
        'uses' => 'PageController@feedbackDelete'
    ]);    
});

Route::post('news/sent-mail','NewsController@sentMail');
Route::get('news/top-videos','NewsController@topvideos');
Route::get('news/viral-videos','NewsController@viralvideos');
Route::get('citizen/news','CitizenJournalistController@news');
Route::post('citizenews/newstore','CitizenJournalistController@newstore');
Route::get('citizenews/{slug}','CitizenJournalistController@citizenNews');
Route::get('news/top-news-now', 'NewsController@topNewsNow');
Route::get('conference/live/{slug}',[
    'as'    => 'conference',
    'uses'  => 'ConferenceController@conference'
]);
Route::get('advertisement/showadd/{id}','AdvertisementController@showadd');

Route::any('news/nextTopNews','NewsController@nextTopNews');
/*
 *Application
 */
Route::any('users/forgotpassword','UsersController@forgotpassword');


Route::any('pages/feedback','PageController@feedback');
Route::post('pages/feedbackStore','PageController@feedbackStore');
Route::any('pages/sitemap','PageController@sitemap');
Route::any('pages/{slug}','PageController@pages');

Route::post('news/commentStore','NewsController@commentStore');

Route::post('news/commentFileUpload','NewsController@commentFileUpload');
Route::post('news/commentRemovefile','NewsController@commentRemovefile');
Route::get('category/{slug}','NewsController@category');
Route::any('news/commentlist','NewsController@newscommentlist');


//Route::get('conference','ConferenceController@conference');
Route::post('conference/showAnnouncement','ConferenceController@showAnnouncement'); 
Route::post('saveuserstreamurl','ConferenceController@saveuserstreamurl');
Route::post('liveconferenceuser','ConferenceController@liveconferenceuser');

/*
 *forget password and change password
 */
Route::any('users/forgotpassword','UsersController@forgotpassword');
Route::post('forgotpassword/store','UsersController@forgotpasswordstore');
Route::any('users/changepassword/{id}','UsersController@changepassword');
Route::post('changepassword/store','UsersController@changepasswordstore');
/*
 *Routes for poll
 */
Route::post('users/storepoll','UsersController@storepoll');
/*
 *Routes for social sites
 */
Route::get('google/{redirecturl}', 'SocialsiteController@google_redirect');
Route::get('social/google', 'SocialsiteController@google');

Route::get('facebook/{redirecturl}', 'SocialsiteController@facebook_redirect');
Route::get('social/facebook', 'SocialsiteController@facebook');

//https://apps.twitter.com/app/new
Route::get('twitter/{redirecturl}', 'SocialsiteController@twitter_redirect');
Route::get('social/twitter', 'SocialsiteController@twitter');

Route::get('social/logout/{redirecturl}', 'SocialsiteController@logout');

Route::get('/social', function()
{
    return Share::load('http://ommcomnews.com', 'Link description')->services();
});


Route::get('/{slug}', 'NewsController@newsDetails');


Route::any('api/v0.1/postsHome', 'WebsController@postsHome');


Route::any('api/v0.1/postsHome/featuredNews', 'WebsController@featuredNews');
Route::any('api/v0.1/postsHome/conferenceNews', 'WebsController@conferenceNews');
Route::any('api/v0.1/postsHome/citizenCustomize', 'WebsController@citizenCustomize');
Route::any('api/v0.1/postsHome/categoryNews', 'WebsController@categoryNews');
Route::any('api/v0.1/postsHome/advertisementPost', 'WebsController@advertisementPost');
Route::any('api/v0.1/postsHome/topViralVideos', 'WebsController@topViralVideos');
Route::any('api/v0.1/breakingNews', 'WebsController@breakingNews');
Route::any('api/v0.1/versionDetails/{ostype}', 'WebsController@versionDetails');
Route::any('api/v0.1/deviceRegister', 'WebsController@deviceRegister');
Route::any('api/v0.1/categories', 'WebsController@categories');
Route::any('api/v0.1/subCategories/{id}', 'WebsController@subCategories');
Route::any('api/v0.1/posts/topNewsList', 'WebsController@topNewsNow');
Route::any('api/v0.1/posts/nextTopNews', 'WebsController@nextTopNews');
Route::any('api/v0.1/posts/odishaPlus', 'WebsController@odishaPlus');
Route::any('api/v0.1/posts/slug/{slug}', 'WebsController@newsDetails');


Route::any('api/v0.1/citizen/latest', 'WebsController@citizenLatest');
Route::any('api/v0.1/citizen/mostviewed', 'WebsController@mostviewed');
Route::any('api/v0.1/citizen/citizenAdvPopup', 'WebsController@citizenAdvPopup');
Route::any('api/v0.1/citizen/{slug}', 'WebsController@citizenNewsDetails');
Route::any('api/v0.1/poll/question', 'WebsController@pollQuestion');
Route::any('api/v0.1/poll/save', 'WebsController@pollSave');
Route::any('api/v0.1/postComment', 'WebsController@postComment');
Route::any('api/v0.1/postCitizenNews', 'WebsController@postCitizenNews');
Route::any('api/v0.1/conference/mobjoin', 'WebsController@conferenceWebJoin');
Route::any('api/v0.1/conference/conference_details/{id}', 'WebsController@conference_details');
Route::any('api/v0.1/conference/showAnnouncement/{id}', 'WebsController@showAnnouncement');
Route::any('api/v0.1/conference/conference_status/{id}', 'WebsController@conferenceStatus');
Route::any('api/v0.1/topVideos', 'WebsController@topVideos');
Route::any('api/v0.1/viralVideos', 'WebsController@viralVideos');
Route::any('api/v0.1/postUserDetails', 'WebsController@postUserDetails');
Route::any('api/v0.1/feedback', 'WebsController@postFeedback');

Route::any('api/v0.1/posts/{slug}', 'WebsController@category');
Route::any('api/v0.1/posts/nextCategory/{slug}', 'WebsController@nextCategory');
Route::any('api/v0.1/posts/searchNewsList/{slug}', 'WebsController@searchNewsList');

Route::get('conference/testconference', 'ConferenceController@testconference');