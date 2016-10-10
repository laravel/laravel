@extends('layouts.adIndex')
@section('title')
	赛事报名列表
@stop
@section('crumbs')
	赛事报名列表
@stop
@section('search')
<script src=""></script>	
    <form action="/admin/matchUsersList" method='get'>
		<table  style="width:90%; margin-bottom:20px;">
			<tr>
				<td>UID：</td>
				<td><input name="uid" type='text' id="uid" value="{{$search['uid']}}" class="form-control" style="width:120px;" /></td>
				<td>真实姓名：</td>
                <td><input name="name" type='text' id="name" value="{{$search['name']}}" class="form-control" style="width:120px;" /></td>
				<td>手机号码：</td>
                <td><input name="mobile" type='text' id="mobile" value="{{$search['mobile']}}" class="form-control" style="width:120px;" /></td>
                <td>电子邮箱：</td>
                <td><input name="email" type='text' id="email" value="{{$search['email']}}" class="form-control" style="width:160px;" /></td>
                <td>参赛码：</td>
                <td><input name="invitationcode" type='text' id="invitationcode" value="{{$search['invitationcode']}}" class="form-control" style="width:100px;" /></td>
			</tr>
            <tr>
				
                <td>开始时间：</td>
				<td><input name="sdate" type='text' id="sdate" value="{{$search['sdate']}}" class="form-control"  style="width:120px;" /></td>
				<td>结束时间：</td>
                <td><input name="edate" type='text' id="edate" value="{{$search['edate']}}" class="form-control" style="width:120px;" /></td>
				<td>用户昵称：</td>
                <td><input name="nick_name" type='text' id="nick_name" value="{{$search['nick_name']}}" class="form-control" style="width:120px;" /></td>
                <td>赛事：</td>
				<td>
                <select name="goods_id" id="goods_id" class="form-control" style="width:160px;">
                	<option value="0">所有赛事</option>
					<?php foreach($all_goods as $k=>$v){?>
                    <option value="<?php echo $k;?>" <?php echo $k==$search["goods_id"]?"selected":"";?>><?php echo $v;?></option>
                    <?php }?>
                </select>
                </td>
                <td colspan="2">
               
                </td>
			</tr>
            <tr>
				
                <td>省份：</td>
				<td>
                <select name="province_id" id="province_id" class="form-control" >
                	<option value="">选择</option>
                    <?php foreach($data['allprovince'] as $k=>$v){?>
                	<option value="<?php echo $k;?>" <?php echo $search['province_id']==$k?"selected":"";?>><?php echo $v;?></option>
                    <?php }?>
                </select>
                </td>
				<td>城市：</td>
                <td>
                <select name="city_id" id="city_id" class="form-control" >
                	<option value="">选择</option>
                    <?php
                    if(isset($data['allcity'][$search['province_id']])){
						foreach($data['allcity'][$search['province_id']] as $k=>$v){
							$ck=$k==$search["city_id"]?'selected':'';
							echo '<option value="'.$k.'" '.$ck.'>'.$v.'</option>';
						}
					}
					?>
                </select>
                </td>
				<td>县区：</td>
                <td>
                <select name="area_id" id="area_id" class="form-control" >
                	<option value="">选择</option>
                    <?php
                    if(isset($data['allarea'][$search['city_id']])){
						foreach($data['allarea'][$search['city_id']] as $k=>$v){
							$ck=$k==$search["area_id"]?'selected':'';
							echo '<option value="'.$k.'" '.$ck.'>'.$v.'</option>';
						}
					}
					?>
                </select>
                </td>
	<td>
		16周岁以下
		<input type="checkbox" id="age" name="age" value="1" <?php if(!empty($search['age'])) echo "checked";?>>
	</td>
                <td colspan="2">
                <input type="submit" value="搜索" class="btn btn-mini btn-success" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="/admin/matchUsersListXls" id="btn-dao" target="_blank">导出xls</a>
                </td>
			</tr>
		</table>
	</form>
<script language="javascript">
$("#btn-dao").click(function(){
	var uid=$("#uid").val();
	var name=$("#name").val();
	var mobile=$("#mobile").val();
	var email=$("#email").val();
	var sdate=$("#sdate").val();
	var edate=$("#edate").val();
	var nick_name=$("#nick_name").val();
	var goods_id=$("#goods_id").val();
	var province_id = $('#province_id').val();
	var city_id = $('#city_id').val();
	var area_id = $('#area_id').val();
	var age = <?php echo $search['age'];?>;
	var invitationcode=$("#invitationcode").val();
	
	var url="http://www.weinidushi.com.cn/admin/matchUsersListXls";
	var url_str="?uid="+uid;
	url_str+="&name="+name;
	url_str+="&mobile="+mobile;
	url_str+="&email="+email;
	url_str+="&sdate="+sdate;
	url_str+="&edate="+edate;
	url_str+="&nick_name="+nick_name;
	url_str+="&goods_id="+goods_id;
	url_str+="&invitationcode="+invitationcode;
	url_str+="&province_id="+province_id;
	url_str+="&city_id="+city_id;
	url_str+="&area_id="+area_id;
	url_str+="&age="+age;

	$(this).attr("href",url+url_str);
	return true;
	
});
$("#province_id").change(function(){
	//重置
	$("#city_id").empty();
	$("#area_id").empty();
	
	var _val=$(this).val();
	if(_val!=""){
		$.getJSON("/admin/getCity",{province_id:_val},function(data){
			var html="<option>选择</option>";
			$.each(data, function(i, field){
				html+='<option value="'+field.id+'">'+field.name+'</option>';
			});
			$("#city_id").append(html);
		});
	}
});
$("#city_id").change(function(){
	var _val=$(this).val();
	if(_val!=""){
		$.getJSON("/admin/getArea",{city_id:_val},function(data){
			var html="<option>选择</option>";
			$.each(data, function(i, field){
				html+='<option value="'+field.id+'">'+field.name+'</option>';
			});
			$("#area_id").append(html);
		});
	}
});
</script>  
@stop


