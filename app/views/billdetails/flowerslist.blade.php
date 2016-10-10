@extends('layouts.adIndex')
@section('title')
	鲜花流水
@stop
@section('search')
	<form action="/admin/flowersList" method='get'>
		<table>
			<tr>
				<td>送花人ID</td>
				<td><input id = 'fromid' name="fromid" type='text' class="form-control" value="{{$return['fromid']}}" /></td>
				<td>收花人ID</td>
				<td><input id = 'toid' name="toid" type='text' class="form-control" value="{{$return['toid']}}" /></td>
                <td>送花人名称</td>
				<td><input id = 'fromidnick' name="fromidnick" type='text' class="form-control" value="{{$return['fromidnick']}}" /></td>
				<td>收花人名称</td>
				<td><input id = 'toidnick' name="toidnick" type='text' class="form-control" value="{{$return['toidnick']}}" /></td>
                <td>作品名</td>
				<td><input id = 'opusname' name="opusname" type='text' class="form-control" value="{{$return['opusname']}}" /></td>
			</tr>
			<tr>	
				<td>伴奏歌名</td>
				<td><input id = 'poemname' name="poemname" type='text' class="form-control" value="{{$return['poemname']}}" /></td>
				<td>伴奏名</td>
				<td><input id = 'readername' name="readername" type='text' class="form-control" value="{{$return['readername']}}" /></td>
				<td>商品名</td>
				<td><input id = 'goodname' name="goodname" type='text' class="form-control" value="{{$return['goodname']}}" /></td>
				<td>订单号</td>
				<td><input id = 'orderid' name="orderid" type='text' class="form-control" value="{{$return['orderid']}}" /></td>
				<td>
					<input type="text" id="starttime" name="starttime"  class="form-control"  value="<?php if(!empty($return['starttime'])) {echo $return['starttime'];} else {echo "开始时间";}?>"/>
				</td>
				<td>
					<input type="text" id="endtime" class="form-control"  name="endtime" value="<?php if(!empty($return['endtime'])){ echo $return['endtime'];}else{echo "结束时间";}?>"/>
				</td>
                <td>类型:</td>
				<td>
                <select id="flag" name="flag" class="form-control" style="width:110px;">
                	<option value="-1" <?php if($return['flag']==-1){echo "selected";}?> >全部类型</option>
                    <option value="0" <?php if($return['flag']== 0){echo "selected";}?> >别人赠送</option>
                    <option value="1" <?php if($return['flag']== 1){echo "selected";}?> >系统赠送</option>
                    <option value="2" <?php if($return['flag']== 2){echo "selected";}?> >兑换消耗</option>
                    <option value="3" <?php if($return['flag']== 3){echo "selected";}?> >提现消耗</option>
                </select>
                </td>
				<td colspan=2>
					<input class="search btn btn-mini btn-success" type="submit"  value='查询' />
				</td>
			<tr/>
		</table>
	</form>
@stop
@section('crumbs')
	鲜花流水
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>送花人ID</th>
			<th>收花人ID</th>
			<th>送花人</th>
			<th>收花人</th>
            <th>作品名</th>
            <th>伴奏歌名</th>
            <th>伴奏名</th>
			<th>订单号</th>
			<th>商品名</th>
			<th>数量<i class="icon-search"></i></th>
			<th>时间</th>
			<th>类型</th>
		</tr>
		<?php foreach ($flowers as $v): ?>
		<tr>
			<td><?php echo $v['fromid']; ?></td>
			<td><?php echo $v['toid']; ?></td>
			<td><?php echo $v['fromidnick']; ?></td>
			<td><?php echo $v['toidnick']; ?></td>
			<td><?php echo $v['opusname']; ?></td>
			<td><?php echo $v['poemname']; ?></td>
			<td><?php echo $v['readername']; ?></td>
            <td><?php echo $v['orderid']; ?></td>
			<td><?php echo $v['goodname']; ?></td>
			<td><?php echo $v['num']; ?></td>
			<td><?php echo date('Y-m-d H:i:s',$v['time']); ?></td>
			<td><?php echo $v['flag'];?></td>
		</tr>
	<?php endforeach;?>
	</table>
	<?php echo $flower->appends(array('fromid'=>$return['fromid'],'toid'=>$return['toid'],'fromidnick'=>$return['fromidnick'],'toidnick'=>$return['toidnick'],'opusname'=>$return['opusname'],'goodname'=>$return['goodname'],'orderid'=>$return['orderid'],'starttime'=>$return['starttime'],'endtime'=>$return['endtime'],'flag'=>$return['flag'],'poemname'=>$return['poemname'],'readername'=>$return['readername']))->links(); ?>
<script type="text/javascript">
		$('#starttime').datepicker({
			dateFormat:'yy-mm-dd'
			});
		$('#endtime').datepicker({
			dateFormat:'yy-mm-dd'
			});
</script>
@stop


