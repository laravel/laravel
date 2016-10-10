@extends('layouts.adIndex')
@section('title')
	比赛列表
@stop
@section('crumbs')
	比赛列表
@stop
@section('search')
	<form action="/admin/getCompetitionList" method='get'>
		<table>
			<tr>
				<td>比赛分类</td>
				<td style="width:110px">
					<select name="pid">
                    	<option value="-1">所有</option>
                        <option value="0" <?php echo $search['pid']==0?'selected':'';?>>其他朗诵会</option>
                        <option value="1" <?php echo $search['pid']==1?'selected':'';?>>官方朗诵会</option>
						<option value="2" <?php echo $search['pid']==2?'selected':'';?>>社团朗诵会</option>
						<option value="3" <?php echo $search['pid']==3?'selected':'';?>>名人朗诵会</option>
                        <option value="4" <?php echo $search['pid']==4?'selected':'';?>>高端赛事</option>
                        <option value="5" <?php echo $search['pid']==5?'selected':'';?>>普通赛事</option>
                        <option value="6" <?php echo $search['pid'] == 6 ? 'selected':'';?>>诗文高端赛事</option>
                        <option value="7" <?php echo $search['pid'] == 7 ? 'selected':'';?>>诗文普通赛事</option>
					</select>
				</td>
				<td>比赛名称</td>
				<td>
					<input type="text" name="name" id="name" value="<?php echo $search['name'];?>" />
				</td>
				<td>
					<input class="search btn btn-mini btn-success" type="submit"  value='选择' />
				</td>
			</tr>
		</table>
	</form>
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>比赛id</th>
			<th>比赛名称</th>
			<th>主图<i class="icon-search"></i></th>
			<th>轮播图</th>
			<th>分类名称</th>
			<th>开始时间</th>
			<th>结束时间</th>
			<th>是否有月榜</th>
            <th>收费内容</th>
			<th>置顶</th>
			<th>结束/未结束</th>
		</tr>
		@foreach ($compeition as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['name']}}</td>
			<td><img src="{{$item['mainpic']}}" width="100" /></td>
			<td style="width:150px">
            <?php
            $tmp = explode(';',$item['piclist']);
            echo implode("<br>",$tmp);
			?>
            
            </td>
			<td>{{$item['pid']}}</td>
			<td>{{date('Y/m/d H:i',$item['starttime'])}}</td>
			<td>{{date('Y/m/d H:i',$item['endtime'])}}</td>
			<td>{{$item['monthflag']}}</td>
            <td>
			<?php
            if(isset($search['price'][$item['id']])){
				echo $search['price'][$item['id']];
			}else{
				echo "-";
			}
			?>
            </td>
			<td>
				@if($item['sort'] == 0)
					<button name="makeTop" id="makeTop" comptitionid={{$item['id']}} style="width:50px" onclick="makeTop($(this))">置顶</button>
				@else
					<button name="makeTop">已置顶</button>
				@endif
			</td>
			<td>
				@if(empty($item['isfinish']))
					<button class="operator btn btn-mini btn-success" type="button" isfinish = {{$item['isfinish']}} finish={{$item['id']}} onclick="doFinish($(this))">未结束</button>
				@else
					<button class="operator btn btn-mini btn-danger" type="button" isfinish = {{$item['isfinish']}} finish={{$item['id']}} onclick="doFinish($(this))">结束</button>
				@endif
                <a href="/admin/updateCompetition?id=<?php echo $item['id']?>">修改</a>
			</td>
		</tr>
		@endforeach
	</table>
@stop
<script type="text/javascript">
	function doFinish(_this){
		var id = _this.attr('finish');
		var isfinish = _this.attr('isfinish');
		if(id == 'underfined' || id == null || id == ''){ alert('操作失败,请重试');}
		if(confirm('确定此操作?')){
			$.ajax({
				'type':'POST',
				'url':'/admin/finishCompetition',
				'dataType':'json',
				'data':{'id':id,'isfinish':isfinish},
				'success':function(data){
					if(data == 1){ window.location.reload(); return;}
					if(data == -1){ alert('操作失败,请重试'); return;}
				}
			});
		};
	}

	function makeTop(_this)
	{
		var comptitionid = _this.attr('comptitionid');
		if(comptitionid == 'underfined' || comptitionid == '' || comptitionid == null)
		{
			alert('置顶失败');
			return;
		}
		if(confirm('确定置顶？')) {
			$.ajax({
				'type':'POST',
				'url':'/admin/makeTop',
				'dataType':'json',
				'data':{'id':comptitionid},
				'success':function(data){
					if(data == 1) {window.location.reload();return;}
					if(data == -1) {alert('操作失败');return;}
				}
			});
		}
	}
</script>

