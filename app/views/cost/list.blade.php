@extends('layouts.adIndex')
@section('title')
	提现管理
@stop
@section('crumbs')
	提现管理
	@include('cost.search')
<script language="javascript">
	function pay_blade(id){
		$.ajax({   
			url:'/admin/givemoney?',   
			type:'post',   
			data:'id='+id,
			dataType: "json", 
			success:function(data){
				if(data.code==1){
				    alert("提现成功");
				    window.location.reload();
				}else{
				    alert("提现失败");
				}
			}
		});
	}
</script>
@stop
@section('content')
 	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>用户id<i class="icon-search"></i></th>
			<th>用户昵称</th>
			<th>提现花数量</th>
			<th>提现类型</th>
			<th>提现时间</th>
			<th>操作</th>
 
		</tr>
		@foreach ($list as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['uid']}}</td>
			<td>{{$item['nick']}}</td>
			<td>{{$item['num']}}</td>
		    <td>{{$item['type']}}</td>
			<td>{{$item['time']}}</td>
			@if(isset($item['id']) && $item['id'] ==1 )
			    <td id="pay" onclick="pay_blade({{$item['id']}})"><a href="javascript:;">发起提现</a></td>
			@else
			    <td> </td>
			@endif
		</tr>
		@endforeach
	</table>
 
 	{{$page->appends(array('username'=>'','pay_type'=>'','starttime'=>'','endtime'=>''))->links()}}


    <ul class="pagination"><li><a href="javascript:;">总共 <em style="color:red;"> {{$count}}</em> 条记录</a></li></ul>
@stop

