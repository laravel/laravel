{{ Form::open(array('url' => '/admin/juryList')) }}
{{Form::label('jury_competition', '有评委比赛',array('class'=>'awesome'));}}
{{ Form::select('jury_competition',$jury_competition,$search['jury_competition'],['class'=>'form-control','style'=>'width:250px;display:inline;margin-left:20px'])}}
{{Form::submit('检索',['class'=>"search btn btn-mini btn-success",'style'=>'margin-left:5px'])}}
{{ Form::close() }}