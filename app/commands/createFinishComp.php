<?php 
	ini_set('date.timezone','PRC');
	//静态榜单生成 每天执行一次
	if(!$pdo = conDataBase())
	{
		die('数据库连接失败');
	}
	// $endtime = strtotime(date('Y-m-d H:i:s',time()))-10;
	// $sql = "select id,endtime from competitionlist where monthflag = 0 and isfinish=0 and endtime <= {$endtime}";	
	$sql = "select id,endtime from competitionlist where id=36 limit 1";
	$statement = $pdo->query($sql);
	$tmpRs = $statement->fetch(PDO::FETCH_ASSOC);
	if(empty($tmpRs))
	{
		die('没有要生成的数据');
	}
	$sql = "select opus.id,opus.uid,opus.commentnum,opus.poemid,opus.name,opus.url,opus.lyricurl,opus.type,opus.firstchar,opus.lnum,opus.lnum as premonthnum,opus.repostnum,opus.praisenum,opus.addtime,opus.opustime,user.nick,user.gender,user.grade,user.sportrait,user.authtype,user.isleague from competition_opus_rel left join opus on competition_opus_rel.opusid = opus.id left join user on user.id = opus.uid where competition_opus_rel.competitionid =36 and opus.isdel = 0 and user.isdel != 1 order by lnum desc,opus.repostnum desc, opus.praisenum desc limit 100";
	// $sql = "select opus.id,opus.uid,opus.commentnum,opus.poemid,opus.name,opus.url,opus.lyricurl,opus.type,opus.firstchar,opus.lnum,opus.premonthlnum,opus.lnum,opus.repostnum,opus.praisenum,opus.addtime,opus.opustime,user.nick,user.gender,user.grade,user.sportrait,user.authtype from competition_opus_rel left join opus on competition_opus_rel.opusid = opus.id left join user on user.id = opus.uid where competition_opus_rel.competitionid = {$value['id']} and opus.isdel = 0 and user.isdel != 1 order by monlnum desc,opus.repostnum desc, opus.praisenum desc limit 100";
	$tmprs = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	if(!empty($tmprs))
	{
		//staticcomplog
		$complistid = $tmpRs['id'];
		$endtime = $tmpRs['endtime'];
		$content = serialize($tmprs);
		$addtime = $endtime-99;
		$sql = "insert into staticcomplog(complistid,content,addtime) values ('{$complistid}','{$content}',{$addtime})";
		$pdo->exec($sql);
		$isfinish = 1;
		$sql = "update competitionlist set haslist = 1,isfinish={$isfinish} where id = 36";
		$pdo->exec($sql);
	}
	//链接数据库
	function conDataBase() {
		try {
			// $pdo = new PDO('mysql:host=localhost;dbname=poem','root','poemproject@2014');
			$pdo = new PDO('mysql:host=192.168.0.3;dbname=poem','poem','EQJfuZV&Ks2V1f5V');
			$pdo->query('set names utf8');
			return $pdo;
		} catch(Exception $e) {
			return false;
		}
	}
 ?>