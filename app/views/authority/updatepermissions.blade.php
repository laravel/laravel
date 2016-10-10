@extends('layouts.adIndex')
@section('title')
	修改权限
@stop
@section('crumbs')
	修改权限
@stop
@section('content')
	<form action ='/admin/updatePer' method = 'post' enctype="multipart/form-data" >
	<table class="table table-hover table-bordered ">
		<tr>
			<td width="200px">权限名称</td>
			<td><input class="form-control input-xxlarge" id="name" name = "name" type="text" value="<?=$permission['name']?>"></td>
			<input type="hidden" name = "id" value="<?=$permission['id']?>">
		</tr>
		<tr>
			<td width="200px">图标</td>
			<td><input class="form-control input-xxlarge" id="icon" name = "icon" type="file" value="">
			<img src="<?=$permission['icon']?>">
			</td>

		</tr>
		<tr>
			<td>排序</td>
			<td><input class="form-control input-xxlarge" id="sort" name = "sort" type="text" value="<?=$permission['sort']?>"></td>
		</tr>
		<tr>
			<td>权限标识</td>
			<td><input class="form-control input-xxlarge" id="flag" name = "flag" type="text" value="<?=$permission['flag']?>"></td>
		</tr>
		<tr>
			<td>客户端标识</td>
			<td><input class="form-control input-xxlarge" id="action" name = "action" type="text" value="<?=$permission['action']?>"></td>
		</tr>
		<tr>
			<td>状态</td>
			<td>
				<input type="radio" name="status"  value="0" <?php if($permission['status'] == 0) echo 'checked="checked"';?> >正常
				<input type="radio" name="status"  value="1" <?php if($permission['status'] == 1) echo 'checked="checked"';?> >删除
			</td>
		</tr>
		<tr>
			<td>权限父id</td>
			<td>
				<input type="radio" name="pid"  value="0" <?php if($permission['pid'] == 0) echo 'checked="checked"';?> >会员
				<input type="radio" name="pid"  value="1" <?php if($permission['pid'] == 1) echo 'checked="checked"';?> >私信通
			</td>
		</tr>
		<tr>
			<td>平台区分</td>
			<td>
				<input type="radio" name="plat_form"  value="2" <?php if($permission['plat_form'] == 2) echo 'checked="checked"';?> >全部
				<input type="radio" name="plat_form"  value="0" <?php if($permission['plat_form'] == 0) echo 'checked="checked"';?> >IOS
				<input type="radio" name="plat_form"  value="1" <?php if($permission['plat_form'] == 1) echo 'checked="checked"';?> >安卓					
			</td>
		</tr>
		<tr text-align="center">
			<td  colspan="2"><input id="sub" class = "btn btn-danger" type="submit" name="提交" /></td>
		</tr>
	</table>
	</form>
	
<script type="text/javascript">
		$('#starttime').datepicker({
			dateFormat:'yy-mm-dd'
			});
		$('#endtime').datepicker({
			dateFormat:'yy-mm-dd'
			});
</script>
@stop

