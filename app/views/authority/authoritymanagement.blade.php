@extends('layouts.adIndex')
@section('title')
	权限
@stop



@section('crumbs')
	权限
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>权限ID</th>
			<th>权限名称</th>
			<th>状态</th>
			<th>添加时间</th>
            <th>权限标识</th>
            <th>权限的父id</th>
            <th>平台区分</th>
			<!--<th>图标</th>-->
			<th>排序</th>
			<th>客户端标识<i class="icon-search"></i></th>
			<th>描述信息标题</th>
			<th>更新</th>
			<!--<th>描述信息详情</th>-->
		</tr>
		<?php foreach ($permission_config as $key => $value): ?>
		<tr>
			<input type="hidden" name="perid" value="<?=$value['perid']?>">
			<td><?php echo $value['perid'];?></td>
			<td><?php echo $value['name'];?></td>
			<td>
				@if($value['status'] == 0)
					<button class="authStatus btn btn-mini btn-success" type="button" value='{{$value['perid']}}|0'>正常</button>
				@elseif($value['status'] == 1)
					<button class="authStatus btn btn-mini btn-danger" type="button" value='{{$value['perid']}}|1'>删除</button>
				@endif
			</td>
			<td><?php echo date('Y-m-d H:i:s',$value['addtime']);?></td>
			<td><?php echo $value['flag'];?></td>
			<td><?php echo $value['pid'];?></td>
			<?php if ($value['plat_form'] == 2): ?>
				<td>安卓和IOS</td>
			<?php elseif($value['plat_form'] == 0): ?>
				<td>IOS</td>
			<?php elseif($value['plat_form'] == 1): ?>	
				<td>安卓</td>
			<?php endif ?>
			<!--<td><?php echo $value['icon'];?></td>-->
			<td><?php echo $value['sort'];?></td>
			<td><?php echo $value['action'];?></td>
			<td>
				<textarea type="text" rows="3" class='authContent'>{{$value['title']}}</textarea>
				<input type="hidden" value='{{$value['detailid']}}' />
			</td>
			<!--<td><?php echo $value['desc'];?></td>-->
			<td><a class="btn btn-mini btn-success" href="/admin/updatePermissions/{{$value['perid']}}" target="_blank" >更新</a></td>	
		</tr>
		<tr>
			<td colspan="13">
			描述信息详情：<textarea type="text" rows="2" cols="100" class='desc'>{{$value['desc']}}</textarea>
				<input type="hidden" value='{{$value['detailid']}}' />
			</td>
		</tr>
		<?php endforeach ?>
	</table>
	<?php echo $permission_config->links(); ?>
<script type="text/javascript">
		$('#starttime').datepicker({
			dateFormat:'yy-mm-dd'
			});
		$('#endtime').datepicker({
			dateFormat:'yy-mm-dd'
			});
		//修改状态
		$(function() {
		$('.authStatus').each(function() {
			$(this).click(function() {
				var authStr = $(this).val();
				var authArr = authStr.split('|');
				var perid = authArr[0];
				var sign = authArr[1];
				//console.log(perid);
				$.post('/admin/listStatus',{perid:perid,sign:sign},function(data) {
					if('error'==data) {
						alert(data);
						alert('添加认证失败,请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});

	//修改标题
	$(function() {
		$('.authContent').each(function() {
			$(this).focusout(function() {
				var title = $(this).val();
				var detailid = $(this).next().val();
				console.log(detailid);
				$.post('/admin/listTitle',{detailid:detailid,title:title},function(data) {
					if('error' == data) {
						alert('修改标题失败,请重试');
					} else {
						alert("修改成功");
						location.reload();
					}
				});
			});
		});
	});
	//修改描述详情
	$(function() {
		$('.desc').each(function() {
			$(this).focusout(function() {
				var desc = $(this).val();
				var detailid = $(this).next().val();
				console.log(detailid);
				$.post('/admin/listDesc',{detailid:detailid,desc:desc},function(data) {
					console.log(data);
					if('error' == data) {
						alert('修改描述失败,请重试');
					} else {
						alert("修改成功");
						location.reload();
					}
				});
			});
		});
	});
</script>
@stop


