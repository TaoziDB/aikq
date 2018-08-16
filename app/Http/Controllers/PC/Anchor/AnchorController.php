<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/7/11
 * Time: 下午6:35
 */

namespace App\Http\Controllers\PC\Anchor;

use App\Events\ChatPushNotification;
use App\Models\Anchor\Anchor;
use App\Models\Anchor\AnchorRoom;
use App\Models\Anchor\AnchorRoomTag;
use App\Models\Match\Odd;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class AnchorController extends Controller
{
    public function index(Request $request){
        $result = array();
        $result['hotAnchors'] = Anchor::getHotAnchor();
        $result['livingRooms'] = AnchorRoom::getLivingRooms();
//        $hotMatches = AnchorRoomTag::getHotMatch();
        $tmp = array();
//        foreach ($hotMatches as $hotMatch) {
//            $match = $hotMatch->getMatch();
//            if (isset($match)&& $match['status'] >= 0) {
//                $tmp[] = $hotMatch;
////                dump($match);
//            }
//        }
        $result['title'] = "主播频道_美女主播解说足球直播_爱看球";
        $result['keywords'] = "爱看球,足球直播,美女主播,足球解说";
        $result['description'] = "爱看球主播频道，资深主播为你解说各种体育赛事，包含NBA、英超、西甲、中超、法甲、欧冠等各类热门足球直播，还有美女主播陪你看哦。";
        $result['hotMatches'] = $tmp;
        $result['check'] = 'anchor';
        return view('pc.anchor.index',$result);
    }

    public function room(Request $request,$room_id)
    {
        $result = array();
        $room = AnchorRoom::find($room_id);
        if (isset($room)) {
            $match = $room->getLivingTag();
            $result['match'] = $match;
            $result['room_tag'] = $match['tag'];
        } else{
            $result['match'] = null;
            $result['room_tag'] = null;
        }
        $anchor = $room->anchor;
        $result['check'] = 'anchor';
        $result['room_id'] = $room_id;
        $result['room'] = $room;
        $result['anchor'] = $anchor;
        $result['title'] = $room->title.'_爱看球';
        $result['keywords'] = "爱看球主播,".$anchor->name.',体育直播,资深主播';
        $result['description'] = "爱看球主播频道，资深主播".$anchor->name."正在为你解说体育直播。";
        return view('pc.anchor.room',$result);
    }

    public function player(Request $request,$room_id){
        $result = array();
        $result['cdn'] = env('CDN_URL');
        $result['room_id'] = $room_id;
        return view('pc.anchor.player',$result);
    }

    public function playerUrl(Request $request,$room_id){
        $room = AnchorRoom::find($room_id);
        $url = (isset($room->live_rtmp)&&strlen($room->live_rtmp) > 0)?$room->live_rtmp:$room->live_flv;
        if (isset($room)) {
            $match = $room->getLivingTag();
            return response()->json(array('code' => 0, 'match'=>$match, 'status' => $room->status, 'title' => $room->title, 'live_url' => $url));
        } else{
            return response()->json(array('code'=>-1,'live_url'=>''));
        }
    }

    /*** app 接口 ****/
    public function playerUrlApp(Request $request,$room_id){
        $room = AnchorRoom::find($room_id);
        if (isset($room)) {
            $key = env('APP_DES_KEY');
            $iv = env('APP_DES_IV');
            $url = (isset($room->live_rtmp)&&strlen($room->live_rtmp) > 0)?$room->live_rtmp:$room->live_flv;
            $url = openssl_encrypt($url, "DES", $key, 0, $iv);
            $match = $room->getLivingTag();
            $tag = isset($match) ? $match['tag'] : null;

            $showScore = 0; $h_color = null; $a_color = null;
            if (isset($tag)) {
                $showScore = $tag['show_score'];
                $h_color = $tag['h_color'];
                $a_color = $tag['a_color'];
            }
            return response()->json(array('code' => 0, 'show_score'=>$showScore,
                'status' => $room->status,'match'=>$match,
                'h_color'=>$h_color, 'a_color'=>$a_color,
                'title' => $room->title, 'live_url' => $url));
        }
        else{
            return response()->json(array('code'=>-1,'live_url'=>''));
        }
    }

    public function appV110(Request $request){
        $result = array();
        //热门主播
        $result['hotAnchors'] = Anchor::getHotAnchor();
        $tmp = array();
        foreach ($result['hotAnchors'] as $anchor) {
            $tmp[] = $anchor->appModel();
        }
        $result['hotAnchors'] = $tmp;
        //热门比赛
        $hotMatches = AnchorRoomTag::getHotMatch();
        $tmp = array();
        foreach ($hotMatches as $hotMatch) {
            $match = $hotMatch->appModel();
            if (isset($match) && $match['match']['status'] >= 0)
                $tmp[] = $match;
        }
        $result['hotMatches'] = $tmp;
        //正在直播
        $result['livingRooms'] = AnchorRoom::getLivingRooms();
        $tmp = array();
        foreach ($result['livingRooms'] as $livingRoom) {
            $tmp[] = $livingRoom->appModel(true);
        }
        $result['livingRooms'] = $tmp;
        return response()->json(array(
            'code'=>0,
            'data'=>$result
        ));
    }

    public function livingRoom(Request $request){
        //正在直播
        $livingRooms = AnchorRoom::getLivingRooms();
        $tmp = array();
        foreach ($livingRooms as $livingRoom) {
            $model = $livingRoom->appModel(true);
            if ($livingRoom['status'] == AnchorRoom::kLiveStatusLiving){
                $model['statusStr'] = '直播中';
            }
            else{
                $model['statusStr'] = '';
            }
            $model['url'] = '';
            $tmp[] = $model;
        }
        return response()->json(array(
            'code'=>0,
            'data'=>$tmp
        ));
    }

    /**
     * 发送弹幕
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request){
        $data = [
            'message'=>$request->input('message'),
            'nickname'=>$request->input('nickname'),
            'time'=>$request->input('time'),
            'verification'=>$request->input('verification'),
            'mid'=>$request->input('mid'),
        ];
        broadcast(new ChatPushNotification($data));
        return response()->json(['result' => 'ok'], 200);
    }

    /**
     * 静态化
     * @param Request $request
     * @param $room_id
     */
    public function staticRoom(Request $request, $room_id){
        //静态文件
        $json = $this->playerUrl($request, $room_id)->getData();
        $json = json_encode($json);
        if (!empty($json)) {
            Storage::disk('public')->put('static/anchor/room/url/' . $room_id . '.json', $json);
        }

        //直播终端页面
        $html = $this->room(new Request(), $room_id);
        if (!empty($html)) {
            Storage::disk('public')->put('static/anchor/room/' . $room_id . '.html', $html);
        }

        //播放器静态化
        $player = $this->player(new Request(), $room_id);
        if (!empty($player)) {
            Storage::disk('public')->put('static/anchor/room/player/' . $room_id . '.html', $player);
        }
    }
}