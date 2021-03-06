<?php
    $cdn = env('CDN_URL');
    $cur = empty($cur) ? 'live' : $cur;
    if ($cur == "live") {
        $liveIco = $cdn . "/img/mobile/commom_icon_live_s.png";
        $liveUrl = '';
        $liveClass = 'on';
    } else {
        $liveIco = $cdn . "/img/mobile/commom_icon_live_n.png";
        $liveUrl = 'href=/';
        $liveClass = '';
    }

    if ($cur == "anchor") {
        $anchorIco = $cdn . "/img/mobile/commom_icon_anchor_s.png";
        $anchorUrl = '';
        $anchorClass = 'on';
    } else {
        $anchorIco = $cdn . "/img/mobile/commom_icon_anchor_n.png";
        $anchorUrl = 'href=/anchor/';
        $anchorClass = '';
    }

    if ($cur == "news") {
        $newsIco = $cdn . "/img/mobile/icon_news_s.png";
        $newsUrl = '';
        $newsClass = 'on';
    } else {
        $newsIco = $cdn . "/img/mobile/icon_news_n.png";
        $newsUrl = 'href=/news/';
        $newsClass = '';
    }
?>
<dl id="Bottom">
    <dd class="{{$liveClass}}">
        <a {{$liveUrl}}>
            <img src="{{$liveIco}}">
            <p>直播</p>
        </a>
    </dd>
    {{--<dd class="{{$anchorClass}}">--}}
        {{--<a {{$anchorUrl}}>--}}
            {{--<img src="{{$anchorIco}}">--}}
            {{--<p>主播</p>--}}
        {{--</a>--}}
    {{--</dd>--}}
    <dd class="{{$newsClass}}">
        <a {{$newsUrl}}>
            <img src="{{$newsIco}}">
            <p>资讯</p>
        </a>
    </dd>
    {{--<dd>--}}
        {{--<a href="https://shop.liaogou168.com">--}}
            {{--<img src="{{env('CDN_URL')}}/img/mobile/commom_icon_recommend_n.png">--}}
            {{--<p>推荐</p>--}}
        {{--</a>--}}
    {{--</dd>--}}
</dl>