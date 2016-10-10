<table>
{{ Form::open(array('url' => 'admin/applyStudentList','method'=>'post')) }}
	<tr>
		<td>
		{{ Form::label('uid','用户id：',array('class'=>'awesome'));}}
    	{{ Form::text('uid',$search['uid'],array('class'=>'form-control' ,'style'=>'width:135px;display:inline')); }}
    	</td>
    	<td>
    	{{ Form::label('name', '真实姓名：',array('class'=>'awesome'))}}
    	{{ Form::text('name',$search['name'],array('class'=>'form-control' ,'style'=>'width:150px;display:inline'));}}
    	</td>
    	<td>
	    {{ Form::label('mobile','手机号码：',array('class'=>'awesome'));}}
	    {{ Form::text('mobile',$search['mobile'],array('class'=>'form-control' ,'style'=>'width:150px;display:inline'));}}
	    </td>
	    <td>
	    {{ Form::label('email','电子邮箱：',array('class'=>'awesome'));}}
	    {{ Form::text('email',$search['email'],array('class'=>'form-control' ,'style'=>'width:150px;display:inline'));}}
	    </td>
	    <td>
	    {{ Form::label('intivitationcode','参赛码：',array('class'=>'awesome'));}}
	    {{ Form::text('intivitationcode',$search['intivitationcode'],array('class'=>'form-control' ,'style'=>'width:150px;display:inline'));}}
	    </td>
	</tr>
	<tr>
		<td>
		 {{ Form::label('starttime','开　始：',array('class'=>'awesome'));}}
	    {{ Form::text('starttime',$search['starttime'],array('class'=>'form-control' ,'style'=>'width:150px;display:inline'));}}
	    </td>
	    <td>
	    {{ Form::label('endtime','结　　束：',array('class'=>'awesome'));}}
	    {{ Form::text('endtime',$search['endtime'],array('class'=>'form-control' ,'style'=>'width:150px;display:inline'));}}
	    </td>
	    <td>
	    {{ Form::label('nick','昵　　称：',array('class'=>'awesome'));}}
	    {{ Form::text('nick',$search['nick'],array('class'=>'form-control' ,'style'=>'width:150px;display:inline'));}}
	    </td>
	    <td colspan="2" >
	    {{ Form::label('competition_id','培 训 班：',array('class'=>'awesome'));}}
	    {{ Form::select('competition_id',$data['classList'],$search['competition_id'],array('class'=>'form-control' ,'style'=>'width:350px;display:inline'));}}
	    </td>
	</tr>
   	<tr>
   		<td>
   		{{ Form::label('province_id','省　份：',array('class'=>'awesome'));}}
	   {{ Form::select('province_id',$data['all_province'],$search['province_id'],array('class'=>'form-control' ,'style'=>'width:150px;display:inline'));}}
	   </td>
	   <td>
	   {{ Form::label('city_id','城　　市：',array('class'=>'awesome'));}}
	   {{ Form::select('city_id',$data['city'],$search['city_id'],array('class'=>'form-control' ,'style'=>'width:150px;display:inline'));}}
	   </td>
	   <td>
	   {{ Form::label('area_id','县　　区：',array('class'=>'awesome'));}}
	   {{ Form::select('area_id',$data['area'],$search['area_id'],array('class'=>'form-control' ,'style'=>'width:150px;display:inline'));}}
	   </td>
	   <td>
	   {{ Form::label('status','是否交费：',array('class'=>'awesome'));}}
	   {{ Form::select('status',array('-1'=>'全部',0=>'未交费',1=>'已交费'),$search['status'],array('class'=>'form-control' ,'style'=>'width:150px;display:inline'));}}
	   </td>
	   <td>
	   {{ Form::label('deal_status','是否处理：',array('class'=>'awesome'));}}
	   {{ Form::select('deal_status',array('-1'=>'全部',0=>'未处理',1=>'已处理'),$search['deal_status'],array('class'=>'form-control' ,'style'=>'width:150px;display:inline'));}}
	   </td>
	  </tr>
	   <tr>
	   <td colspan="5" style="text-align:center" >
		{{ Form::submit('搜索',array('class'=>' btn btn-mini btn-success' ,'style'=>'display:inline'));}}
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a  id="btn-dao" onclick="execl()" target="_blank">导出xls</a>
		 
		</td>
	   </tr>
{{ Form::close() }}
</table>
<script>
		//根据平台动态修改广告计划内容
		$('#starttime').datepicker({
			dateFormat:'yy-mm-dd'
			});
		$('#endtime').datepicker({
			dateFormat:'yy-mm-dd'
			});
		$("#province_id").change(function(){
			//重置
			$("#city_id").empty();
			$("#area_id").empty();
			var _val=$(this).val();
			if(_val!=""){
				$.getJSON("/admin/getCity",{province_id:_val},function(data){
					var html="<option>全部</option>";
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
	function execl(){
		
		var uid=$("input[name='uid']").val();
		var name=$("input[name='name']").val();
		var mobile=$("input[name='mobile']").val();
		var email=$("input[name='email']").val();
		var intivitationcode=$("input[name='intivitationcode']").val();
		var starttime=$("input[name='starttime']").val();
		var endtime=$("input[name='endtime']").val();
		var nick=$("input[name='nick']").val();
		var competition_id=$("#competition_id").val();
		var province_id=$("#province_id").val();
		var city_id=$("#city_id").val();
		var area_id=$("#area_id").val();
		var status=$("#status").val();
		var deal_status=$("#deal_status").val();

		var url="/admin/downStudentList";
		var url_str="?uid="+uid;
		url_str+="&name="+name;
		url_str+="&mobile="+mobile;
		url_str+="&email="+email;
		url_str+="&intivitationcode="+intivitationcode;
		url_str+="&starttime="+starttime;
		url_str+="&endtime="+endtime;
		url_str+="&nick="+nick;
		url_str+="&competition_id="+competition_id;
		url_str+="&province_id="+province_id;
		url_str+="&city_id="+city_id;
		url_str+="&area_id="+area_id;
		url_str+="&status="+status;
		url_str+="&deal_status="+deal_status;
		var tourl=url+url_str;
		 
	$("#btn-dao").attr("href",tourl);
	return true;


	}
	</script>