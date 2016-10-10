@extends('layouts.adIndex')
@section('title')
	线下缴费
@stop
@section('crumbs')
	线下缴费
@stop
@section('search')
	<form action="/admin/matchFreeAdd" method='get'>
		<table>
			<tr>
				<td>选择赛事</td>
				<td style="width:300px">
					<select name="competitionid">
                    	<?php foreach($all_goods as $k=>$v){?>
						<option value="<?php echo $k;?>" <?php if($competitionid == $k) echo "selected"?>><?php echo $v;?></option>
                        <?php }?>
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
			<th>uid</th>
			<th>name</th>
		</tr>
		@foreach ($uids as $item)
		<tr>
			<td>{{$item}}</td>
			<td>{{$userinfo[$item]}}</td>
		</tr>
		@endforeach
	</table>
</div>

@stop



