<?php 
	/**
	*	静态榜单生成 每月第一天执行
	*	朗诵会的静态榜单
	*	@author:wang.hognli
	*	@since:2014/12/07
	*/	
	//设置时区
	ini_set('date.timezone','Asia/Shanghai');

	if(!$pdo = conDataBase())
	{
		die('数据库连接失败');
	}
	$endtime = strtotime(date('Y-m-d',time()))-10;
	$sql = "select id,endtime from competitionlist where isfinish = 0 and endtime >= {$endtime}";	
	$statement = $pdo->query($sql);
	$tmpRs = $statement->fetchAll(PDO::FETCH_ASSOC);
	// $tmpRs = array(array('id'=>8,'endtime'=>1419782399),array('id'=>14,'endtime'=>1426175999));
	if(empty($tmpRs))
	{
		die('没有要生成的数据');
	}
	foreach($tmpRs as $key=>$value)
	{
		if(empty($value)) continue;

		$sql = "select opus.id,opus.uid,opus.commentnum,opus.poemid,opus.name,opus.url,opus.lyricurl,opus.type,opus.firstchar,opus.premonthnum,opus.lnum-opus.premonthnum as lnum,opus.repostnum,opus.praisenum,opus.addtime,opus.opustime,user.nick,user.gender,user.grade,user.sportrait,user.authtype,user.teenager,user.isleague from competition_opus_rel left join opus on competition_opus_rel.opusid = opus.id left join user on user.id = opus.uid where competition_opus_rel.competitionid = {$value['id']} and opus.isdel = 0 and user.isdel != 1 order by lnum desc,opus.repostnum desc, opus.praisenum desc limit 100";
		$tmprs = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($tmprs))
		{
			//staticcomplog
			$complistid = $value['id'];
			$content = serialize($tmprs);
			$addtime = $endtime-100;
			$sql = "insert into staticcomplog(complistid,content,addtime) values ({$complistid},'{$content}',{$addtime})";
			$pdo->exec($sql);
			$isfinish = 0;
			$finishTime = $addtime + 86299;
			if($finishTime > $value['endtime'])
			{
				$isfinish = 1;
			}
			$sql = "update competitionlist set haslist = 1,isfinish={$isfinish} where id = {$value['id']}";
			$pdo->exec($sql);
		}
		else
		{
			continue;
		}
	}
	//链接数据库
	function conDataBase() {
		try {
			$pdo = new PDO('mysql:host=192.168.0.3;dbname=poem','poem','EQJfuZV&Ks2V1f5V');
			$pdo->query('set names utf8');
			return $pdo;
		} catch(Exception $e) {
			return false;
		}
	}
 ?>