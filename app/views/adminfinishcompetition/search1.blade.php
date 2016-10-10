{{ Form::open(array('url' => '/admin/listFinishCompetition')) }}
{{Form::label('competitionid', '结束比赛',array('class'=>'awesome'));}}
{{ Form::select('competitionid',$competitionlist,$search['competitionid'],['id'=>'competitionid','class'=>'form-control','style'=>'width:250px;display:inline;margin-left:20px'])}}

<script src=""></script>	
{{Form::label('nick_name','用户昵称',array('class'=>'awesome','style'=>"margin-left:20px"));}}
{{ Form::text('nick_name',$search['nick_name'],['id'=>'nick_name','class'=>'form-control','style'=>'width:200px;display:inline;margin-left:10px'])}}

{{Form::label('name', '真实姓名',array('class'=>'awesome','style'=>"margin-left:20px"));}}
{{ Form::text('name',$search['name'],['id'=>'name','class'=>'form-control','style'=>'width:200px;display:inline;margin-left:10px'])}}

{{Form::label('uid', '用户id',array('class'=>'awesome','style'=>"margin-left:10px"));}}
{{ Form::text('uid',$search['uid'],['id'=>'uid','class'=>'form-control','style'=>'width:200px;display:inline;margin-left:10px'])}}
<br>
{{Form::label('plat_from', '用户平台',array('class'=>'awesome','style'=>'margin-left:10px'));}}
{{ Form::select('plat_from',$plat_from,$search['plat_from'],['id'=>'plat_from','class'=>'form-control','style'=>'width:120px;display:inline;margin-left:10px'])}}

{{Form::label('pay_type', '付款平台',array('class'=>'awesome','style'=>'margin-left:10px' ));}}
{{ Form::select('pay_type',$pay_type,$search['pay_type'],['id'=>'pay_type','class'=>'form-control','style'=>'width:140px;display:inline;margin-left:10px'])}}

{{Form::label('CD','光盘  ',array('class'=>'awesome','style'=>'margin-left:10px'));}}
{{ Form::select('CD',$CD,$search['CD'], ['id'=>'CD','class'=>'form-control','style'=>'width:150px;display:inline;margin-left:10px'])}}

{{Form::label('sdate', '开始时间',array('class'=>'awesome','style'=>'margin-left:20px'));}}
{{ Form::text('sdate' ,$search['sdate'],['id'=>'sdate','class'=>'form-control','style'=>'width:150px;display:inline;margin-left:7px'])}}
{{Form::label('edate', '结束时间',array('class'=>'awesome'));}}
{{ Form::text('edate',$search['edate'] ,['id'=>'edate','class'=>'form-control','style'=>'width:150px;display:inline;margin-left:10px'])}}

    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
 
{{Form::submit('检索',['class'=>"search btn btn-mini btn-success",'style'=>'margin-left:30px'])}}
 <a   onclick="execl()" id="btn-dao" target="_blank">导出xls</a>
{{ Form::close() }}
 <script>
  function execl(){
	 
	var uid=$("#uid").val();
	var name=$("#name").val();
	var competitionid=$("#competitionid").val();
	var nick_name=$("#nick_name").val();
	var plat_from=$("#plat_from").val();
	var pay_type=$("#pay_type").val();
	var CD=$("#CD").val();
	var sdate=$("#sdate").val();
	var edate=$("#edate").val();

	var url="/admin/listFinishdown";
	var url_str="?uid="+uid;
	url_str+="&name="+name;
	url_str+="&nick_name="+nick_name;
	url_str+="&competitionid="+competitionid;
	url_str+="&plat_from="+plat_from;
	url_str+="&pay_type="+pay_type;
	url_str+="&CD="+CD;
	url_str+="&sdate="+sdate;
	url_str+="&edate="+edate;
		var tourl=url+url_str;
	$("#btn-dao").attr("href",tourl);

	return true;
}



	$('#sdate').datepicker();
	$('#edate').datepicker();
 
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