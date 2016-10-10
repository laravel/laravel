@extends('layouts.adIndex')
@section('title')
	观众报名列表
@stop
@section('crumbs')
	观众报名列表
@stop
@section('search')
<form action="/admin/audienceList" method='get'>
    <table>
        <tr>
            <td>选择活动</td>
            <td>
            <select name="a_id" class="form-control" >
                <option value="0">选择活动</option>
                <?php foreach($all as $k=>$v){?>
                <option value="<?php echo $k;?>" <?php echo $k==$a_id?'selected':'';?>><?php echo $v;?></option>
                <?php }?>
            </select>
            </td>
            
            <td colspan=2>
                <input class="search btn btn-mini btn-success" type="submit"  value='查询' />
            </td>
        </tr>
    </table>
</form>
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>活动</th>
            <th>UID</th>
			<th>真实姓名</th>
			<th>昵称</th>
            <th>性别</th>
			<th>身份证号</th>
			<th>电话</th>
			<th>地址</th>
			<th>是否朗诵会员</th>
            <th>报名时间</th>

		</tr>
		@if(!empty($list))
        @foreach ($list as $item)
        <tr>
            <td>{{$item['id']}}</td>
            <td>
            <?php
            if(isset($all[$item['a_id']])){
				echo $all[$item['a_id']];
			}else{
				echo $item['a_id'];
			}
			?>
            </td>
            <td>{{$item['uid']}}</td>
            <td>{{$item['name']}}</td>
            <td>{{$item['nick_name']}}</td>
            <td>
            <?php
			if(isset($users[$item['uid']])){
				echo $users[$item['uid']]['gender'] == 1 ? '男':'女';
			}
			?>
            </td>
            <td>{{$item['card']}}</td>
            <td>{{$item['mobile']}}</td>
            <td>{{$item['address']}}</td>
           
            <td>
            <?php
            if($item['isleague']==1){
				echo "<span style=color:green>是</span>";
			}else{
				echo "否";
			}
			?>
            </td>
            <td>{{date("Y-m-d H:i",$item['addtime'])}}</td>
        </tr>
        @endforeach
		@endif
	</table>
		@if(!empty($list))
            {{ $list->appends(array('a_id'=>$a_id))->links()  }}
		@endif

@stop


