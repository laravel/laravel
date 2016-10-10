{{Input::old('endtime')}}
{{ Form::open(array('url' => 'admin/jr_adv_list','method'=>'get')) }}
	{{ Form::label('os','平台',array('class'=>'awesome'));}}
    {{ Form::select('os',$data['_os'],$data['select_os'],array('class'=>'form-control' ,'style'=>'width:100px;display:inline')); }}
    {{ Form::label('adid', '广告计划',array('class'=>'awesome'))}}
    {{ Form::select('adid',$data['_adid'],$data['select_adid'],array('class'=>'form-control' ,'style'=>'width:300px;display:inline'));}}
    {{ Form::label('cid','统计方式',array('class'=>'awesome'));}}
    {{ Form::select('cid',$data['_sc'],$data['select_sc'],array('class'=>'form-control' ,'style'=>'width:100px;display:inline'));}}
    {{ Form::label('starttime','开始时间',array('class'=>'awesome'));}}
    {{ Form::text('starttime',$data['starttime'],array('class'=>'form-control' ,'style'=>'width:150px;display:inline'));}}
    {{ Form::label('endtime','结束时间',array('class'=>'awesome'));}}
    {{ Form::text('endtime',$data['endtime'],array('class'=>'form-control' ,'style'=>'width:150px;display:inline'));}}
	{{ Form::submit('搜索',array('class'=>'form-control' ,'style'=>'width:100px;display:inline'));}}
{{ Form::close() }}
<script>
		//根据平台动态修改广告计划内容
		$('#starttime').datepicker({
			dateFormat:'yy-mm-dd'
			});
		$('#endtime').datepicker({
			dateFormat:'yy-mm-dd'
			});
	</script>