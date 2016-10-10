@extends('layouts.adIndex')
@section('title')
	活动聊天房间管理
@stop
@section('search')
	<form action="/admin/roomList" method='get'>
		<table>
			<tr>
				
                <td style="width:150px">
					活动名称
                </td>
                 <td style="width:150px">
                    <input type="text" name="name" value="<?php echo $name;?>" />
				</td>
				<td colspan=2>
					<input class="search btn btn-mini btn-success" type="submit"  value='查询' />&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
		</table>
	</form>

@stop
@section('crumbs')
	活动聊天房间管理
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>活动名称</th>
            <th>环信组名称</th>
            <th>环信组id</th>
			<th>密码</th>
			<th>说明</th>
			<th>创建时间</th>
			<th>关闭时间</th>
			<th>操作</th>
		</tr>
		@foreach ($roomlist as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['c_id']}}</td>
            <td>{{$item['hx_name']}}</td>
            <td>{{$item['hx_id']}}</td>
			<td>{{$item['password']}}</td>
			<td>{{$item['content']}}</td>
			<td>{{date("Y-m-d H:i",$item['addtime'])}}</td>
			<td>{{date("Y-m-d H:i",$item['closetime'])}}</td>
            
			<td>
            <a href="/admin/roomUserList?hx_id={{$item['hx_id']}}" target="_blank">查看成员</a>
            </td>
		</tr>
		@endforeach
	</table>
	{{ $roomlist->appends(array('name'=>$name))->links()  }}
    <ul class="pagination"><li><a href="javascript:;">总共 <em style="color:red;">{{$total}}</em> 条记录</a></li></ul>

@stop

