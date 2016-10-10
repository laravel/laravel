@extends('layouts.adIndex')
@section('title')
	添加班级活动
@stop
@section('crumbs')
	添加班级活动
@stop
@section('search')
@stop

@section('content')
{{Form::open(array('url'=>"/admin/addClassActive",'method'=>'post',"enctype"=>"multipart/form-data"));}}
	<table class="table table-hover table-bordered ">
		<tr>
			<th>班级所属</th>
			<td>   
				<select name="pid"  class="form-control  awesome" style="width:200px">
					<option value="0">普通班级</option>
					<?php foreach ($college as $key => $value) {?>
						  <optgroup label='<?php echo $key ?>'></optgroup>
						<?php foreach ($value as $k=> $v) {?>
						<option value="<?=$v['id']?>"  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$v['name']?></option>
					<?php } } ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>{{Form::label('name','班级活动名称:')}}</th>
			<td>
				{{Form::text('name','',array('class'=>'form-control' ));}}
			</td>
		</tr>
		<tr>
			<th>{{Form::label('desc','班级活动描述信息:')}}</th>
			<td>{{Form::textarea('desc','',array('class'=>'form-control'))}}</td>
		</tr>
		<tr>
			<th>{{Form::label('price','班级报名费:')}}</th>
			<td>{{Form::text('price','',array('class'=>'form-control'))}}</td>
		</tr>

		<tr>
			<th>{{Form::label('piclist0','活动主图:')}}</th>
			<td>
				{{Form::file('piclist0')}}
			</td>
		</tr>
		<tr>
			<th>{{Form::label('piclist5','活动小图:')}}</th>
			<td>
				{{Form::file('piclist5')}}
			</td>
		</tr>
		<tr>
			<th>{{Form::label('piclist','活动轮播图')}}</th>
			<td>
				{{Form::file('piclist1')}}
				{{Form::file('piclist2')}}
				{{Form::file('piclist3')}}
				{{Form::file('piclist4')}}
			</td>
		</tr>
		<tr>
			<th>{{Form::label('has_invitecode','是否有邀请码')}}</th>
			<td>没有邀请码{{Form::radio('has_invitecode', 0,true);}}有邀请码:{{Form::radio('has_invitecode', 1);}}</td>
		</tr>
		<tr>
			<th>{{Form::label('sort','活动排序')}}</th>
			<td>{{Form::text('sort',0,array('class'=>'form-control'));}}</td>
		</tr>
		<tr>
			<th>{{Form::label('starttime','开始时间')}}</th>
			<td>{{Form::text('starttime','',array('class'=>'form-control'));}}</td>
		</tr>
		<tr>
			<th>{{Form::label('endtime',"结束时间")}}</th>
			<td>{{Form::text('endtime','',array('class'=>'form-control' ));}}</td>
		</tr>
		<tr>
			<th>{{Form::label('clause_title','服务条款标题')}}</th>
			<td>{{Form::text('clause_title','',array('class'=>'form-control' ));}}</td>
		</tr>
		<tr>
			<th>{{Form::label('clause','服务条款内容')}}</th>
			<td>{{Form::textarea('clause','',array('class'=>'form-control'));}}</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">{{Form::submit('添加',array('class'=>"btn btn-success"));}}</td>
		</tr>
	</table>
{{ Form::close() }}
<script>
	$('#starttime').datepicker();
	$('#endtime').datepicker();
</script>
@stop

