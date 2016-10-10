@extends('layouts.adIndex')
@section('title')
	后台你添加认证用户
@stop
@section('crumbs')
	后台你添加认证用户
@stop
@section('search')
	<form action="/admin/getAdmAddUser" method='post'>
		<table>
			<tr>
				<td>活动名称</td>
				<td style="width:300px">
					<select name="type">
						<option value=1 <?php if($type == 1) echo "selected"?>>诵读联合会</option>
						<option value=2 <?php if($type==2) echo "selected"?>>夏青杯</option>
					</select>
				</td>
				<td colspan=2>
					<input type="submit" value='查询' />
				</td>
			</tr>
		</table>
	</form>
@stop
@section('content')
<div class="table-responsive">
	<table class="table table-hover table-bordered">
		<tr>
			<th>id</th>
			<th>uid</th>
		</tr>
		@foreach ($userinfo as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['nick']}}</td>
		</tr>
		@endforeach
	</table>
</div>
	{{ $rs->links()  }}

@stop



