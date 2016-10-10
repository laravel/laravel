@extends('layouts.adIndex')
@section('title')
	修改班级活动
@stop
@section('crumbs')
	修改班级活动
@stop
@section('search')
@stop
@section('content')
{{Form::open(array('url'=>"/admin/changeClassActive/".$list[0]['id'],'method'=>'post',"enctype"=>"multipart/form-data"));}}
 
	<table class="table table-hover table-bordered ">
		<tr>
			<th>{{Form::label('name','班级活动名称:')}}</th>
			<td>
				{{Form::text('name',$list[0]['name'],array('class'=>'form-control' ));}}
			</td>
		</tr>
		<tr>
			<th>{{Form::label('desc','班级活动描述信息:')}}</th>
			<td>{{Form::textarea('desc',$list[0]['desc'],array('class'=>'form-control'))}}</td>
		</tr>
		<tr>
			<th>{{Form::label('piclist0','活动主图:')}}</th>
			<td>
			<img src="<?php echo "/".$list[0]['mainpic'] ?>" width="150" height="100" />{{Form::file('piclist0')}}
			</td>
		 </tr>
		 	<tr>
			<th>{{Form::label('piclist5','活动小图:')}}</th>
			<td>
			<img src="<?php echo "/".$list[0]['smallpic'] ?>" width="150" height="100" />{{Form::file('piclist5')}}
			</td>
		 </tr>
		<tr>
			<th>{{Form::label('piclist','活动轮播图')}}</th>
			<td>
			 	<img src="/<?php echo  isset($list[0]['piclist']['piclist1'])?$list[0]['piclist']['piclist1']:"" ?>"  width="150" height="100" />{{Form::file('piclist1')}}
				<img src="/<?php echo  isset($list[0]['piclist']['piclist2'])?$list[0]['piclist']['piclist2']:"" ?>"  width="150" height="100" />{{Form::file('piclist2')}}
				<img src="/<?php echo  isset($list[0]['piclist']['piclist3'])?$list[0]['piclist']['piclist3']:"" ?>"  width="150" height="100" />{{Form::file('piclist3')}}
				<img src="/<?php echo  isset($list[0]['piclist']['piclist4'])?$list[0]['piclist']['piclist4']:"" ?>"  width="150" height="100" />{{Form::file('piclist4')}}	 
			</td>
		</tr>
		<tr>
			<th>{{Form::label('has_invitecode','是否有邀请码')}}</th>
			<td>没有邀请码{{Form::radio('has_invitecode', 0,true);}}有邀请码:{{Form::radio('has_invitecode', 1);}}</td>
		</tr>
		<tr>
			<th>{{Form::label('sort','活动排序')}}</th>
			<td>{{Form::text('sort',$list[0]['sort'],array('class'=>'form-control'));}}</td>
		</tr>
		<tr>
			<th>{{Form::label('starttime','开始时间')}}</th>
			<td>{{Form::text('starttime',date("m/d/Y",$list[0]['starttime']),array('class'=>'form-control'));}}</td>
		</tr>
		<tr>
			<th>{{Form::label('endtime',"结束时间")}}</th>
			<td>{{Form::text('endtime',date("m/d/Y",$list[0]['endtime']),array('class'=>'form-control' ));}}</td>
		</tr>
		<tr>
			<th>{{Form::label('clause_title','服务条款标题')}}</th>
			<td>{{Form::text('clause_title',$list[0]['clause_title'],array('class'=>'form-control' ));}}</td>
		</tr>
		<tr>
			<th>{{Form::label('clause','服务条款内容')}}</th>
			<td>{{Form::textarea('clause',$list[0]['clause'],array('class'=>'form-control'));}}</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">{{Form::submit('修改',array('class'=>"btn btn-success"));}}</td>
		</tr>
	</table>
{{ Form::close() }}
<script>
	$('#starttime').datepicker();
	$('#endtime').datepicker();
</script>
@stop

