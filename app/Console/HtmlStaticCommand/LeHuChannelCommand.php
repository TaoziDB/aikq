<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/11/11
 * Time: 10:10
 */

namespace App\Console\HtmlStaticCommand;


use App\Console\LiveDetailCommand;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PC\Live\LiveController;
use App\Models\Match\MatchLiveChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class LeHuChannelCommand extends BaseCommand
{
    protected function command_name()
    {
        return "lh_channel";
    }

    protected function description()
    {
        return "其他播放器";
    }

    protected function onMobileHandler(Request $request)
    {
        $cache = Storage::get('/public/static/json/m/lives.json');
        $json = json_decode($cache, true);
        $this->flushChannel($json);
    }

    protected function onPcHandler(Request $request)
    {
        $cache = Storage::get('/public/static/json/pc/lives.json');
        $json = json_decode($cache, true);
        $this->flushChannel($json);
    }

    protected function flushChannel($json) {
        if (is_null($json) || !isset($json['matches'])) {
            echo '获取数据失败';
            return;
        }
        $json = $json['matches'];
        foreach ($json as $index=>$datas){
            foreach ($datas as $match){
                $time = isset($match['time']) ? $match['time'] : 0;
                $now = time();
                if ($time == 0 ) {//无比赛时间跳过
                    continue;
                }
                $start_time = strtotime($time);//比赛时间
                $flg_1 = $start_time >= $now && $now + 35 * 60 >= $start_time;//开赛前35分钟
                $flg_2 = false;//$start_time <= $now && $start_time + 3 * 60 * 60  >= $now;//开赛后3小时 开赛后编辑修改，会即时更新。
                if ( $flg_1 || $flg_2 ) {
                    try {
                        $channels = $match['channels'];

                        //dump($channels);
                        //continue;
                        foreach ($channels as $channel) {
                            $player = isset($channel['player']) ? $channel['player'] : 1;
                            if ($player == MatchLiveChannel::kPlayerExLink) continue;
                            $id = isset($channel['id']) ? $channel['id'] : '';
                            if (empty($id)) continue;
                            $link = isset($channel['link']) ? $channel['link'] : "";
                            if (!preg_match("/ws.live.sjmhw.com/", $link)) {
                                continue;
                            }

                            $matchLiveChannel = MatchLiveChannel::query()->find($id);
                            if (!isset($matchLiveChannel) || empty($matchLiveChannel->room_num)) continue;
                            $json = self::getLeHuLink($matchLiveChannel->room_num);
                            if (is_null($json)) continue;
                            $player = $matchLiveChannel->player;
                            if ($player == MatchLiveChannel::kPlayerM3u8 && isset($json['m3u8'])) {
                                $matchLiveChannel->content = $json['m3u8'];
                                $matchLiveChannel->save();
                            } else if ($player == MatchLiveChannel::kPlayerFlv && isset($json['hls'])) {
                                $matchLiveChannel->content = $json['hls'];
                                $matchLiveChannel->save();
                            }
                        }
                    } catch (\Exception $exception) {
                        dump($exception);
                    }
                }
            }
        }
    }

    public static function getLeHuLink($room_num) {
        $key = "LeHuChannelCommand_" . $room_num;
        $infoStr = Redis::get($key);
        $info = json_decode($infoStr, true);
        if (is_null($info)) {
            $url = "https://www.lehuzhibo.com/lua-cgi/line-info?id=".$room_num;
            $out = Controller::execUrl($url, 1, true);
            $json = json_decode($out, true);
            if (!isset($json) || !isset($json['hls']) || !isset($json['m3u8'])) {
                //dump("获取观看地址失败");
                return null;
            }
            $info = $json;
            Redis::setEx($key, 60 * 5, json_encode($json) );
        }
//        dump($room_num . " = " . json_encode($info) );
        return $info;
    }

}