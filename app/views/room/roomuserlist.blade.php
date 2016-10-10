@extends('layouts.adIndex')
@section('title')
	聊天室成员列表
@stop
@section('search')
	<form action="/admin/roomUserList" method='get'>
		<table>
			<tr>
				
                <td style="width:100px">
					聊天室名称
                </td>
                 <td style="width:240px">
                    <select name="hx_id" class="form-control input-large">
                    	<?php
						if(!empty($all_room)){
						foreach($all_room as $k=>$v){
						?>
                    	<option value="<?php echo $k;?>" <?php echo $k==$hx_id?'selected':'';?>><?php echo $v['hx_name'];?></option>
                        <?php
						}}
						?>
                    </select>
				</td>
				<td colspan=2>
					<input class="search btn btn-mini btn-success" type="submit"  value='查询' />&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
		</table>
	</form>

@stop
@section('crumbs')
	聊天室成员列表
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>聊天室</th>
            <th>环信UID</th>
            <th>昵称</th>
            <th>性别</th>
            <th>是否群主</th>
			<th>创建时间</th>
			<th>更新时间</th>
			<th>操作</th>
		</tr>
		@foreach ($list as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$all_room[$item['hx_id']]['hx_name']}}</td>
            <td>{{$item['hx_uid']}}</td>
            <td>{{$item['nick']}}</td>
            <td><?php echo $item['gender']==1?'男':'女';?></td>
			<td><?php echo $item['is_owner']==1?'是':'否';?></td>
			<td>{{date("Y-m-d H:i",$item['addtime'])}}</td>
			<td>{{date("Y-m-d H:i",$item['updatetime'])}}</td>
            
			<td>
            <a href="#" target="_blank">移除</a>
            </td>
		</tr>
		@endforeach
	</table>
	{{ $list->appends(array('hx_id'=>$hx_id))->links()  }}
    <ul class="pagination"><li><a href="javascript:;">总共 <em style="color:red;">{{$total}}</em> 条记录</a></li></ul>

@stop

