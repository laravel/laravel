@extends('layouts.adIndex')
@section('title')
	发送消息
@stop
@section('crumbs')
	发送消息
@stop
@section('search')
<div style="border:1px solid red;color:red;padding-left:20px">
	<h3>特别提示</h3>
	<h4>1,发信人可以通过角色管理进行添加，角色管理不提供删除角色功能，可修改昵称和头像</h4>
	<h4>2,发信内容尽量简短，最好不要超过50个字</h4>
	<h4>3,填写用户ID后,无论发送对象是否选定,都无效</h4>
	<h4>4,选定诵读比赛,诗文比赛后,请记得选择比赛</h4>
</div>
@stop

@section('content')
{{Form::open(array('url'=>"/admin/adminSendNotifiaction",'enctype'=>"multipart/form-data",'method'=>'get'));}}
	<table class="table table-hover table-bordered ">
		<tr>
			<th>{{Form::label('role_name','发信人:')}}</th>
			<td>
				@foreach($roles as $k=>$v)
					<?php if($k==0) $true = true; else $true= false;?>
					{{Form::label($k,"$v[role_name]".':',array('style'=>'display:inline'));}}{{Form::radio('role_name', $v['id'], $true,array('class'=>'radio-inline','id'=>$k));}}
					<br/>
				@endforeach
			</td>
		</tr>
		<tr>
			<th>{{Form::label('content','发信内容:')}}</th>
			<td>{{Form::textarea('content','',array('class'=>'form-control'))}}</td>
		</tr>
		<tr>
			<th>{{Form::label('touser','发送对象:')}}</th>
			<td>
				@foreach($to_users as $key=>$value)
					<span style="border:1px solid gray;padding:7px 10px;">
					{{Form::label('touser'.$key,"$value".':',array('style'=>'display:inline'))}}{{Form::radio('touser', $key, false,array('class'=>'radio-inline','id'=>'touser'.$key))}}
					</span>
				@endforeach
				{{ Form::select('competitionid',$competition,0,array('class'=>'form-control' ,'style'=>'width:350px;display:none'));}}
			</td>
		</tr>
		<tr>
			<th>{{Form::label('otheruser','用户id')}}</th>
			<td>{{Form::text('otheruser','',array('class'=>'form-control' ,'style'=>'width:200px;display:inline'));}}</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">{{Form::submit('发送',array('class'=>"btn btn-success"));}}</td>
		</tr>
	</table>
{{ Form::close() }}
<script type="text/javascript">
	$('input:radio[name="touser"]').bind('click',function(){
		var _var = $(this).val();
		if(_var==7){
			$('#competitionid').css('display','inline');
		}else{
			$('#competitionid').css('display','none');
		}
	});
</script>
@stop

