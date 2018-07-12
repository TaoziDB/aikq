<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8" />
    <meta content="telephone=no,email=no" name="format-detection" />
    <meta name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=0.5, minimum-scale=0.5, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/style_phone.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/roomPhone.css">
    <link rel="Shortcut Icon" data-ng-href="img/ico.ico" href="{{env('CDN_URL')}}/img/mobile/ico.ico">
    <link href="{{env('CDN_URL')}}/img/mobile/icon_face.png" sizes="100x100" rel="apple-touch-icon-precomposed">
    <title>料狗</title>
</head>
<body>
<div id="Navigation">
    <div class="banner">
        {{$match['hname']}} VS {{$match['aname']}}
    </div>
</div>
<div id="Video">
    <p>主播正在客户端直播~~</p>
    <a href="/m/downloadPhone.html">点击下载app观看</a>
</div>
<div id="Chat">

</div>
</body>
<script src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/socket.io/2.1.1/socket.io.js"></script>
<script type="text/javascript">
    function encodeUTF8(s){
        var i,r=[],c,x;
        for(i=0;i<s.length;i++)
            if((c=s.charCodeAt(i))<0x80)r.push(c);
            else if(c<0x800)r.push(0xC0+(c>>6&0x1F),0x80+(c&0x3F));
            else {
                if((x=c^0xD800)>>10==0) //对四字节UTF-16转换为Unicode
                    c=(x<<10)+(s.charCodeAt(++i)^0xDC00)+0x10000,
                            r.push(0xF0+(c>>18&0x7),0x80+(c>>12&0x3F));
                else r.push(0xE0+(c>>12&0xF));
                r.push(0x80+(c>>6&0x3F),0x80+(c&0x3F));
            };
        return r;
    };

    function md5(data){
        /**************************************************
         Author：次碳酸钴（admin@web-tinker.com）
         Input：Uint8Array
         Output：Uint8Array
         **************************************************/
        var i,j,k;
        var tis=[],abs=Math.abs,sin=Math.sin;
        for(i=1;i<=64;i++)tis.push(0x100000000*abs(sin(i))|0);
        var l=((data.length+8)>>>6<<4)+15,s=new Uint8Array(l<<2);
        s.set(new Uint8Array(data.buffer)),s=new Uint32Array(s.buffer);
        s[data.length>>2]|=0x80<<(data.length<<3&31);
        s[l-1]=data.length<<3;
        var params=[
            [function(a,b,c,d,x,s,t){
                return C(b&c|~b&d,a,b,x,s,t);
            },0,1,7,12,17,22],[function(a,b,c,d,x,s,t){
                return C(b&d|c&~d,a,b,x,s,t);
            },1,5,5,9,14,20],[function(a,b,c,d,x,s,t){
                return C(b^c^d,a,b,x,s,t);
            },5,3,4,11,16,23],[function(a,b,c,d,x,s,t){
                return C(c^(b|~d),a,b,x,s,t);
            },0,7,6,10,15,21]
        ],C=function(q,a,b,x,s,t){
            return a=a+q+(x|0)+t,(a<<s|a>>>(32-s))+b|0;
        },m=[1732584193,-271733879],o;
        m.push(~m[0],~m[1]);
        for(i=0;i<s.length;i+=16){
            o=m.slice(0);
            for(k=0,j=0;j<64;j++)m[k&3]=params[j>>4][0](
                    m[k&3],m[++k&3],m[++k&3],m[++k&3],
                    s[i+(params[j>>4][1]+params[j>>4][2]*j)%16],
                    params[j>>4][3+j%4],tis[j]
            );
            for(j=0;j<4;j++)m[j]=m[j]+o[j]|0;
        };
        return new Uint8Array(new Uint32Array(m).buffer);
    };

    //    var socket = io.connect('http://bj.xijiazhibo.cc');
    //var socket = io.connect('http://localhost:6001');
    var socket = io.connect('http://ws.aikq.cc');
    socket.on('connect', function (data) {
        var mid = '{{$room_id}}';
        var time = Date.parse( new Date())/1000 + '';
        var key = mid + '?' + time.substring(time.length - 1) + '_' + time.substring(time.length - 2);
        var key = new Uint8Array(encodeUTF8(key));
        var result = md5(key);
        var in_string = Array.prototype.map.call(result,function(e){
            return (e<16?"0":"")+e.toString(16);
        }).join("");
        var req = {
            'mid':mid,
            'time':time,
            'verification':in_string,
        }
        socket.emit('user_mid', req);
    });

    socket.on('server_send_message', function (data) {
        console.log(data);
        $('#Chat').append('<p class="ev"><span>'+data['nickname']+'：</span>'+data['message']+'</p>');
    });
</script>
</html>
