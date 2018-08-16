<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(["namespace" => 'Live'], function () {
    //Route::get('/',"LiveController@lives");
});

//世界杯专题
Route::group(["namespace" => 'FIFA'], function () {
    Route::get("/worldcup/2018/index.html", "WorldCupController@index");
    Route::get("/worldcup/2018/rank.html", "WorldCupController@rank");
    Route::get("/worldcup/2018/team_index.html", "WorldCupController@teamIndex");
    Route::get("/worldcup/2018/team/{tid}.html", "WorldCupController@teamDetail");
    Route::get("/worldcup/2018/topic/index.html", "WorldCupController@topicList");

    //静态化,只有球队列表才在这里做一次,没有必要定时任务,一次就够了
    Route::get("/static/worldcup/team_index","WorldCupController@staticTeamIndex");
    Route::get("/static/worldcup/rank","WorldCupController@staticRank");
    Route::get("/static/worldcup/index","WorldCupController@staticIndex");
    Route::get("/static/worldcup/team/{tid}","WorldCupController@staticTeamDetail");
    Route::get("/static/worldcup/topic","WorldCupController@staticTopicList");
});

/**
 * 直播入口
 */
Route::group(["namespace" => 'Live'], function () {
    Route::any("/", function (){
        return redirect('/m/lives.html');
    });
    Route::any("/football.html", function (){
        return redirect('/m/lives.html');
    });
    Route::any("/basketball.html", function (){
        return redirect('/m/lives.html');
    });
    Route::any("/other.html", function (){
        return redirect('/m/lives.html');
    });
    Route::get("/lives.html", "LiveController@lives");//直播列表
    //Route::get("/football.html", "LiveController@footballLives");//直播列表
    //Route::get("/basketball.html", "LiveController@basketballLives");//直播列表
    //Route::get("/other.html", "LiveController@otherLives");//自建赛事 直播列表

    Route::get("/live/football/{id}.html", "LiveController@footballdetail");//直播终端
    Route::get("/live/basketball/{id}.html", "LiveController@basketballDetail");//直播终端
    Route::get("/live/other/{id}.html", "LiveController@otherDetail");//直播终端

    Route::get("/live/subject/videos/{type}/{page}.html", 'LiveController@subjectVideos');//录像列表
    Route::get("/live/subject/video/{first}/{second}/{vid}.html", 'LiveController@subjectVideoDetail');//录像终端

//    Route::get('/lives/player.html', function () {return view('mobile.live.player');});
//    Route::get("/lives/roll/{time}/{id}.html", "LiveController@roll");//直播滚球
//    Route::get("/lives/data/{time}/{id}.html", "LiveController@match_data");//直播数据
//    Route::get("/lives/tip/{time}/{id}.html", "LiveController@matchTip");//直播提点数据
    Route::get("/lives/data/refresh.json", "LiveController@match_live");//比赛比分数据

//    Route::get("/live/football/recommend/{mid}", "LiveController@getArticleOfFMid");//直播终端足球推荐
//    Route::get("/live/basketball/recommend/{mid}", "LiveController@getArticleOfBMid");//直播终端篮球推荐

//    //天天直播
//    Route::get("/match/live/url/zb/{id}", 'LiveController@getTTZBLiveUrl');
//    //无插件
//    Route::get("/match/live/url/wcj/{id}", 'LiveController@getWCJLiveUrl');
//    //channel通用
//    Route::get("/match/live/url/channel/{id}.json", 'LiveController@getLiveUrl');
});

//主播
Route::group(["namespace" => 'Anchor'], function () {
    Route::get("/anchor/index.html", "AnchorController@index");//比赛比分数据
    Route::get("/anchor/room/{room_id}.html", "AnchorController@room");//比赛比分数据
});

/**
 * 文章列表、终端
 */
Route::group(["namespace" => 'Article'], function () {
    //Route::get("/news/index.html", "ArticleController@articles");//文章列表
    //Route::get("/news/{type}/{date}/{id}.html", "ArticleController@detail");//比赛比分数据
});
