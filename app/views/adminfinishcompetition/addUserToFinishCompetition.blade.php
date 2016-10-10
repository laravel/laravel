@extends('layouts.adIndex')
@section('title') 添加参赛用户 
@stop 
@section('search') 
@include('adminfinishcompetition.search') 
@stop 
@section('crumbs') 
添加参赛用户 
@stop 
@section('content')
<table class="table table-hover table-bordered ">
  <tr>
    <th>用户id<i class="icon-search"></i></th>
    <th>用户昵称</th>
    <th>真实姓名</th>
    <th>性别</th>
    <th>用户平台</th>
    <th>比赛项目</th>
    <th>参赛时间</th>
    <th>设置参赛资格</th>
  </tr>
  @if($user_match!="") @foreach($rs as $k=>$v) @if(empty($user_match[$v['uid']]['nick_name']))
  <?php continue;?>
    @endif
    <tr>
      <td>{{$v['uid']}}</td>
      <td>
        @if(!empty($user_match[$v['uid']]['nick_name'])) {{$user_match[$v['uid']]['nick_name']}} @endif
      </td>
      <td>
        @if(!empty($user_match[$v['uid']]['name'])) {{$user_match[$v['uid']]['name']}} @endif
      </td>
      <td>
        @if(!empty($user_match[$v['uid']]['gender'])) {{$user_match[$v['uid']]['gender']}} @endif
      </td>
      <td>
        @if(!empty($plat_from[$v['uid']])) {{$plat_from[$v['uid']]}} @endif
      </td>
      <td>
        @if(!empty($user_match[$v['uid']]['competition_name'])) {{$user_match[$v['uid']]['competition_name']}} @endif
      </td>
      <td>
        @if(!empty($user_match[$v['uid']]['addtime'])) {{date('Y-m-d',$user_match[$v['uid']]['addtime'])}} @endif
      </td>
      <td>
        @if(empty($user_match[$v['uid']]['permission']))
        <button class="operator btn btn-mini btn-success" type="button" data-flag=1 competition-id="{{$competitionid}}" value="{{$v['uid']}}">添加资格</button>
        @else
        <button class="operator btn btn-mini btn-danger" type="button" data-flag=0 competition-id="{{$competitionid}}" value="{{$v['uid']}}">取消资格</button>
        @endif
      </td>
    </tr>
    <tr>
      <td colspan=7>
        @if(!empty($user_match[$v['uid']]['card'])) 身份证号:{{$user_match[$v['uid']]['card']}} @endif @if(!empty($user_match[$v['uid']]['age'])) 年龄: {{$user_match[$v['uid']]['age']}} @endif @if(!empty($user_match[$v['uid']]['provice_name'])) 省份：{{$user_match[$v['uid']]['provice_name']
        }} @endif @if(!empty($user_match[$v['uid']]['city_name'])) 城市：{{$user_match[$v['uid']]['city_name']}} @endif @if(!empty($user_match[$v['uid']]['area_name'])) 县区：{{$user_match[$v['uid']]['area_name']}} @endif @if(!empty($user_match[$v['uid']]['company']))
        单位名称：{{$user_match[$v['uid']]['company']}} @endif @if(!empty($user_match[$v['uid']]['address'])) 地址： {{$user_match[$v['uid']]['address']}} @endif @if(!empty($user_match[$v['uid']]['zip'])) 邮编：{{$user_match[$v['uid']]['zip']}} @endif @if(!empty($user_match[$v['uid']]['email']))
        邮箱：{{$user_match[$v['uid']]['email']}} @endif
        <br/> @if(!empty($user_match[$v['uid']]['note'])) 组合类型:{{$user_match[$v['uid']]['note']}} @endif
      </td>
      </td>
    </tr>
    @endforeach @endif
</table>

{{$rs->appends($search)->links();}}

<ul class="pagination">
  <li><a href="javascript:;">总共 <em style="color:red;">{{$rs->appends($search)->getTotal();}}</em> 条记录</a></li>
</ul>
<script type="text/javascript">
  $('.operator').bind('click', function(data) {
    var competitionid = $(this).attr('competition-id');
    var uid = $(this).val();
    var flag = $(this).attr('data-flag');
    var a = confirm("确定进行该操作吗？");
    if (a == true) {
      $.post("/admin/modifyComAuth", {
        competitionid: competitionid,
        uid: uid,
        flag: flag
      }, function(data) {
        if (data == 1) {
          window.location.reload();
        } else {
          window.alert(data);
        }
      });
    }
  });
</script>
@stop