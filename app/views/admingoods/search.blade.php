{{ Form::open(array('url' => '/admin/addUserToFinishCompetition')) }}
{{Form::label('competitionid', '结束比赛',array('class'=>'awesome'));}}
{{ Form::select('competitionid',$competitionlist,$search['competitionid'],['class'=>'form-control','style'=>'width:250px;display:inline;margin-left:20px'])}}
{{Form::label('nick', '用户昵称',array('class'=>'awesome','style'=>"margin-left:20px"));}}
{{ Form::text('nick',$search['nick'],['class'=>'form-control','style'=>'width:200px;display:inline;margin-left:10px'])}}
{{Form::label('uid', '用户id',array('class'=>'awesome','style'=>"margin-left:20px"));}}
{{ Form::text('uid',$search['uid'],['class'=>'form-control','style'=>'width:200px;display:inline;margin-left:10px'])}}
{{Form::submit('检索',['class'=>"search btn btn-mini btn-success",'style'=>'margin-left:5px'])}}
{{ Form::close() }}