@extends('layouts.adIndex')
@section('title')
	更新广告
@stop
@section('crumbs')
	更新广告
@stop
@section('search')
	
@stop
@section('content')
{{ Form::open(array('url' => 'admin/doUpdateThirdAdvising','method'=>'post','enctype'=>'multipart/form-data')) }}
	@if(count($errors) > 0)
		@foreach($errors->all() as $error)
			<div class="alert alert-danger" role="alert">{{$error}}</div>
		@endforeach
	@endif
	<table class="table table-hover">
	<tr>
		<td width=250>{{ Form::label('id','栏目ID',['class'=>'text-primary'])}}</td>
		<td colspan=3>{{ Form::text('id',$rs['id'],['class'=>'form-control','readonly'=>'readonly'])}}</td>
	</tr>
	<tr>
		<td width=250>{{ Form::label('column_id','所属栏目',['class'=>'text-primary'])}}</td>
		<td colspan=3>{{ Form::select('column_id',$data['column_list'],$rs['column_id'],['class'=>'form-control'])}}</td>
	</tr>
	<tr>
		<td>{{ Form::label('name','广告名称',['class'=>'text-primary'])}}</td>
		<td colspan=3>{{ Form::text('name',$rs['name'],['class'=>'form-control'])}}</td>
	</tr>
	<tr>
		<td>{{ Form::label('description','广告描述',['class'=>'text-primary'])}}</td>
		<td colspan=3>{{ Form::textarea('description',$rs['description'],['class'=>'form-control','row'=>5])}}</td>
	</tr>
	<tr>
		<td>{{ Form::label('ad_type','广告类型',['class'=>'text-primary'])}}</td>
		<td>{{ Form::select('ad_type',$data['ad_type'],$rs['ad_type'],['class'=>'form-control'])}}</td>

		<td>{{ Form::label('is_close','用户可关闭',['class'=>'text-primary'])}}</td>
		<td>{{ Form::select('is_close',[0=>'不可关闭',1=>'可关闭'],$rs['is_close'],['class'=>'form-control'])}}</td>
	</tr>
	<tr>
		<td>{{ Form::label('pic','广告图片',['class'=>'text-primary'])}}</td>
		<td colspan=3>{{ Form::file('pic')}}{{ Form::image($rs['pic'],'pic',['width'=>300,'height'=>120])}}</td>
	</tr>
	<tr>
		<td>{{ Form::label('weight','权重(越大越靠前)',['class'=>'text-primary'])}}</td>
		<td>{{ Form::text('weight',$rs['weight'],['class'=>'form-control'])}}</td>

		<td>{{ Form::label('duration','展示时长(s)',['class'=>'text-primary'])}}</td>
		<td>{{ Form::text('duration',$rs['duration'],['class'=>'form-control'])}}</td>
	</tr>
	<tr>
		<td>{{ Form::label('starttime','广告开始日期',['class'=>'text-primary'])}}</td>
		<td>{{ Form::text('starttime',date('Y-m-d H:i:s',$rs['starttime']),['class'=>'form-control','id'=>'starttime'])}}</td>

		<td>{{ Form::label('endtime','广告结束日期',['class'=>'text-primary'])}}</td>
		<td>{{ Form::text('endtime',date('Y-m-d H:i:s',$rs['endtime']),['class'=>'form-control','id'=>'endtime'])}}</td>
	</tr>

	<tr>
		<td>{{ Form::label('type','跳转类型(点字可选)',['class'=>'text-primary'])}}</td>
		<td colspan="3">
			@foreach($data['type'] as $key=>$item)
			<?php $checked = '' ; if($key == $rs['type']) $checked = 'checked';?>
				@if($key != 0 && $key%5==0)
					<?php echo "<br/><br/>"; ?>
				@endif
				{{Form::label('type'.$key,"$item".':',array('style'=>'display:inline'))}}
				{{Form::radio('type',$key,'',['style'=>'margin-right:20px','id'=>'type'.$key,'class'=>'type',$checked])}}
			@endforeach
		</td>
	</tr>
	<tr>
		<td>{{Form::label('url','广告地址(站内广告地址为空,站外必填)',['class'=>'text-primary'])}}</td>
		<td colspan="3">{{Form::text('url',$rs['url'],['class'=>'form-control','id'=>'url','disabled'=>'disabled','placeholder'=>'站外必填'])}}</td>
	</tr>
	<tr>
		<td>{{Form::label('argument','跳转位置(人或者歌或者活动id,可选)',['class'=>'text-primary'])}}</td>
		<td colspan="3">{{Form::text('argument',$rs['argument'],['class'=>'form-control','id'=>'argument','disabled'=>'disabled','placeholder'=>'排除站外跳转必填'])}}</td>
	</tr>
	<tr>
		<td>{{Form::label('platform','广告平台',['class'=>'text-primary'])}}</td>
		<td colspan="3">{{Form::select('platform',[0=>'ios',1=>'android'],$rs['platform'],['class'=>'form-control','id'=>'platform'])}}</td>
	</tr>
	<tr>
		<td colspan=4 style="text-align:center">{{Form::submit('添加',['class'=>'btn btn-info','style'=>"text-align:center"])}}</td>
	</tr>
	</table>
{{ Form::close() }}
<script type="text/javascript">
	$('#starttime').datepicker({
		dateFormat:'yy-mm-dd'
	});
	$('#endtime').datepicker({
		dateFormat:'yy-mm-dd'
	});
	$('.type').each(function(){
		if($(this).is(":checked")){
			val = $(this).val();
			if(val == 0){
				$('#url').removeAttr('disabled');
				$('#argument').attr('disabled','disabled');
			}else{
				$('#url').attr('disabled','disabled');
				$('#argument').removeAttr('disabled');
			}
		}
	});
	$(function(){
		$('.type').each(function(){
			$(this).click(function() {
				if($(this).is(":checked")){
					val = $(this).val();
					if(val == 0){
						$('#url').removeAttr('disabled');
						$('#argument').attr('disabled','disabled');
					}else{
						$('#url').attr('disabled','disabled');
						$('#argument').removeAttr('disabled');
					}
				}
			});
		});
	});

</script>
@stop

