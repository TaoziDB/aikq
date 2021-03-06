<?php

namespace App\Http\Controllers\PC;

use App\Http\Controllers\Controller;
use App\Models\PcArticle;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function appConfig(Request $request){
        return response()->json(
            [
                'code'=>0,
                'data'=>[
                    'more'=>'https://shop.liaogou168.com',
                    'icon'=>'http://mp.dlfyb.com/img/pc/image_qr_868.jpg',
                    'weixin'=>'kanqiu8888',
                    'ios_version'=>'1.2.0',
                    'android_version'=>'1.2.0',
                    'update_url'=>'https://www.aikanqiu.com/download/index.html#browser',
                    'anchor'=>1,
                ]
            ]
        );
    }

    public function appConfigV110(Request $request){
        $array = $this->appConfivV110p();
        return response()->json($array);
    }

    public function appConfigV120(Request $request){
        $array = $this->appConfivV120p();
        return response()->json($array);
    }

    public function appConfivV110p(){
        return array(
            'code'=>0,
            'data'=>[
                'more'=>'https://shop.liaogou168.com/',
                'host'=>'http://cms.aikanqiu.com',
                'ws_host'=>'http://ws.aikanqiu.com',
                'icon'=>'https://static.dlfyb.com/img/pc/image_qr_868.jpg',
                'weixin'=>'kanqiu8888',
                'ios_version'=>'1.2.0',
                'android_version'=>'2.0.1',
                'update_url'=>'https://www.aikanqiu.com/download/index.html#browser',
                'android_upgrade_url'=>'https://www.aikanqiu.com/download/index.html',
                'anchor'=>0,
                'showMore'=>'0',
            ]
        );
    }

    public function appConfivV120p(){
        return array(
            'code'=>0,
            'data'=>[
                'more'=>'https://shop.liaogou168.com/',
                'host'=>'http://cms.aikanqiu.com',
                'ws_host'=>'http://ws.aikanqiu.com',
                'icon'=>'https://static.dlfyb.com/img/pc/image_qr_868.jpg',
                'weixin'=>'kanqiu8888',
                'ios_version'=>'1.2.0',
                'android_version'=>'2.0.1',
                'update_url'=>'https://www.aikanqiu.com/download/index.html#browser',
                'android_upgrade_url'=>'https://www.aikanqiu.com/download/index.html',
                'android_install_url'=>'https://static.dlfyb.com/download/android.apk',
                'install_url'=>'itms-services://?action=download-manifest&url=https://static.dlfyb.com/download/test.plist',
                'anchor'=>0,
                'showMore'=>'0',
            ]
        );
    }

    public function appConfivV130p(){
        return array(
            'code'=>0,
            'data'=>[
                'more'=>'https://shop.liaogou168.com/',
                'host'=>'http://cms.aikanqiu.com',
                'ws_host'=>'http://ws.aikanqiu.com',
                'icon'=>'https://static.dlfyb.com/img/pc/image_qr_868.jpg',
                'weixin'=>'kanqiu8888',
                'ios_version'=>'1.2.0',
                'android_version'=>'2.0.1',
                'update_url'=>'https://www.aikanqiu.com/download/index.html#browser',
                'android_upgrade_url'=>'https://www.aikanqiu.com/download/index.html',
                'android_install_url'=>'https://static.dlfyb.com/download/android.apk',
                'install_url'=>'itms-services://?action=download-manifest&url=https://static.dlfyb.com/download/test.plist',
                'anchor'=>0,
                'showMore'=>'0',
                'stream_refers'=>['live.sjmhw.com'=>'https://www.aikanqiu.com'],//添加需要设置refer的播放地址
                'android_system_browser'=>0,//非直播的url跳转方式：0-app，1-系统浏览器
                'android_upgrade_msg'=>'最新版本v2.0.1，更换了播放器内核，看比赛声音更流畅',//非直播的url跳转方式：0-app，1-系统浏览器
            ]
        );
    }

    public function index(Request $request){
       return view('pc.home');
   }

    /**
     * 邀请
     * @param Request $request
     * @param $code
     * @return $this
     */
   public function invitation(Request $request,$code){
       if (self::isMobile($request)){
           return response()->redirectTo('/m')->withCookie(cookie('LIAOGOU_INVITATION_CODE', $code));
       }
       else{
           return response()->redirectTo('/')->withCookie(cookie('LIAOGOU_INVITATION_CODE', $code));
       }
   }
}
