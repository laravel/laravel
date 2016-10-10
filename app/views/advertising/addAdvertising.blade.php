@extends('layouts.adIndex')
@section('title')
	添加广告
@stop
@section('crumbs')
	添加广告
@stop
@section('content')
<form action="{{ url('/admin/doAddAdvertising') }}"
        method="post"
        enctype="multipart/form-data"
		>
	<table class="table table-hover table-bordered ">
		@if(!empty($data['id']))
			<input type="hidden" name="id" value="{{$data['id']}}" />
		@endif
		<tr>
			<td width="250px">广告名称<span style="color:red"> *</span></td>
			<td><input class="form-control input-xxlarge" id="name" name = "name" type="text" value="@if(!empty($data['name'])){{$data['name']}}@endif"></td>
		</tr>
		<tr>
			<td>描述信息<span style="color:red"> *</span></td>
			<td height="300px">
				<textarea class="form-control" rows="12" id="des" name="des">@if(!empty($data['des'])) {{$data['des']}}@endif</textarea>	  	
			</td>
		</tr>
		<tr>
			<td>上传图片(图片大小640*100)<span style="color:red"> *</span></td>
			<td><input class="file" id="formName" name="formName" type="file">
				@if(!empty($data['pic']))
					<img src='<?php echo Config::get('app.url');?>{{$data['pic']}}' />
				@endif
			</td>
		</tr>
		<tr>
			<td>广告地址(站内广告地址为空,站外必填)</td>
			<td>
			  	<input class="form-control" type="text" id='adurl' name='adurl' value="@if(!empty($data['url'])){{$data['url']}}@endif">
			</td>
		</tr>
		<tr>
			<td>跳转位置(人或者歌或者活动id,可选)</td>
			<td>
				<input class="form-control" type="text" name="argument" id = "argument" value="@if(!empty($data['argument'])){{$data['argument']}}@endif">
			</td>
		</tr>
		<tr>
			<td>广告类型<span style="color:red"> *</span></td>
			<td>
				<input type="radio" name="type"  value="1" @if(isset($data['type']) && $data['type'] == 1) checked @endif/>站内人
				<input type="radio" name="type"  value="2" @if(isset($data['type']) && $data['type'] == 2) checked @endif/>诵读会
				<input type="radio" name="type"  value="0" @if(isset($data['type']) && $data['type'] == 0) checked @endif />站外
				<input type="radio" name="type"  value="3" @if(isset($data['type']) && $data['type'] == 3) checked @endif/>夏青杯
				<input type="radio" name="type"  value="4" @if(isset($data['type']) && $data['type'] == 4) checked @endif/>诵读联合会
                <input type="radio" name="type"  value="5" @if(isset($data['type']) && $data['type'] == 5) checked @endif/>诗经奖
                <input type="radio" name="type"  value="6" @if(isset($data['type']) && $data['type'] == 6) checked @endif/>站内比赛
                <input type="radio" name="type"  value="8" @if(isset($data['type']) && $data['type'] == 8) checked @endif/>静态图片，不做任何操作
                <input type="radio" name="type"  value="9" @if(isset($data['type']) && $data['type'] == 9) checked @endif/>活动观众报名
                <input type="radio" name="type"  value="11" @if(isset($data['type']) && $data['type'] == 11) checked @endif/>班级活动报名
				<input type="radio" name="type"  value="11" @if(isset($data['type']) && $data['type'] == 12) checked @endif/>商城
			</td>
		</tr>
		<tr>
			<td>广告平台<span style="color:red"> *</span></td>
			<td>
				<input type="radio" name="platform"  value="0" @if(isset($data['platform']) && $data['platform'] == 0) checked @endif >苹果
				<input type="radio" name="platform"  value="1" @if(isset($data['platform']) && $data['platform'] == 1) checked @endif>android
			</td>
		</tr>
		<tr>
			<td>广告版本<span style="color:red"> *</span></td>
			<td>
				<input type="radio" name="isnew"  value="0" @if(isset($data['isnew']) && $data['isnew'] == 0) checked @endif >旧版本
				<input type="radio" name="isnew"  value="1" @if(isset($data['isnew']) && $data['isnew'] == 1) checked @endif >新版本
			</td>
		</tr>

		<tr text-align="center">
			<td  colspan="2"><input id="sub" class = "btn btn-danger" type="submit" name="提交" /></td>
		</tr>
	</table>
</form>
<script type="text/javascript">
	
</script>
@stop

