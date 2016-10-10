{{ Form::open(['url' => 'admin/personalHomepage','method'=>'post','enctype'=>'multipart/form-data']) }}
    	{{ Form::select('flag',[0=>'自己',1=>'他人'],$flag ,['class'=>"form-control",'style'=>'width:150px;display:inline']);}}
	{{ Form::submit('搜索',array('class'=>'search btn btn-mini btn-success' ,'style'=>'width:100px;display:inline'));}}
{{ Form::close() }}