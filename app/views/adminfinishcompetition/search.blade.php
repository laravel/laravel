{{ Form::open(array('url' => '/admin/addUserToFinishCompetition')) }}
{{Form::label('competitionid', '结束比赛',array('class'=>'awesome'));}}
{{ Form::select('competitionid',$competitionlist,$search['competitionid'],['class'=>'form-control','style'=>'width:250px;display:inline;margin-left:20px'])}}
{{Form::label('nick', '用户昵称',array('class'=>'awesome','style'=>"margin-left:20px"));}}
{{ Form::text('nick',$search['nick'],['class'=>'form-control','style'=>'width:200px;display:inline;margin-left:10px'])}}
{{Form::label('name', '真实姓名',array('class'=>'awesome','style'=>"margin-left:20px"));}}
{{ Form::text('name',$search['name'],['class'=>'form-control','style'=>'width:200px;display:inline;margin-left:10px'])}}
{{Form::label('uid', '用户id',array('class'=>'awesome','style'=>"margin-left:20px"));}}
{{ Form::text('uid',$search['uid'],['class'=>'form-control','style'=>'width:200px;display:inline;margin-left:10px'])}}

<br/>
{{Form::label('province_id', '省份',array('class'=>'awesome'));}}
{{ Form::select('province_id',$data['allprovince'],$search['province_id'],['id'=>'province_id','class'=>'form-control','style'=>'width:150px;display:inline;margin-left:20px'])}}

{{Form::label('city_id', '城市',array('class'=>'awesome'));}}
{{ Form::select('city_id',$data['city'], $search['city_id'],['id'=>'city_id','class'=>'form-control','style'=>'width:150px;display:inline;margin-left:20px'])}}

{{Form::label('area_id', '县区',array('class'=>'awesome'));}}
{{ Form::select('area_id',$data['area'],$search['area_id'], ['id'=>'area_id','class'=>'form-control','style'=>'width:150px;display:inline;margin-left:20px'])}}

{{Form::label('permission', '参赛资格',array('class'=>'awesome'));}}
{{ Form::select('permission',$permission,$search['permission'], ['id'=>'permission','class'=>'form-control','style'=>'width:150px;display:inline;margin-left:20px'])}}



{{Form::label('plat', '用户平台',array('class'=>'awesome'));}}
{{ Form::select('plat',$plat,$search['plat'],['class'=>'form-control','style'=>'width:120px;display:inline;margin-left:20px'])}}



{{Form::submit('检索',['class'=>"search btn btn-mini btn-success",'style'=>'margin-left:5px'])}}
{{ Form::close() }}
 <script>
	 
		$("#province_id").change(function(){
			//重置
			$("#city_id").empty();
			$("#area_id").empty();
			var _val=$(this).val();
			if(_val!=""){
				$.getJSON("/admin/getCity",{province_id:_val},function(data){
					var html="<option value='0'>全部</option>";
					$.each(data, function(i, field){
						html+='<option value="'+field.id+'">'+field.name+'</option>';
					});
					$("#city_id").append(html);
				});
			}
		});
		$("#city_id").change(function(){
			var _val=$(this).val();
			if(_val!=""){
				$.getJSON("/admin/getArea",{city_id:_val},function(data){
					var html="<option>全部</option>";
					$.each(data, function(i, field){
						html+='<option value="'+field.id+'">'+field.name+'</option>';
					});
					$("#area_id").append(html);
				});
			}
		});
	</script>