@extends('layouts.adIndex')
@section('title')
	广告列表
@stop
@section('crumbs')
	广告列表
@stop
@section('search')
<form action="/admin/advertisingList" method='get'>
    <table  style="margin-bottom:20px;">
        <tr>
            <td>广告类型：</td>
            <td>
            <select name="type" class="form-control">
            	<option value="-1" <?php echo $search['type']==-1?'selected':'';?> >全部</option>
                <option value="1" <?php echo $search['type']==1?'selected':'';?>>站内人</option>
                <option value="2" <?php echo $search['type']==2?'selected':'';?>>站内比赛</option>
                <option value="0" <?php echo $search['type']==0?'selected':'';?>>站外</option>
                <option value="3" <?php echo $search['type']==3?'selected':'';?>>夏青杯</option>
                <option value="4" <?php echo $search['type']==4?'selected':'';?>>诵读联合会</option>
                <option value="5" <?php echo $search['type']==5?'selected':'';?>>诗经奖</option>
                <option value="6" <?php echo $search['type']==6?'selected':'';?>>活动</option>
                <option value="8" <?php echo $search['type']==8?'selected':'';?>>静态图片，不做任何操作</option>
				<option value="9" <?php echo $search['type']==9?'selected':'';?>>班级观众报名</option>
				<option value="11" <?php echo $search['type']==11?'selected':'';?>>班级活动报名</option>
				<option value="20" <?php echo $search['type']==12?'selected':'';?>>商城</option>
            </select>
            </td>
            <td>广告平台：</td>
            <td>
            <select name="platform" class="form-control">
            	<option value="-1">全部</option>
                <option value="0" <?php echo $search['platform']==0?'selected':'';?>>苹果</option>
                <option value="1" <?php echo $search['platform']==1?'selected':'';?>>安卓</option>
            </select>
            </td>
            <td>广告版本：</td>
            <td>
            <select name="isnew" class="form-control">
            	<option value="-1">全部</option>
                <option value="0" <?php echo $search['isnew']==0?'selected':'';?>>旧版本</option>
                <option value="1" <?php echo $search['isnew']==1?'selected':'';?>>新版本</option>
            </select>
            </td>
            <td>
            <input type="submit" value="搜索" class="btn btn-mini btn-success" />
            </td>
        </tr>
    </table>
</form>
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>广告名称</th>
			<th>描述信息<i class="icon-search"></i></th>
			<th>广告图片</th>
			<th>排序</th>
            <th>广告地址</th>
			<th>跳转位置</th>
			<th>广告类型</th>
			<th>广告平台</th>
			<th>添加时间</th>
			<th>广告版本</th>
			<th>修改</th>
			<th>广告状态</th>
			<th>删除</th>
		</tr>
		@if(!empty($advertisingList))
		@foreach ($advertisingList as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['name']}}</td>
			<td>{{$item['des']}}</td>
			<td><img style="width:150px;height:40px" src='{{$item['pic']}}' /></td>
			<td><input type="text" name="orderby" class="orderby" data-id="{{$item['id']}}" value="{{$item['orderby']}}" style="width:50px;" /></td>
            <td>{{$item['url']}}</td>
			<th>{{$item['argument']}}</th>
			<td>
				@if(isset($all_type[$item['type']]))
					{{$all_type[$item['type']]}}
				@endif
			</td>
			<td>
				@if($item['platform'] == 0)
					苹果
				@elseif($item['platform'] == 1)
					android
				@endif
			</td>
			<td>{{date('Y-m-d H:i',$item['addtime'])}}</td>
			<td>
				@if($item['isnew'] == 0)
					旧版
				@elseif($item['isnew'] == 1)
					新版
				@endif
			</td>
			<td>
				<!-- <button class="modify btn btn-mini btn-danger" type="button" value='{{$item['id']}}'>修改</button> -->
				<a href="/admin/advUpdate/{{$item['id']}}" target="_blank">修改</a>
			</td>
			<td>
				@if($item['status'] == 0)
					<button class="operator btn btn-mini btn-danger" type="button"  value='{{$item['id']}}|0'>关闭</button>
				@elseif($item['status'] == 1) 
					<button class="operator btn btn-mini btn-success" type="button"  value='{{$item['id']}}|1'>开启</button>
				@endif
			</td>
			<td>
				<button class="del btn btn-mini btn-danger" type="button"  value='{{$item['id']}}'>删除</button>
			</td>
		</tr>
		@endforeach
		@endif
	</table>
	{{ $advertisingList->appends(array('type'=>$search['type'],'platform'=>$search['platform'],'isnew'=>$search['isnew']))->links()  }}
<script type="text/javascript">
	$(function() {
		$(".orderby").blur(function(){
			var id=$(this).attr("data-id");
			var orderby=$(this).val();
			$.get("/admin/advOrderby",{id:id,orderby:orderby},function(data){
				window.alert("修改成功");
			});
		});
		$('.operator').each(function(){
			$(this).click(function() {
				var uidSign = $(this).val();
				var arr = uidSign.split('|');
				var adid = arr[0];
				var sign = arr[1];
				$.post('/admin/delOrDelAdv',{adid:adid,sign:sign},function(data) {
					if('error' == data) {
						alert('操作失败，请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});

	$('.del').click(
		function() {
			var adid = $(this).val();
			$.post('/admin/delAdv',{adid:adid},function(data) {
				if('error'==data) {
					alert('删除失败,请重试');
				} else {
					location.reload();
				}
			});
		});
</script>
@stop

