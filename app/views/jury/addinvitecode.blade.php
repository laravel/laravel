@extends('layouts.adIndex')
@section('title')
	添加邀请码
@stop
@section('crumbs')
	添加邀请码
@stop
@section('content')
		<form name="aaa" id="aaa" action="{{ url('/admin/addInviteCode') }}"  method='post'>
			<table class="table table-hover table-bordered ">
				<tr width="200px">
					<td>真实姓名</td>
					<td><input class="form-control input-xxlarge" id="name" name = "name" type="text" ></td>
				</tr>
                <tr>
					<td>邀请码</td>
					<td><input class="form-control input-xxlarge" id="code" name="code" type="text" /></td>
				</tr>
                <tr>
					<td>电话</td>
					<td><input class="form-control input-xxlarge" id="mobile" name="mobile" type="text" /></td>
				</tr>
                <tr>
					<td>地址</td>
					<td><input class="form-control input-xxlarge" id="address" name="address" type="text" /></td>
				</tr>
				
				<tr text-align="center">
					<td  colspan="2"><input id="sub" class = "btn btn-danger" type="submit" name="添加邀请码" /></td>
				</tr>
			</table>
		</form>
@stop


