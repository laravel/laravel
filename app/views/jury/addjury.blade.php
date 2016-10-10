@extends('layouts.adIndex')
@section('title')
	添加评委
@stop
@section('crumbs')
	添加评委
@stop
@section('search')
	<form action="/admin/GetUserList" method='get'>
		<table>
			<tr>
				<td>用户昵称</td>
				<td style="width:300px"><input id = 'nick' name="nick" type='text' class="form-control" /></td>
				<td colspan=2>
					<input class="search btn btn-mini btn-success" type="submit"  value='查询' />
				</td>
			</tr>
		</table>
	</form>
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<form action="{{ url('/admin/addJury') }}" 
				method='post'
				enctype="multipart/form-data"
		>
			<table class="table table-hover table-bordered ">
				<tr>
					<td width="200px">用户id(用户id,可以为空)</td>
					<td><input class="form-control" id="uid" name = "uid" type="text" value=""></td>
				</tr>
				<tr>
					<td>真实姓名</td>
					<td>
						<input class="form-control input-xxlarge" id="name" name = "name" type="text" value="">		  	
					</td>
				</tr>
				<tr>
					<td>活动id(表示哪个活动的评委)</td>
					<td>
						@if(!empty($not_finish_competitionlist))
							<select class="form-control" name="type" id="type">
							@foreach($not_finish_competitionlist as $k=>$v)
								<option value={{$k}}>{{$v}}</option>
							@endforeach
							</select>
						@endif
					</td>
				</tr>
				<tr>
					<td>评委级别(1总决赛 2分赛区)</td>
					<td>
                    	<select name="level" id="level" class="form-control">
                            <option value="1">总决赛评委</option>
                            <option value="2">分赛区评委</option>
                        </select>
					</td>
				</tr>
				<tr>
					<td>评委排名</td>
					<td>
						<input class="form-control input-xxlarge" id="sort" name="sort" teyp="text" value="" />
					</td>
				</tr>
				<tr>
					<td>上传头像</td>
					<td>
						<input class="file" id="thumb" name="thumb" type="file"><br/>
					</td>
				</tr>
				<tr text-align="center">
					<td  colspan="2"><input id="sub" class = "btn btn-danger" type="submit" name="提交" /></td>
				</tr>
			</table>
		</form>
@stop


