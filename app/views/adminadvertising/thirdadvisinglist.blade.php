@extends('layouts.adIndex')
@section('title')
	获取第三方广告列表
@stop
@section('crumbs')
	获取第三方广告列表
@stop
@section('search')
	@include('adminadvertising.search')
@stop
@section('content')
{{Form::open(array('url'=>"",'method'=>'post',"enctype"=>"multipart/form-data"));}}
	<table class="table table-hover table-bordered ">
	<tr>
		<td>id</td>
		<td>所属栏目</td>
		<td>名称</td>
		<td>类型</td>
		<td>用户可关闭</td>
		<td>广告权重</td>
		<td>展示时长(s)</td>
		<td>开始时间</td>
		<td>结束时间</td>
		<td>更新</td>
		<td>是否删除</td>
	</tr>
	@foreach($data['list'] as $item)
	<tr>
		<td>{{$item['id']}}</td>
		<td>
			{{$data['column_list'][$item['column_id']]}}
		</td>
		<td>{{$item['name']}}</td>
		<td>
			{{$data['ad_type'][$item['ad_type']]}}
		</td>
		<td>
			{{$data['is_close'][$item['is_close']]}}
		</td>
		<td>{{$item['weight']}}</td>
		<td>{{$item['duration']}}</td>
		<td>{{date('Y-m-d H:i:s',$item['starttime'])}}</td>
		<td>{{date('Y-m-d H:i:s',$item['endtime'])}}</td>
		<td><a href="/admin/updateThrAd/{{$item['id']}}" target="_blank"/>更新</a></td>
		<td>
			@if($item['is_del'] == 0)
				<button class="is_del btn btn-mini btn-danger" type="button" value={{$item['id']}} data-flag = 0>删除</button>
			@else
				<button class="is_del btn btn-mini btn-success" type="button" value={{$item['id']}} data-flag = 1>恢复</button>
			@endif
		</td>
	</tr>
	<tr>
		<td colspan="11">
			所属平台:
			@if($item['platform'] == 0)
				苹果
			@else
				安卓
			@endif
			<br/>
			跳转类型:{{$data['type'][$item['type']]}}
			<br/>
			@if($item['type'] ==0 )
				外链地址:{{$item['url']}}
			@else
				跳转id:{{$item['argument']}}
			@endif
			<br/>
			描述信息:{{$item['description']}}<br/><br/>
			{{Form::image($item['pic'],'',['width'=>300,'height'=>120])}}
		</td>
	</tr>
	@endforeach
	</table>
{{Form::close()}}
<script type="text/javascript">
	$('.is_del').each(function(){
		$(this).bind('click',function(){
			var id = $(this).val();
			var is_del = $(this).attr('data-flag');
			$.ajax({
				url:'/admin/delRevertThrAdv',
				method:"POST",
				data:{id:id,is_del:is_del},
			}).done(function(msg){
				if(msg==1){
					window.location.reload();
				}else{
					alert(msg);
					return;
				}
			});
		});
	});
</script>
@stop

