@extends('layouts.adIndex')
@section('title')
	{{$title}}
@stop
@section('crumbs')
	{{$title}}
@stop
@section('search')
<form action="/admin/feedBackList" method='get'>
	<table  style=" margin-bottom:20px;">
        <tr>
            <td>状态：</td>
            <td>
            <select name="status" id="status" class="form-control">
                <?php foreach($all_status as $k=>$v){?>
                <option value="<?php echo $k?>" <?php echo $k==$status?"selected":"";?>><?php echo $v;?></option>
                <?php }?>
            </select>
            </td>
            <td>
            <input type="submit" value="搜索" class="btn btn-mini btn-success" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </td>
        </tr>
    </table>
</form>
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th style="width:30px">id</th>
			<th style="width:20px">uid</th>
            <th style="width:50px">昵称</th>
            <th style="width:50px">性别</th>
			<th style="width:100px;">真实姓名</th>
			<th style="width:50px">联系电话</th>
			<th>反馈内容</th>
            <th width="100">提交时间</th>
            <th style="width:50px">平台</th>
            <th style="width:50px">设备型号</th>
            <th>推送内容</th>
			<th width="200">操作</th>
		</tr>
		@foreach ($list as $item)
		<tr>	
			<td>{{$item['id']}}</td>
            <td>{{$item['uid']}}</td>
			<td>
            <?php
            if(isset($users[$item['uid']])){
				echo $users[$item['uid']]['nick'];
			}else{
				echo $item['nick'];
			}
			?>
            </td>
            <td>
            <?php
            if(isset($users[$item['uid']])){
				echo $users[$item['uid']]['gender']==1?'男':'女';
			}
			?>
            </td>
			<td>{{$item['realname']}}</td>
			<td>{{$item['telphone']}}</td>
            			<td style="width:200px;">{{$item['content']}}</td>
			<td>{{date('Y-m-d H:i',$item['addtime'])}}</td>
			<td>
				@if(empty($item['plat_form']))
					ios
				@else
					安卓
				@endif
			</td>
			<td>{{$item['dev']}}</td>
			<td>
				<textarea id="{{$item['id']}}" name="push_content" style="height:200px"><?php if(!empty($item['notice_msg'])) echo unserialize($item['notice_msg']); ?></textarea>
			</td>
			<td>
				<?php
                if($item['status']==0){
				?>
                <button class="operator btn btn-mini btn-danger" type="button" data-id="1"  value="{{$item['id']}}">问题不存在</button>

				<?php if($item['uid']){ ?>
                <button class="operator btn btn-mini btn-danger" type="button" data-uid= "{{$users[$item['uid']]['id']}}" data-id="2"  value="{{$item['id']}}">已解决</button>
				<?php   }?>
                <?php
				}else{
					echo $all_status[$item['status']];
				}
				?>
			</td>
		</tr>
		@endforeach
	</table>
	{{ $list->appends(array('status'=>$status))->links()  }}
<script language="javascript">
$(".operator").click(function(){
	var id=$(this).val();
	var status=$(this).attr("data-id");
	var sel = '#'+id;
	var push_content = $(sel).val();
	var uid = $(this).attr('data-uid');
	$.get("/admin/setfeedBackStatus",{'id':id,'status':status,'push_content':push_content,'uid':uid},function(data){
		if(data==-1){
			window.alert('请填写推送内容');
			return;
		}
		window.alert(data);
		window.location.reload();
	});
});
</script>
@stop

