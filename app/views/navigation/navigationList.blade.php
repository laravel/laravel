@extends('layouts.adIndex')
@section('title')
	导航列表|添加伴奏分类
@stop
@section('search')
	<form>
		<table class="table table-hover table-bordered ">
			<tr>
				<td width="150px">父分类</td>
				<td width="600px">
					<select id = 'lastdata' name='lastdata' class="form-control">
						@foreach($navigationList as $item)
							@if($item['pid'] == 0)
								<option value='{{$item['id']}}'>{{$item['category']}}</option>
							@endif
						@endforeach
					</select>
				</td>
			</tr>
			<tr>
				<td>分类名称</td>
				<td>
					<input class="form-control" type="text" id = 'subhead' name = 'subhead'>
				</td>
			</tr>
			<tr>
				<td>分类排列顺序</td>
				<td>
					<input class="file" id="subsort" name="subsort" type="text">
				</td>
			</tr>
			<tr>
				<td>添加子类/添加父类</td>
				<td>
					<input type="radio" name="suborparcat" id = "suborparcat" value="0" />父类
					<input type="radio" name="suborparcat" id = "suborparcat" value="1" checked/>子类
				</td>
			</tr>
			<tr text-align="center">
				<td  colspan="2"><button class="operator btn btn-mini btn-danger" type="button"  value=''>添加子类</button></td>
			</tr>
		</table>
	</form>
@stop
@section('crumbs')
	导航列表
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>导航id</th>
			<th>父分类id</th>
			<th>导航名称</th>
			<th>导航图片<i class="icon-search"></i></th>
			<th>导航类型</th>
			<th>导航顺序</th>
			<th>图片尺寸</th>
			<th>禁用/恢复</th>
		</tr>
		@foreach ($navigationList as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['pid']}}</td>
			<td><input class="mycategory" type="text" value='{{$item['id']}}|{{$item['category']}}' /><br/><span style="color:red">请一定保留分类前面的数字和'|'号</sapn></td>
			<td><img style="width:25px;height:25px" src='{{$item['pic']}}' />
				<form action="{{ url('/admin/modifyNavigation') }}"
		        method="post"
		        enctype="multipart/form-data"
				>
					<input class="file" id="formName" name="formName" type="file">
					<input type='hidden' name='id' value='{{$item['id']}}' />
					<input id="sub" class = "btn btn-success" type="submit" name="修改" />
				</form>
			</td>
			<td>
				@if($item['type'] == 0) 
					<button type="button" class="btn btn-info">首页/分类</button>
				@elseif($item['type'] == 1)
					<button type="button" class="btn btn-info">首页</button>
				@endif
			</td>
			
			<td>
				<input type="text" id="modifysort" name="modifysort" class="modifysort" value='{{$item['sort']}}'/>
				<input type="hidden" id='navid' name='navid' value='{{$item['id']}}' />
			</td>
			<td>{{$item['size']}}</td>
			<td>
				@if($item['isdel'] == 0)
					<button class="navdelorreplay btn btn-mini btn-danger" type="button"  value='{{$item['id']}}|0'>删除分类</button>
				@elseif($item['isdel'] == 1) 
					<button class="navdelorreplay btn btn-mini btn-success" type="button" value='{{$item['id']}}|1'>恢复分类</button>
				@endif
			</td>
		</tr>
		@endforeach
	</table>
<script type="text/javascript">
	$(function() {
		$('.mycategory').each(function(){
			$(this).focusout(function() {
				var category = $(this).val();
				var obj = $(this);
				if(category.indexOf('|')<0) {
					alert("请一定保留分类前面的数字和'|'号");
					return;
				}
				var arr = category.split('|');
				var categoryid = arr[0];
				var cateName = arr[1];
				$.post('/admin/modifyNavigation',{categoryid:categoryid,cateName:cateName,type:1},function(data) {
					if('error' == data) {
						alert('操作失败');
					}
				});
			});
		});
	});

	//检测子分类是否存在
	$(function() {
		$('#subhead').focusout(function() {
			var subhead = $('#subhead').val(); //子分类名称
			var parentid = $('select option:selected').val(); //父分类id
			if(subhead.length <=0) {
				alert('分类名称不能为空');
				return;
			}
			//检测此父分类下的子类是否存在
			$.post('/admin/checkSubHeadExists',{subhead:subhead,parentid:parentid},function(data) {
				if('error'==data) {
					alert('此分类已经存在,请更换名称');
					return;
				}
			});
		});
	});

	//添加分类
	$(function() {
		$('.operator').click(function() {
			var subhead = $('#subhead').val(); //分类名称
			var parentid = $('select option:selected').val(); //分类id
			var subsort = $('#subsort').val();
			var suborparcat = $(':radio:checked').val(); //0父类1子类
			if(subhead.length<=0 || parentid.length <=0 || subsort.length<=0) {
				alert('有选项没填，请检查');
				return;
			}
			$.post('/admin/addSubNavigation',{subhead:subhead,parentid:parentid,subsort:subsort,suborparcat:suborparcat},function(data) {
				if('error'==data) {
					alert('添加类失败，请重试');
					return;
				} else {
					location.reload();
				}
			});
		});
	});

	//修改导航顺序
	$(function() {
		$('.modifysort').each(function() {
			$(this).focusout(function() {
				var oldSort = $(this).val();
				var navid = $(this).next().val();
				if(oldSort.length<=0) {
					alert('修改后的顺序不能为空');
					return;
				}
				$.post('/admin/modifyNavSort',{oldSort:oldSort,navid:navid},function(data) {
					if('error' == data) {
						alert('修改顺序失败,请重试');
						return;
					} else {
						location.reload();
					}
				});
			});
		});
	});

	//删除分类 or 恢复分类
	$(function() {
		$('.navdelorreplay').each(function() {
			$(this).click(function() {
				var navigation = $('.navdelorreplay').val();
				if(navigation.length <=0) {
					alert('无法删除分类,请重试');
					return;
				}
				var arr = navigation.split('|');
				var navid = arr[0];
				var status = arr[1];
				$.post('/admin/navDelOrReplay',{navid:navid,status:status},function(data) {
					if('error' == data) {
						alert('操作失败，请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});
</script>
@stop

