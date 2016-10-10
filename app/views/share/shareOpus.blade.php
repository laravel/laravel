<!DOCTYPE html>
<html>
    
    <head>
        <meta charset="utf-8">
        <title>
            为你读诗
        </title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="apple-mobile-web-app-capable" content="yes">

  <meta name="viewport" content="width=device-width,maximum-scale=1, user-scalable=no, minimal-ui">
 
    <style>
        *{ padding:0; margin:0;}
        body{ background-color:#FFF; font-size:0.75em; text-align:center;}
        
        a{ text-decoration:none; border:0px;color:black;}
        .contentUl{list-style-type:none;}
        .contentUl li{ float:left;}
        .contentUl p{ margin:2% 0;}
        .textB{ font-weight:bold; font-size:1.25em;}
        .textC{font-size:1.25em; color:#6E6E6E;}
        .clear{ clear:both;}
        .colorC{ color:#6E6E6E; font-size:1.25em; }
        .textClech{font-size:1.5em;}
        .block{ display:block;}
        .none{ display:none;}
        .heightDiv{ height:20px; display:block;}
    </style>
    <script src="http://cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>
    <script src="http://g.tbcdn.cn/kissy/k/1.4.1/seed-min.js" data-config="{combine:true}"></script>
    </head>

<body>
    <a id = "downringtone" href="{{$list['downurl']}}" target="_blank">
        <div style="margin:1% 3% 0 3%; padding:2% 0; background-color:#F8F8F8; height:50%;">
            <ul class="contentUl">
                <li style="width:20%;"><img src="../../img/icon-72.png" style="margin-top:2px" alt="" width="80%"/></li>
                <li style="text-align:left; width:50%; margin-left:2%;">
                    <!--<h4 class="textClech">为你读诗</h4>
                    <h4 class="colorC">有诗歌·有远方</h4>-->
                    <div style="padding-top:2%;">
                        <p class="textClech">为你读诗</p>
                    </div>
                    <p class="colorC">配乐诵读录音棚</p>
                </li>
                <li style="width:25%; margin-top:3%; margin-right:3%;">
                    <img src="../../img/up_download.png" style="margin-top:5px" alt="" width="100%"/>
                </li>
            </ul>
            <div class="clear"></div>
        </div>
    </a>
    
    <div class="heightDiv">&nbsp;</div>
    
    <div style="text-align:center;">
        <img src="../../img/baby.png" alt="" width="100%"/>
        <!-- <h2 style="font-size:1.5em;color:gray">配乐诵读像K歌一样简单</h2> -->
        <!-- <h2 style="font-size:1.5em;color:gray">读好诗·读美文·读精彩·读人生</h2> -->
    </div>
    
    <!-- <div class="heightDiv">&nbsp;</div> -->
    
    <div style=" border-top:1px solid  #CCC; margin:0px 3%; padding-top:2%; position:relative;">
        <ul class="contentUl">
            <li style="margin-top:5px;">
                <img style="width:80px;height:80px" src="{{$list['sportrait']}}" alt="" width="79%"/>
            </li>
            @if($list['isleague'] == 1)
                <img style="width:23px;height:23px;float:left;margin-left:-23px;margin-top:62px" src="../../img/isleague.png"/>
            @elseif($list['authtype'] == 1)
                <img style="width:23px;height:23px;float:left;margin-left:-23px;margin-top:62px" src="../../img/v.png"/>
            @elseif($list['teenager'] == 1)
                <img style="width:23px;height:23px;float:left;margin-left:-23px;margin-top:62px" src="../../img/teenager.png"/>
            @endif
            <li style="text-align:left; margin-left:15px;">
                <p class="textB">
                @if(mb_strlen($list['name']) >=10)
                    {{mb_substr($list['name'],0,10)}}...
                @else
                    {{$list['name']}}
                @endif
                </p>
                <p class="textC">
                {{$list['nick']}}&nbsp;
                    <!--性别图片-->
                    @if($list['gender'] == 0)
                        <img style="margin-left:-3px" src="../../img/nv.png" />
                    @elseif($list['gender'] == 1)
                        <img style="margin-left:-3px" src="../../img/nan.png" />
                    @endif
                    <img src="../../img/{{$list['grade']}}.png" alt="" width="17%"/> 
                    <!--等级图片-->
               
                </p>
                <p style="margin:3% 0 1% 0;">
                    <img src="../../img/time.png" alt="" width="15" height="15" style="vertical-align:middle; padding-bottom:4px;"/>
                    <span id="text-time">{{$list['realtime']}}</span>
                </p>
                <p style="margin:0px 0px;">
                    <img src="../../img/erji.png" alt="" width="17" height="14"/>
                    <span id="text-hear">{{$list['lnum']}}</span>&nbsp;&nbsp;&nbsp;
                    <img src="../../img/good.png" alt="" width="15" height="15" />
                    <span id="text-good">{{$list['praisenum']}}</span>&nbsp;&nbsp;&nbsp;&nbsp;
                    <img src="../../img/transmit.png" alt="" width="15" height="15"/>
                    <span id="text-transmit">{{$list['repostnum']}} </span>
                </p>
            </li>
        </ul>
        
        
        <div style="position:absolute; right:15px; top:23px; z-index:1;">
            <img src="../../img/play.png" alt="" width="35" height="35" id="play-button" class="block"/>
            <img src="../../img/stop.png" alt="" width="35" height="35" id="stop-button" class="none"/>
            <input type="hidden" name="opusUrl" id="opusUrl" value='{{$list['url']}}' /> 
        </div> 
        <div class="clear"></div> 
    </div>
    
    
    <div style="border-top:1px solid #CCC; width:100%; padding:1% 0; background-color:#F8F8F8; position:fixed; bottom:0; left:0;z-index:99">
    
        <ul class="contentUl">
            <li style="text-align:center; width:50%; margin-top:3%;">
                <img src="../../img/logo.png" alt="" style="margin-top:4px" width="80%"/>
            </li>
            <li style="text-align:center; width:50%;">
                <a id="downringtone2" href="{{$list['downurl']}}" target="_blank">
                <img src="../../img/bottom_download.png" alt="" style="margin-top:4px" width="80%"/>
                </a>
            </li>
        </ul>
         <div class="clear"></div>
         <div id="slowdownid" style="width:95%;position:fixed;height:220px;top:0px;z-index:1000;left:8px;display:none">
            <img src= "" id = "myimageid" height="120px" />
            <input type = "hidden" id="flag" value="{{$list['flag']}}" />
        </div>
    </div>
    
    <script type="text/javascript">
        
        var height = $(window).height();
        if(height < 480){
            //$(window).height("680px");
            $("body").css("marginBottom","80px");
        }
        KISSY.use('node,dom,event,gallery/slide/1.2/index,anim,gallery/simple-mask/1.0/,io,cookie,gallery/musicPlayer/2.0/index',function(S,Node,DOM,Event,Slide,Anim,Mask,IO,Cookie,MusicPlayer){

            var $=Node.all;
            var opusUrl = $('#opusUrl').val();
        //首页
          var musicPlayer = new MusicPlayer({
              type:'auto',
                  mode:'random',
                  volume:1,
                   // auto:'false', //自动播放 默认不播放.
                 //   mode:'order', //如果几首歌想随机播放,设置为 random, 默认为order.

                    musicList:[{"name":"背景音乐", "path":opusUrl}]
                });
             // musicPlayer.stop();
             // musicPlayer.play();
            $('#play-button').on("click",function(){
                $('#stop-button').css("display","block");
                $('#play-button').css("display","none");
                musicPlayer.play();
            });
            $('#stop-button').on("click",function(){
                $('#play-button').css("display","block");
                $('#stop-button').css("display","none");
                musicPlayer.stop();
            });
        });

        //判断是什么设备
          var deviceid = document.getElementById('flag').value;
          if(deviceid == 1) {
            //说明是ios设备
            document.getElementById('myimageid').src="http://www.weinidushi.com.cn/img/shareios.jpg";
          } else {
            //说明是android设备
            document.getElementById('myimageid').src="http://www.weinidushi.com.cn/img/shareandroid.jpg";
          }
          $('#downringtone').click(function(event) {
            if(is_weixin() == true) {
              //判断是什么设备
              $("#slowdownid").slideDown('2000');
              event.preventDefault();
            } else {
              var url  = document.getElementById('downringtone').href;
              document.getElementById('downringtone').href=url;
            }
          });
            $('#downringtone2').click(function(event) {
                if(is_weixin() == true) {
                  //判断是什么设备
                  $("#slowdownid").slideDown('2000');
                  event.preventDefault();
                } else {
                  var url  = document.getElementById('downringtone2').href;
                  document.getElementById('downringtone2').href=url;
                }
            });

            // is_weixin();
          function is_weixin() {
            var ua = navigator.userAgent.toLowerCase();
            if (ua.match(/MicroMessenger/i) == "micromessenger") {
            return true;
            } else {
              return false;
            }
          } 
    </script>
</body>
</html>
