{{ Form::open(array('url' => '/admin/tianLaiGoodsList')) }}
{{Form::label('id', '商品id',['class'=>'text-primary']);}}
{{ Form::text('id',$search['id'],['class'=>'form-control','style'=>'width:100px;display:inline;margin-left:20px'])}}
{{Form::label('category', '商品分类',['class'=>'text-primary']);}}
{{ Form::select('category',$data['category'],$search['category'],['class'=>'form-control','style'=>'width:250px;display:inline;margin-left:20px'])}}
{{Form::label('isdel', '上架/下架',['class'=>'text-primary']);}}
{{ Form::select('isdel',$data['isdel'],$search['isdel'],['class'=>'form-control','style'=>'width:250px;display:inline;margin-left:20px'])}}
{{Form::submit('检索',['class'=>"search btn btn-mini btn-success",'style'=>'margin-left:5px'])}}
{{ Form::close() }}