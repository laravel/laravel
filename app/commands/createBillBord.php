<?php 
/**
*	生成周榜，月榜，年榜
*	@author:wang.hongli
*	@since:2014/11/16
*	每周第一天，每月第一天，每年第一天执行 -120秒
*	主播大赛周榜，月榜，年榜
**/
header('Content-type:text/html;chsrset=utf8');
$pdo = conDataBase();
if(!$pdo)
{
	die('数据库连接失败');
}

$addTime = time()-3600; //添加时间 inserttime
//type 1周2月3年
//flag 0 作品 1 主播
$type = !empty($argv[1]) ? $argv[1] : 1; //默认周
$flag = !empty($argv[2]) ? $argv[2] : 0; //默认作品
$author = $argv[3];
if(empty($author) || $author != 'shoushan@2013')
{
	die('error');
}
$sql = "insert into subbillnav (type,inserttime,flag) values ($type,$addTime,$flag)";
$pdo->exec($sql);
$sid = $pdo->lastinsertid();
//存储路径
$path = '/www/poem/public/upload/billbord/';
//作品
if(empty($flag))
{	
	switch ($type) {
		case 1:
			$sql = "select opus.id,opus.uid,opus.commentnum,opus.poemid,opus.name,opus.url,opus.lyricurl,opus.type,opus.firstchar,opus.praisenum,opus.lnum-opus.preweeknum as totalNum,opus.repostnum,opus.addtime,opus.opustime,user.nick,user.gender,user.grade,user.sportrait,user.authtype,user.isleague from user left join opus on user.id = opus.uid where opus.isdel = 0 and user.isdel != 1 order by totalNum desc,opus.repostnum desc, opus.lnum desc limit 100";
			$sql2 = "update opus set preweeknum = lnum";
			break;
		case 2:
			$sql = "select opus.id,opus.uid,opus.commentnum,opus.poemid,opus.name,opus.url,opus.lyricurl,opus.type,opus.firstchar,opus.praisenum,opus.lnum-opus.premonthnum as totalNum,opus.repostnum,opus.addtime,opus.opustime,user.nick,user.gender,user.grade,user.sportrait,user.authtype,user.isleague from user left join opus on user.id = opus.uid where opus.isdel = 0 and user.isdel != 1 order by totalNum desc,opus.repostnum desc, opus.lnum desc limit 100";
			$sql2 = "update opus set premonthnum = lnum";
			break;
		case 3:
			$sql = "select opus.id,opus.uid,opus.commentnum,opus.poemid,opus.name,opus.url,opus.lyricurl,opus.type,opus.firstchar,opus.praisenum,opus.lnum-opus.preyearnum as totalNum,opus.repostnum,opus.addtime,opus.opustime,user.nick,user.gender,user.grade,user.sportrait,user.authtype,user.isleague from user left join opus on user.id = opus.uid where opus.isdel = 0 and user.isdel != 1 order by totalNum desc,opus.repostnum desc, opus.lnum desc limit 100";
			$sql2 = "update opus set preyearnum = lnum";
			break;
	}
}
//主播
else
{
	switch ($type) {
		case 1:
			$sql = "select id,nick,phone,gender,lnum,repostnum,attention,praisenum-preweeknum as totalNum ,fans,opusnum,grade,sportrait,portrait,albums,signature,authtype,opusname,isedit,issex,user.addtime,bgpic from  user where user.isdel != 1 order by totalNum desc,user.lnum desc,user.repostnum desc limit 98";
			$sql2 = "update user set preweeknum = praisenum";
			break;
		case 2:
			$sql = "select id,nick,phone,gender,lnum,repostnum,attention,praisenum-premonthnum as totalNum,  fans,opusnum,grade,sportrait,portrait,albums,signature,authtype,opusname,isedit,issex,user.addtime,bgpic from  user where user.isdel != 1 order by totalNum desc,user.lnum desc,user.repostnum desc limit 98";
			$sql2 = "update user set premonthnum = praisenum";
			break;
		case 3:
			$sql = "select id,nick,phone,gender,lnum,repostnum,attention,praisenum-preyearnum as totalNum,fans,opusnum,grade,sportrait,portrait,albums,signature,authtype,opusname,isedit,issex,user.addtime,bgpic from  user where user.isdel != 1 order by totalNum desc,user.lnum desc,user.repostnum desc limit 98";
			$sql2 = "update user set preyearnum = praisenum";
			break;
	}
}

$data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
//生成奖状
if(!empty($data))
{	
	$sql = "insert into diploma (uid,sort,flag,type,addtime) values ";
	$str = '';
	$i = 1;
	foreach($data as $k=>$v)
	{	
		if($i>10) 
		{
			break; 
		}
		//作品
		if(empty($flag))
		{
			$str .= '('.$v['uid'].','.$i.','.$flag.','.$type.','.$addTime.'),';
		}
		else
		{
			$str .= '('.$v['id'].','.$i.','.$flag.','.$type.','.$addTime.'),';
		}
		$i++;
	}
	$str = rtrim($str,',');
	$sql = $sql.$str;
	$pdo->exec($sql);
}
if(!empty($data))
{
	$filePath = $path.$sid.'.txt';
	$data = serialize($data);
	file_put_contents($filePath, $data);
	//将前周，月，年，作品，主播赞数清零
	$pdo->exec($sql2);

}
/**
*	链接数据库
*	@author:wang.hongli
*	@since:2014/11/16
**/
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