@section('content')
 
	<table class="table table-hover table-bordered ">
		<tr>
			<th>ID</th>
			<th>用户ID</th>
			<th>真名<i class="icon-search"></i></th>
			<th width="100px">用户昵称</th>
			<th>性别</th>
            			<th>身份证号</th>
			<th>移动电话</th>
			<th>参加项目</th>
		              <th>邀请码</th>
		              <th>查看作品</th>
			<th>申请时间</th>
			<th>支付时间</th>
            <!--th>查看订单</th-->
			<th>审核状态</th>

		</tr>
		@foreach ($userList as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['uid']}}</td>
			<td>
				@if(!empty($data['user_match_uids'][$item['uid']]['name']))
					{{$data['user_match_uids'][$item['uid']]['name']}}
				@endif
			</td>
			<td style="width:100px">{{$data['user_match_uids'][$item['uid']]['nick_name']}}</td>
		              <td>
		            	@if(isset($data['user_match_uids'][$item['uid']]['gender']) && $data['user_match_uids'][$item['uid']]['gender']  == 1)
							男
						@else
							女
						@endif
		              </td>
			<td>
			@if(!empty($data['user_match_uids'][$item['uid']]['card']))
				{{$data['user_match_uids'][$item['uid']]['card']}}
			@endif
			</td>
			<td>
			@if(!empty($data['user_match_uids'][$item['uid']]['mobile']))
			{{$data['user_match_uids'][$item['uid']]['mobile']}}
			@endif
			</td>
			<td><?php echo isset($all_goods[$item['goods_id']])?$all_goods[$item['goods_id']]:'-'?></td>
			<td>
			@if(!empty($data['user_match_uids'][$item['uid']]['invitationcode']))
				{{$data['user_match_uids'][$item['uid']]['invitationcode']}}
			@endif
			</td>
            <td>
            <?php
            $competitionid=isset($all_competition[$item['goods_id']])?$all_competition[$item['goods_id']]:'';
			?>
            <a href="/admin/matchOpusList?competitionid=<?php echo $competitionid;?>&uid={{$item['uid']}}" target="_blank">查看作品</a>
            </td>
            <td>{{date('Y/m/d H:i',$item['addtime'])}}</td>
            <td>
            @if(!empty($data['user_match_uids'][$item['uid']]['update_time']))
            {{date('Y/m/d H:i',$data['user_match_uids'][$item['uid']]['update_time'])}}
            @endif

            </td>
			<!--td><a href="">看订单</a></td-->
			<td>支付成功</td>
		</tr>
        <tr>
        	<td colspan="13">
        		@if(!empty($data['allprovince'][$data['user_match_uids'][$item['uid']]['province_id']]))
        			省份：{{$data['allprovince'][$data['user_match_uids'][$item['uid']]['province_id']]}}<br/>
        		@endif
        		<?php $province_id = !empty($data['user_match_uids'][$item['uid']]['province_id']) ? $data['user_match_uids'][$item['uid']]['province_id'] : 0;?>
        		<?php $city_id = !empty($data['user_match_uids'][$item['uid']]['city_id']) ? $data['user_match_uids'][$item['uid']]['city_id'] : 0;?>
        		@if(!empty($province_id) && !empty($city_id))
        			城市：{{$data['allcity'][$province_id][$city_id]}}<br/>
        		@endif
        		<?php $area_id = !empty($data['user_match_uids'][$item['uid']]['area_id']) ? $data['user_match_uids'][$item['uid']]['area_id'] : 0;?>
        		@if(!empty($city_id) && !empty($area_id))
        			县区：{{$data['allarea'][$city_id][$area_id]}}<br/>
        		@endif
	            @if(!empty($data['user_match_uids'][$item['uid']]['company']))
	            	单位名称：{{$data['user_match_uids'][$item['uid']]['company']}}<br>
	            @endif
	            @if(!empty($data['user_match_uids'][$item['uid']]['address']))
	            	地址：{{$data['user_match_uids'][$item['uid']]['address']}}&nbsp;&nbsp;&nbsp;
	            @endif
	            @if(!empty($data['user_match_uids'][$item['uid']]['zip']))
	            	邮编：{{$data['user_match_uids'][$item['uid']]['zip']}}&nbsp;&nbsp;&nbsp;
	            @endif
	            @if(!empty($data['user_match_uids'][$item['uid']]['email']))
	            	邮箱：{{$data['user_match_uids'][$item['uid']]['email']}}
	            @endif
           		<br>
           	 	@if(!empty($data['user_match_uids'][$item['uid']]['cause']))
           	 		说明：{{$data['user_match_uids'][$item['uid']]['cause']}}
           	 	@endif
           	 	@if(!empty($data['user_match_uids'][$item['uid']]['note']))
           	 		组合类型：{{$data['user_match_uids'][$item['uid']]['note']}}
           	 	@endif
            </td>
        </tr>
		@endforeach
	</table>
	{{ $userList->appends($search)->links()  }}
    <ul class="pagination"><li><a href="javascript:;">总共 <em style="color:red;">{{$data['total']}}</em> 条记录</a></li></ul>
<script>
	$('#sdate').datepicker();
	$('#edate').datepicker();
</script>
@stop


