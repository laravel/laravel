{{Form::open(array('url'=>"/admin/thirdAdvisingList",'method'=>'post'))}}
	{{Form::label('column_id','所属栏目',['style'=>'float:left'])}}
	<div class="col-xs-2">
	{{Form::select('column_id',$data['column_list'],$search['column_id'],['class'=>'form-control'])}}
	</div>
	{{Form::label('ad_type','广告类型',['style'=>'float:left'])}}
	<div class="col-xs-2">
	{{Form::select('ad_type',$data['ad_type'],$search['ad_type'],['class'=>'form-control'])}}
	</div>
	{{Form::label('name','广告名称',['style'=>'float:left'])}}
	<div class="col-xs-2">
	{{Form::text('name','',['class'=>'form-control'])}}
	</div>
	<br/>
	<br/>
	{{Form::label('platform','广告平台',['style'=>'float:left'])}}
	<div class="col-xs-2">
	{{Form::select('platform',$data['platform'],$search['platform'],['class'=>'form-control'])}}
	</div>

	{{Form::label('starttime','开始时间',['style'=>'float:left'])}}
	<div class="col-xs-2">
	{{Form::text('starttime',$search['starttime'],['class'=>'form-control'])}}
	</div>

	{{Form::label('endtime','结束时间',['style'=>'float:left'])}}
	<div class="col-xs-2">
	{{Form::text('endtime',$search['endtime'],['class'=>'form-control'])}}
	</div>

	{{Form::label('is_del','是否删除',['style'=>'float:left'])}}
	<div class="col-xs-2">
	{{Form::select('is_del',$data['is_del'],$search['is_del'],['class'=>'form-control'])}}
	</div>

	{{Form::submit('搜索',['class'=>'btn btn-success'])}}
{{Form::close()}}
<script type="text/javascript">
	$('#starttime').datepicker({
		dateFormat:'yy-mm-dd'
	});
	$('#endtime').datepicker({
		dateFormat:'yy-mm-dd'
	});
</script>