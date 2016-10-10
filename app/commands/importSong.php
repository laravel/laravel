<?php 
	error_reporting(E_ALL);
	set_time_limit(0);
	header('Content-type:text/html;charset=utf-8');
	date_default_timezone_set('Asia/shanghai');
	require('./PinYin.php');
	//初始化拼音类
	$pinyin = new Pinyin();
	if(!$path = checkArguments($argc,$argv)) return '导入失败，请检查参数是否输入正确';

	//获取excel表格中的数据
	$sheetData = getExcData($path);
	if(empty($sheetData)) return '导入失败';
	unset($sheetData[1]);
	//去除数组中空元素
	foreach($sheetData as $key=>&$value) {
		if(empty($value['A'])) {
			unset($sheetData[$key]);
		}
	}
	unset($value);
	//检测所需文件是否存在
	$info = isExists($sheetData);
	//别忘了打开这个注释
	if(true !== $info) {
		var_dump($info);
		return;
	}
	//链接数据库
	if(!$pdo = conDataBase()) return '数据库链接失败';

	//将写的作者入库--只有一个作者
	importWriter($sheetData,$pdo,$pinyin);
	//将读的作者入库,分类关系入库
	importReader($sheetData,$pdo,$pinyin);

	//将歌词入库
	$filePathL = '/upload/lyric/default/';
	$filePathB = '/upload/poem/default/';
	foreach($sheetData as $key=>$value) {
		$str = null;
		$allchar = null;
		$tmpPyArr = array();
		if(empty($value['D'])) continue;
		$lastPathL = $filePathL.$value['D'].'.lrc';
		//歌词id
		if(!$lyricId = importLyric($lastPathL,$pdo)) continue;
		//歌名
		if(empty($value['G'])) continue;
		$name = $value['G'];
		//首字母
		$spelling = $pinyin->getFirstChar($name);
		//读作者名字
		if(empty($value['J'])) continue;
		$readername = $value['J'];
		$readername = str_replace('，', ',', $readername);
		$readername = str_replace(',',' ',$readername);
		//读者性别
		// if(empty($value['K'])) continue;
		// $readersex = $value['K'];

		//写者名字
		if(empty($value['H'])) continue;
		$writername = $value['H'];
		$tmpWriterName = str_replace('，', ',', $writername);
		$tmpWriterName = str_replace(',',' ',$tmpWriterName);
		$writername = $tmpWriterName;

		//写者性别
		// if(empty($value['I'])) continue;
		// $writersex = $value['I'];

		//伴奏地址
		$lastPathB = $filePathB.$value['C'].'.mp3';
		//原诗地址
		$lastPathY = $filePathB.$value['B'].'.mp3';
		//诗长
		$duration = $value['E'];
		//是否可编辑0可编辑1不可编辑
		if(empty($value['M'])) {
			$isedit = 0;
		} else {
			$isedit = 1;
		}
		//sex 0都可以2原始女读1原诗男读
		if(empty($value['L'])) {
			$sex = 0;
		} else {
			if('男' == $value['L']) {
				$sex = 1;
			} elseif('女' == $value['L']) {
				$sex = 2;
			}
		}
		if(empty($value['N'])) continue;
		$aliasname = $value['N'];
		//全拼首字母
		$allchar = @$pinyin->getPinyin($aliasname);
		$str = '';
		if(!empty($tmpPyArr)) {
			$tmpPyArr = explode(' ', $allchar);
			foreach($tmpPyArr as $k=>$v) {
				if(empty($v)) {
	      			continue;
	    		}
	    		$str .= substr($v,0,1);
			}
		}
		
		$time = time();
		$sql = "insert into poem(name,lyricid,readername,writername,burl,yurl,lyricurl,duration,spelling,allchar,isedit,sex,aliasname,addtime) 
				values ('{$name}',{$lyricId},'{$readername}','{$writername}','{$lastPathB}','{$lastPathY}','{$lastPathL}','{$duration}','{$spelling}','{$str}',{$isedit},{$sex},'{$aliasname}',{$time});";
		if(!$pdo->exec($sql)) continue;
		$poemId = $pdo->lastInsertId();
		//将歌曲分类入库
		if(!importNavPoem($value['F'],$poemId,$pdo)) continue;
		//读者-诗关系表入库
		if(!importReadPoemrel($value['J'],$poemId,$pdo)) continue;
		//写者-诗关系表入库
		if(!importWriterPoemrel($value['H'],$poemId,$pdo)) continue;	

	}

	//用户输入参数是否正确检测
	function checkArguments($argc,$argv) {
		if(empty($argc)) return false;
		if($argc != 2) return false;
		$pattern = '/^[0-9]*\.xls$/';
		if(empty($argv) || $argv[0] != 'importSong.php' || !preg_match($pattern,$argv[1])) return false;
		return $argv[1];
	}

	//获取excel表格中的数据
	function getExcData($inputFileName) {
		set_include_path('./../../vendor/phpoffice/phpexcel/Classes/');
		include 'PHPExcel/IOFactory.php';
		$inputFileType = 'Excel5';
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$inputFileName = './'.$inputFileName;
		$objPHPExcel = $objReader->load($inputFileName);
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		return $sheetData;
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

	//检测所需文件是否存在
	function isExists($arr = '') {
		$filePathY = './../../public/upload/poem/default/';
		$filePathB = './../../public/upload/poem/default/';
		$filePathL = './../../public/upload/lyric/default/';
		$returnArr = array();
		foreach($arr as $v) {
			$y = null;
			$b = null;
			$l = null;
			if(empty($v['B']) || empty($v['C']) || empty($v['D'])) return '请检查exls中是否有空元素';

			$y = $filePathY.$v['B'].'.mp3';
			$b = $filePathB.$v['C'].'.mp3';
			$l = $filePathL.$v['D'].'.lrc';

			if(!file_exists($y)) 
				$returnArr[] = $y;
			if(!file_exists($b))
				$returnArr[] = $b;
			if(!file_exists($l)) 
				$returnArr[] = $l;
		}
		if(empty($returnArr)) {
			return true;
		} else {
			return $returnArr;
		}
	}

	//将写的作者,分类入库
	function importWriter($arr = '',$pdo,$pinyin) {
		//男=1 女=2		
		$time = time();
		foreach($arr as $key=>$value) {
			$tmpStr = null;
			$tmpStr2 = null;
			$tmpArr = array();
			$tmpArr2 = array();
			if(empty($value['H']) || empty($value['I'])) continue;

			if($value['H'] == '未知') {
				$firstchar = 'W';
				$sql = "insert into writer (name,firstchar,pinyin) values ('未知','{$firstchar}','wz');";
				if($pdo->exec($sql)) {
					$writerid = $pdo->lastInsertId();
					$sql1 = "insert into wprel (writerid,poemercatid,addtime) values({$writerid},1,{$time})";
					$sql2 = "insert into wprel (writerid,poemercatid,addtime) values({$writerid},2,{$time})";
					$pdo->exec($sql1);
					$pdo->exec($sql2);
				} else {
					continue;
				}
			} else {
				//判断是否为多个人
				if(strpos($value['H'], ',') || strpos($value['H'],'，')) {
					$tmpStr = str_replace('，', ',', $value['H']);
					$tmpArr = explode(',', $tmpStr);
					$tmpStr2 = str_replace('，', ',', $value['I']);
					$tmpArr2 = explode(',',$tmpStr2);
					if(count($tmpArr) != count($tmpArr2)) {
						echo "{$value['H']} 与 {$value['I']}不对应，程序退出";
						die;
					}
					foreach($tmpArr as $k=>$v) {
						$sex = null;
						$tmpPy = null;
						$tmpPyArr = array();
						$str = null;
						$firstchar = $pinyin->getFirstChar($v);
						$tmpPy = @$pinyin->getPinyin($v);
						$str = '';
						if(!empty($tmpPyArr)) {
							$tmpPyArr = explode(' ', $tmpPy);
							foreach($tmpPyArr as $kk=>$vv) {
								if(empty($vv)) {
					      			continue;
					    		}
					    		$str .= substr($vv,0,1);
							}
						}
						$sql = "insert into writer (name,firstchar,pinyin) values ('{$v}','{$firstchar}','{$str}')";
						if($pdo->exec($sql)) {
							$writerid = $pdo->lastInsertId();
							if('男' == $tmpArr2[$k]) {
								$sex = 1;
							} else {
								$sex = 2;
							}
							$sql = "insert into wprel(writerid,poemercatid,addtime) values({$writerid},{$sex},{$time});";
							$pdo->exec($sql);
						}
					}
				} else {
					$firstchar = $pinyin->getFirstChar($value['H']);
					$tmpPy = null;
					$tmpPyArr = array();
					$str = null;
					$firstchar = $pinyin->getFirstChar($value['H']);
					$tmpPy = @$pinyin->getPinyin($value['H']);
					$str = '';
					if(!empty($tmpPyArr)) {
						$tmpPyArr = explode(' ', $tmpPy);
						foreach($tmpPyArr as $kk=>$vv) {
							if(empty($vv)) {
				      			continue;
				    		}
				    		$str .= substr($vv,0,1);
						}
					}
					$sql = "insert into writer(name,firstchar,pinyin) values ('{$value['H']}','{$firstchar}','{$str}');";
					if($pdo->exec($sql)) {
						$writerid = $pdo->lastInsertId();
						if('男' == $value['I']) {
							$sex = 1;
						} else {
							$sex = 2;
						}
						$sql = "insert into wprel(writerid,poemercatid,addtime) values({$writerid},{$sex},{$time});";
						$pdo->exec($sql);
					}
				}
			}
		}
	}

	//将读的作者入库,分类关系入库
	function importReader($arr = '',$pdo,$pinyin) {
		//男=1 女=2		
		$time = time();
		foreach($arr as $key=>$value) {
			$tmpStr = null;
			$tmpStr2 = null;
			$tmpArr = array();
			$tmpArr2 = array();
			if(empty($value['J']) || empty($value['K'])) continue;

			if($value['J'] == '佚名') {
				$firstchar = 'Y';
				$sql = "insert into reader (name,firstchar,pinyin) values ('佚名','{$firstchar}','ym');";
				if($pdo->exec($sql)) {
					$readerid = $pdo->lastInsertId();
					$sql1 = "insert into rprel (readerid,poemercatid,addtime) values({$readerid},1,{$time})";
					$sql2 = "insert into rprel (readerid,poemercatid,addtime) values({$readerid},2,{$time})";
					$pdo->exec($sql1);
					$pdo->exec($sql2);
				} else {
					continue;
				}
			} else {
				//判断是否为多个人
				if(strpos($value['J'], ',') || strpos($value['J'],'，')) {
					$tmpStr = str_replace('，', ',', $value['J']);
					$tmpArr = explode(',', $tmpStr);
					$tmpStr2 = str_replace('，', ',', $value['K']);
					$tmpArr2 = explode(',',$tmpStr2);
					if(count($tmpArr) != count($tmpArr2)) {
						echo "{$value['J']} 与 {$value['K']}不对应，程序退出";
						die;
					}
					foreach($tmpArr as $k=>$v) {
						$sex = null;
						$tmpPy = null;
						$tmpPyArr = array();
						$str = null;
						$firstchar = @$pinyin->getFirstChar($v);
						$tmpPy = $pinyin->getPinyin($v);
						$str = '';
						if(!empty($tmpPyArr)) {
							$tmpPyArr = explode(' ', $tmpPy);
							foreach($tmpPyArr as $kk=>$vv) {
								if(empty($vv)) {
					      			continue;
					    		}
					    		$str .= substr($vv,0,1);
							}
						}
						$sql = "insert into reader (name,firstchar,pinyin) values ('{$v}','{$firstchar}','{$str}')";
						if($pdo->exec($sql)) {
							$readerid = $pdo->lastInsertId();
							if('男' == $tmpArr2[$k]) {
								$sex = 1;
							} else {
								$sex = 2;
							}
							$sql = "insert into rprel(readerid,poemercatid,addtime) values({$readerid},{$sex},{$time});";
							$pdo->exec($sql);
						}
					}
				} else {
					$tmpPyArr = array();
					$tmpPy = null;
					$str = null;
					$firstchar = @$pinyin->getFirstChar($value['J']);
					$tmpPy = @$pinyin->getPinyin($value['J']);
					$str = '';
					if(!empty($tmpPy)) {
						$tmpPyArr = explode(' ', $tmpPy);
						foreach($tmpPyArr as $kk=>$vv) {
							if(empty($vv)) {
				      			continue;
				    		}
				    		$str .= substr($vv,0,1);
						}
					}
					$sql = "insert into reader(name,firstchar,pinyin) values ('{$value['J']}','{$firstchar}','{$str}');";
					if($pdo->exec($sql)) {
						$readerid = $pdo->lastInsertId();
						if('男' == $value['K']) {
							$sex = 1;
						} else {
							$sex = 2;
						}
						$sql = "insert into rprel(readerid,poemercatid,addtime) values({$readerid},{$sex},{$time});";
						$pdo->exec($sql);
					}
				}
			}
		}
	}

	//将歌词入库，返回歌词id
	function importLyric($filePathL = '',$pdo) {
		$time = time();
		$sql = "insert into lyric (url,addtime) values ('{$filePathL}',{$time})";
		if($pdo->exec($sql)) {
			$lyricId = $pdo->lastInsertId();
			return $lyricId;
		}
		return false;
	}

	//将诗所属分类入库 诗类别，诗id
	function importNavPoem($navigation,$poemId,$pdo) {
		if(empty($navigation)) return false;
		$time = time();
		if(strpos($navigation, ',') !== false) {
			$tmpNaviArr = explode(',', $navigation);
			foreach($tmpNaviArr as $key=>$value) {
				echo $value;
				//判断是否有子分类,有，直接将歌曲插入子类
				if(strpos($value,'|') !== false) {
					hasSubCategory($value,$poemId,$pdo);
				} else {
					$tmpRs = array();
					$value = trim($value);
					$sql = "select id from navigation where category ='{$value}'";
					$statement = $pdo->query($sql);
					$tmpRs = $statement->fetch(PDO::FETCH_ASSOC);
					if($tmpRs) {
						$navId = $tmpRs['id'];
						$sql = "insert into navpoemrel (navid,poemid,addtime) values({$navId},{$poemId},{$time})";
						$pdo->exec($sql);
					} else {
						continue;
					}
				}
			}
		} else {
			if(strpos($navigation,'|') !== false) {
				hasSubCategory($navigation,$poemId,$pdo);
			} else {
				$sql = "select id from navigation where category = '{$navigation}'";
				$statement = $pdo->query($sql);
				$tmpRs = $statement->fetch(PDO::FETCH_ASSOC);
				if(empty($tmpRs)) return;
				$navId = $tmpRs['id'];
				$sql = "insert into navpoemrel (navid,poemid,addtime) values({$navId},{$poemId},{$time})";
				$pdo->exec($sql);
			}
		}
		return true;
	}
	//读者-诗关系表入库
	function importReadPoemrel($reader,$poemId,$pdo) {
		if(empty($reader)) return false;
		$time = time();
		if(strpos($reader,',') || strpos($reader,'，')) {
			$tmpReader = str_replace('，',',',$reader);
			$tmpReaderArr = explode(',',$tmpReader);
			foreach($tmpReaderArr as $key=>$value) {
				$tmpRs = array();
				$sql = "select id from reader where name='{$value}'";
				$statement = $pdo->query($sql);
				$tmpRs = $statement->fetch(PDO::FETCH_ASSOC);
				if(empty($tmpRs)) continue;
				$readerId = $tmpRs['id'];
				//入库readpoemrel
				$sql = "insert into readpoemrel (readerid,poemid,addtime) values({$readerId},{$poemId},{$time});";
				if(!$pdo->exec($sql)) continue;
			}
		} else {
			$tmpRs = array();
			$sql = "select id from reader where name = '{$reader}'";
			$statement = $pdo->query($sql);
			$tmpRs = $statement->fetch(PDO::FETCH_ASSOC);
			if(empty($tmpRs)) return false;
			$readerId = $tmpRs['id'];
			$sql = "insert into readpoemrel (readerid,poemid,addtime) values({$readerId},{$poemId},{$time});";
			if(!$pdo->exec($sql)) 
				return false;
		}
		return true;
	}

	//写者-诗关系表入库
	function importWriterPoemrel($writer,$poemId,$pdo) {
		if(empty($writer)) return false;
		$time = time();
		if(strpos($writer, ',') || strpos($writer,'，')) {
			$tmpWriter = str_replace('，',',',$writer);
			$tmpWriterArr = explode(',',$tmpWriter);
			foreach($tmpWriterArr as $key=>$value) {
				$tmpRs = array();
				$sql = "select id from writer where name = '{$value}'";
				$statement = $pdo->query($sql);
				$tmpRs = $statement->fetch(PDO::FETCH_ASSOC);
				if(empty($tmpRs)) continue;
				$writerId = $tmpRs['id'];
				$sql = "insert into writepoemrel (writerid,poemid,addtime) values ({$writerId},{$poemId},{$time});";
				if(!$pdo->exec($sql)) continue;
			}
		} else {
			$tmpRs = array();
			$sql = "select id from writer where name = '{$writer}'";
			$statement = $pdo->query($sql);
			$tmpRs = $statement->fetch(PDO::FETCH_ASSOC);
			if(empty($tmpRs)) return false;
			$writerId = $tmpRs['id'];
			$sql = "insert into writepoemrel(writerid,poemid,addtime) values({$writerId},{$poemId},{$time});";
			if(!$pdo->exec($sql)) 
				return false;
		}
		return true;
	}

	//根据 | 判断出有子类，直接将原创插入子类
	function hasSubCategory($tmpCat,$poemId,$pdo) {
		$tmpArr = explode('|', $tmpCat);
		if(empty($tmpArr)) return;
		if(empty($tmpArr[0])) return;
		if(empty($tmpArr[1])) return;
		
		$parCat = $tmpArr[0];
		$subCat = $tmpArr[1];
		$time = time();
		$tmpRs1 = array();
		$tmpRs2 = array();
		//将诗插入父类下
		$sql = "select id from navigation where category = '{$parCat}'";
		$statement = $pdo->query($sql);
		$tmpRs1 = $statement->fetch(PDO::FETCH_ASSOC);
		if(empty($tmpRs1)) continue;
		$navId = $tmpRs1['id'];
		$sql = "insert into navpoemrel (navid,poemid,addtime) values({$navId},{$poemId},{$time})";
		$pdo->exec($sql);
		//将诗插入自子类下
		$sql = "select id from navigation where category = '{$subCat}'";
		$statement = $pdo->query($sql);
		$tmpRs2 = $statement->fetch(PDO::FETCH_ASSOC);
		if(empty($tmpRs2)) continue;
		$navId = $tmpRs2['id'];
		$sql = "insert into navpoemrel (navid,poemid,addtime) values({$navId},{$poemId},{$time})";
		$pdo->exec($sql);
		return;

	}
 ?>