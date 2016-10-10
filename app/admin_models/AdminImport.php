<?php

/**
 * 后台导入
 *
 * @author :wang.hongli
 * @since :2015/05/15
 *       
 */
class AdminImport extends AdminCommon {
	public function importPoem($excelPath = '') {
		error_reporting ( 0 );
		set_time_limit ( 0 );
		header ( 'Content-type:text/html;charset=utf-8' );
		date_default_timezone_set ( 'Asia/shanghai' );
		require (app_path ( 'commands/PinYin.php' ));
		// 初始化拼音类
		$pinyin = new Pinyin ();
		// 获取excel表格中的数据
		try {
			$sheetData = $this->getExcData ( $excelPath );
		} catch (Exception $e) {
			$sheetData = '';
		}
		if (empty ( $sheetData ))
			return '请查看excel表格是否上传';
			// 删除excel第一个数组数据
		unset ( $sheetData [1] );
		$excel = new PHPExcel ();
		$objWriter = new PHPExcel_Writer_Excel5 ( $excel );
		
		// 去除数组中空元素
		foreach ( $sheetData as $key => &$value ) {
			if (empty ( $value ['A'] )) {
				unset ( $sheetData [$key] );
			}
		}
		unset ( $value );
		if(empty($sheetData)){
			return '请检测excel表示中是否有数据';
		}
		// 检测所需文件是否存在
		$info = $this->isExists ( $sheetData );
		// 别忘了打开这个注释
		if (true !== $info) {
			$return = '文件不存在';
			if(is_array($info))
			{
				$return .= implode('|', $info);
			}
			return $return;
		}
		// 将写的作者入库--只有一个作者
		$this->importWriter ( $sheetData, $pinyin );
		// 将读的作者入库,分类关系入库
		$this->importReader ( $sheetData, $pinyin );
		// 将歌词入库
		$filePathL =  '/upload/lyric/default/' ;
		$filePathB =  '/upload/poem/default/';
		
		foreach ( $sheetData as $key => $value ) {
			$str = null;
			$allchar = null;
			$tmpPyArr = array ();
			if (empty ( $value ['D'] ))
				continue;
			$lastPathL = $filePathL . $value ['D'] . '.lrc';
			// 歌词id
			if (! $lyricId = $this->importLyric ( $lastPathL ))
				continue;
				// 歌名
			if (empty ( $value ['G'] ))
				continue;
			$name = $value ['G'];
			// 首字母
			$spelling = $pinyin->getFirstChar ( $name );
			$spelling = !empty($spelling) ? strtoupper($spelling) : '';
			// 读作者名字
			if (empty ( $value ['J'] ))
				continue;
			$readername = $value ['J'];
			$readername = str_replace ( '，', ',', $readername );
			$readername = str_replace ( ',', ' ', $readername );
			$readernamespell = $pinyin->getFirstChar ( $readername );
			$readernamespell = !empty($readernamespell) ? strtoupper($readernamespell) : '';
			// 写者名字
			if (empty ( $value ['H'] ))
				continue;
			$writername = $value ['H'];
			$tmpWriterName = str_replace ( '，', ',', $writername );
			$tmpWriterName = str_replace ( ',', ' ', $tmpWriterName );
			$writername = $tmpWriterName;
			$writernamespell = $pinyin->getFirstChar($writername);
			$writernamespell = !empty($writernamespell) ? strtoupper($writernamespell) : '';
			
			// 伴奏地址
			$lastPathB = $filePathB . $value ['C'] . '.mp3';
			// 原诗地址
			$lastPathY = $filePathB . $value ['B'] . '.mp3';
			// 诗长
			$duration = $value ['E'];
			// 是否可编辑0可编辑1不可编辑
			if (empty ( $value ['M'] )) {
				$isedit = 0;
			} else {
				$isedit = 1;
			}
			// sex 0都可以2原始女读1原诗男读
			if (empty ( $value ['L'] )) {
				$sex = 0;
			} else {
				if ('男' == $value ['L']) {
					$sex = 1;
				} elseif ('女' == $value ['L']) {
					$sex = 2;
				}
			}
			if (empty ( $value ['N'] ))
				continue;
			$aliasname = $value ['N'];
			// 全拼首字母
			$allchar = @$pinyin->getPinyin ( $aliasname );
			if(empty($allchar)){
				$allchar = '';
			}
			$str = '';
			if (! empty ( $allchar )) {
				$tmpPyArr = explode ( ' ', $allchar );
				foreach ( $tmpPyArr as $k => $v ) {
					if (empty ( $v )) {
						continue;
					}
					$str .= substr ( $v, 0, 1 );
				}
				$str = !empty($str) ? strtoupper($str) : '';
			}
			$time = time ();
			$poemId = 0;
			try {
				$downnum = mt_rand(5000,10000);
				$poemId = DB::table ( 'poem' )->insertGetId ( array (
						'name' => $name,
						'lyricid' => $lyricId,
						'downnum'=>$downnum,
						'readername' => $readername,
						'writername' => $writername,
						'readerallchar'=>$readernamespell,
						'writerallchar'=>$writernamespell,
						'burl' => $lastPathB,
						'yurl' => $lastPathY,
						'lyricurl' => $lastPathL,
						'duration' => $duration,
						'spelling' => $spelling,
						'allchar' => $str,
						'isedit' => $isedit,
						'sex' => $sex,
						'aliasname' => $aliasname,
						'addtime' => $time
				) );
			} catch (Exception $e) {
			}
			if (! $poemId) {
				continue;
			}
			//将伴奏同步到ES中
			// $apiEsSearch = new ApiEsSearch();
			$apiEsSearch = App::make('apiEsSearch');
			$apiEsSearch->addEsPoem(['id'=>$poemId,'name'=>$name,'aliasname'=>$aliasname,'allchar'=>$str,'readerallchar'=>$readernamespell,'writername'=>$writername,'writerallchar'=>$writernamespell,'spelling'=>$spelling]);
			
			// 将歌曲分类入库
			if (! $this->importNavPoem ( $value ['F'], $poemId ))
				continue;
				// 读者-诗关系表入库
			if (! $this->importReadPoemrel ( $value ['J'], $poemId ))
				continue;
				// 写者-诗关系表入库
			if (! $this->importWriterPoemrel ( $value ['H'], $poemId ))
				continue;
		}
		return true;
	}
	// 获取excel表格中的数据
	public function getExcData($inputFileName) {
		$inputFileType = 'Excel5';
		$objReader = PHPExcel_IOFactory::createReader ( $inputFileType );
		$objPHPExcel = $objReader->load ( $inputFileName );
		$sheetData = $objPHPExcel->getActiveSheet ()->toArray ( null, true, true, true );
		return $sheetData;
	}
	
	// 检测所需文件是否存在
	public function isExists($arr = '') {
		$filePathY = public_path ( 'upload/poem/default/' );
		$filePathB = public_path ( 'upload/poem/default/' );
		$filePathL = public_path ( 'upload/lyric/default/' );
		$returnArr = array ();
		foreach ( $arr as $v ) {
			$y = null;
			$b = null;
			$l = null;
			if (empty ( $v ['B'] ) || empty ( $v ['C'] ) || empty ( $v ['D'] )) {
				return '请检查exls中是否有空元素';
			}
			$y = $filePathY . $v ['B'] . '.mp3';
			$b = $filePathB . $v ['C'] . '.mp3';
			$l = $filePathL . $v ['D'] . '.lrc';
			
			if (! file_exists ( $y ))
				$returnArr [] = $y;
			if (! file_exists ( $b ))
				$returnArr [] = $b;
			if (! file_exists ( $l ))
				$returnArr [] = $l;
		}
		if (empty ( $returnArr )) {
			return true;
		} else {
			return $returnArr;
		}
	}
	// 将写的作者,分类入库
	public function importWriter($arr = '', $pinyin) {
		// 男=1 女=2
		$time = time ();
		foreach ( $arr as $key => $value ) {
			$tmpStr = null;
			$tmpStr2 = null;
			$tmpArr = array ();
			$tmpArr2 = array ();
			$writerid = 0;
			if (empty ( $value ['H'] ) || empty ( $value ['I'] ))
				continue;
			
			if ($value ['H'] == '未知') {
				$firstchar = 'W';
				try {
					$writerid = DB::table ( 'writer' )->insertGetId ( array (
							'name' => '未知',
							'firstchar' => $firstchar,
							'pinyin' => 'wz' 
					) );
				} catch ( Exception $e ) {
				}
				if ($writerid) {
					try {
						DB::table ( 'wprel' )->insert ( array (
								'writerid' => $writerid,
								'poemercatid' => 1,
								'addtime' => $time 
						) );
						DB::table ( 'wprel' )->insert ( array (
								'writerid' => $writerid,
								'poemercatid' => 2,
								'addtime' => $time 
						) );
					} catch ( Exception $e ) {
					}
				} else {
					continue;
				}
			} else {
				// 判断是否为多个人
				if (strpos ( $value ['H'], ',' ) || strpos ( $value ['H'], '，' )) {
					$tmpStr = str_replace ( '，', ',', $value ['H'] );
					$tmpArr = explode ( ',', $tmpStr );
					$tmpStr2 = str_replace ( '，', ',', $value ['I'] );
					$tmpArr2 = explode ( ',', $tmpStr2 );
					if (count ( $tmpArr ) != count ( $tmpArr2 )) {
						echo "{$value['H']} 与 {$value['I']}不对应，程序退出";
						die ();
					}
					foreach ( $tmpArr as $k => $v ) {
						$sex = null;
						$tmpPy = null;
						$tmpPyArr = array ();
						$str = null;
						$firstchar = $pinyin->getFirstChar ( $v );
						$firstchar = !empty($firstchar) ? $firstchar : '';
						$tmpPy = @$pinyin->getPinyin ( $v );
						$str = '';
						if (! empty ( $tmpPy )) {
							$tmpPyArr = explode ( ' ', $tmpPy );
							foreach ( $tmpPyArr as $kk => $vv ) {
								if (empty ( $vv )) {
									continue;
								}
								$str .= substr ( $vv, 0, 1 );
							}
						}
						try {
							$writerid = DB::table ( 'writer' )->insertGetId ( array (
									'name' => $v,
									'firstchar' => $firstchar,
									'pinyin' => $str 
							) );
						} catch ( Exception $e ) {
						}
						
						if ($writerid) {
							if ('男' == $tmpArr2 [$k]) {
								$sex = 1;
							} else {
								$sex = 2;
							}
							try {
								DB::table ( 'wprel' )->insert ( array (
										'writerid' => $writerid,
										'poemercatid' => $sex,
										'addtime' => $time 
								) );
							} catch ( Exception $e ) {
							}
						}
					}
				} else {
					$tmpPy = null;
					$tmpPyArr = array ();
					$firstchar = $pinyin->getFirstChar ( $value ['H'] );
					$firstchar = empty($firstchar) ? '' : $firstchar;
					$tmpPy = @$pinyin->getPinyin ( $value ['H'] );
					$str = '';
					if (! empty ( $tmpPy )) {
						$tmpPyArr = explode ( ' ', $tmpPy );
						foreach ( $tmpPyArr as $kk => $vv ) {
							if (empty ( $vv )) {
								continue;
							}
							$str .= substr ( $vv, 0, 1 );
						}
					}
					try {
						$writerid = DB::table ( 'writer' )->insertGetId ( array (
								'name' => $value ['H'],
								'firstchar' => $firstchar,
								'pinyin' => $str 
						) );
					} catch ( Exception $e ) {
					}
					
					if ($writerid) {
						if ('男' == $value ['I']) {
							$sex = 1;
						} else {
							$sex = 2;
						}
						try {
							DB::table ( 'wprel' )->insert ( array (
									'writerid' => $writerid,
									'poemercatid' => $sex,
									'addtime' => $time 
							) );
						} catch ( Exception $e ) {
						}
					}
				}
			}
		}
	}
	
	// 将读的作者入库,分类关系入库
	public function importReader($arr = '', $pinyin) {
		// 男=1 女=2
		$time = time ();
		foreach ( $arr as $key => $value ) {
			$tmpStr = null;
			$tmpStr2 = null;
			$tmpArr = array ();
			$tmpArr2 = array ();
			if (empty ( $value ['J'] ) || empty ( $value ['K'] ))
				continue;
			
			if ($value ['J'] == '佚名') {
				$firstchar = 'Y';
				try {
					$readerid = DB::table ( 'reader' )->insertGetId ( array (
							'name' => '佚名',
							'firstchar' => $firstchar,
							'pinyin' => 'ym' 
					) );
				} catch ( Exception $e ) {
				}
				
				if ($readerid) {
					try {
						DB::table ( 'rprel' )->insert ( array (
								'readerid' => $readerid,
								1,
								$time 
						) );
						DB::table ( 'rprel' )->insert ( array (
								'readerid' => $readerid,
								2,
								$time 
						) );
					} catch ( Exception $e ) {
					}
				} else {
					continue;
				}
			} else {
				// 判断是否为多个人
				if (strpos ( $value ['J'], ',' ) || strpos ( $value ['J'], '，' )) {
					$tmpStr = str_replace ( '，', ',', $value ['J'] );
					$tmpArr = explode ( ',', $tmpStr );
					$tmpStr2 = str_replace ( '，', ',', $value ['K'] );
					$tmpArr2 = explode ( ',', $tmpStr2 );
					if (count ( $tmpArr ) != count ( $tmpArr2 )) {
						echo "{$value['J']} 与 {$value['K']}不对应，程序退出";
						die ();
					}
					foreach ( $tmpArr as $k => $v ) {
						$sex = null;
						$tmpPy = null;
						$tmpPyArr = array ();
						$str = null;
						$firstchar = @$pinyin->getFirstChar ( $v );
						$firstchar = !empty($firstchar) ? $firstchar : '';
						$tmpPy = $pinyin->getPinyin ( $v );
						$str = '';
						if (! empty ( $tmpPy )) {
							$tmpPyArr = explode ( ' ', $tmpPy );
							foreach ( $tmpPyArr as $kk => $vv ) {
								if (empty ( $vv )) {
									continue;
								}
								$str .= substr ( $vv, 0, 1 );
							}
						}
						try {
							$readerid = DB::table ( 'reader' )->insertGetId ( array (
									'name' => $v,
									'firstchar' => $firstchar,
									'pinyin' => $str 
							) );
						} catch ( Exception $e ) {
						}
						
						if ($readerid) {
							if ('男' == $tmpArr2 [$k]) {
								$sex = 1;
							} else {
								$sex = 2;
							}
							try {
								DB::table ( 'rprel' )->insert ( array (
										'readerid' => $readerid,
										'poemercatid' => $sex,
										'addtime' => $time 
								) );
							} catch ( Exception $e ) {
							}
						}
					}
				} else {
					$tmpPyArr = array ();
					$tmpPy = null;
					$str = null;
					$firstchar = @$pinyin->getFirstChar ( $value ['J'] );
					$firstchar = !empty($firstchar) ? $firstchar : '';
					$tmpPy = @$pinyin->getPinyin ( $value ['J'] );
					$str = '';
					if (! empty ( $tmpPy )) {
						$tmpPyArr = explode ( ' ', $tmpPy );
						foreach ( $tmpPyArr as $kk => $vv ) {
							if (empty ( $vv )) {
								continue;
							}
							$str .= substr ( $vv, 0, 1 );
						}
					}
					try {
						$readerid = DB::table ( 'reader' )->insertGetId ( array (
								'name' => $value ['J'],
								'firstchar' => $firstchar,
								'pinyin' => $str 
						) );
					} catch ( Exception $e ) {
					}
					if ($readerid) {
						if ('男' == $value ['K']) {
							$sex = 1;
						} else {
							$sex = 2;
						}
						try {
							DB::table ( 'rprel' )->insert ( array (
									'readerid' => $readerid,
									'poemercatid' => $sex,
									'addtime' => $time 
							) );
						} catch ( Exception $e ) {
						}
					}
				}
			}
		}
	}
	// 将歌词入库，返回歌词id
	public function importLyric($filePathL = '') {
		$time = time ();
		try {
			$lyricId = DB::table ( 'lyric' )->insertGetId ( array (
					'url' => $filePathL,
					'addtime' => $time 
			) );
		} catch ( Exception $e ) {
			$lyricId = DB::table('lyric')->where('url',$filePathL)->pluck('id');
		}
		
		if ($lyricId) {
			return $lyricId;
		}
		return false;
	}
	
	// 将诗所属分类入库 诗类别，诗id
	public function importNavPoem($navigation, $poemId) {
		if (empty ( $navigation ))
			return false;
		$time = time ();
		if (strpos ( $navigation, ',' ) !== false) {
			$tmpNaviArr = explode ( ',', $navigation );
			foreach ( $tmpNaviArr as $key => $value ) {
				// 判断是否有子分类,有，直接将歌曲插入子类
				if (strpos ( $value, '|' ) !== false) {
					$this->hasSubCategory ( $value, $poemId );
				} else {
					$value = trim ( $value );
					$navId = DB::table ( 'navigation' )->where ( 'category', $value )->pluck ( 'id' );
					if ($navId) {
						try {
							DB::table ( 'navpoemrel' )->insert ( array (
									'navid' => $navId,
									'poemid' => $poemId,
									'addtime' => $time 
							) );
						} catch ( Exception $e ) {
						}
					} else {
						continue;
					}
				}
			}
		} else {
			if (strpos ( $navigation, '|' ) !== false) {
				$this->hasSubCategory ( $navigation, $poemId );
			} else {
				$navId = DB::table ( 'navigation' )->where ( 'category', $navigation )->pluck ( 'id' );
				if (empty ( $navId ))
					return;
				try {
					DB::table ( 'navpoemrel' )->insert ( array (
							'navid' => $navId,
							'poemid' => $poemId,
							'addtime' => $time 
					) );
				} catch ( Exception $e ) {
				}
			}
		}
		return true;
	}
	
	// 读者-诗关系表入库
	public function importReadPoemrel($reader, $poemId) {
		if (empty ( $reader ))
			return false;
		$time = time ();
		if (strpos ( $reader, ',' ) || strpos ( $reader, '，' )) {
			$tmpReader = str_replace ( '，', ',', $reader );
			$tmpReaderArr = explode ( ',', $tmpReader );
			foreach ( $tmpReaderArr as $key => $value ) {
				$readerId = DB::table ( 'reader' )->where ( 'name', $value )->pluck ( 'id' );
				if (empty ( $readerId ))
					continue;
					// 入库readpoemrel
				if (! DB::table ( 'readpoemrel' )->insert ( array (
						'readerid' => $readerId,
						'poemid' => $poemId,
						'addtime' => $time 
				) )) {
					continue;
				}
			}
		} else {
			$tmpRs = array ();
			$readerId = DB::table ( 'reader' )->where ( 'name', $reader )->pluck ( 'id' );
			if (empty ( $readerId ))
				return false;
			if (! DB::table ( 'readpoemrel' )->insert ( array (
					'readerid' => $readerId,
					'poemid' => $poemId,
					'addtime' => $time 
			) )) {
				return false;
			}
		}
		return true;
	}
	
	// 写者-诗关系表入库
	public function importWriterPoemrel($writer, $poemId) {
		if (empty ( $writer ))
			return false;
		$time = time ();
		if (strpos ( $writer, ',' ) || strpos ( $writer, '，' )) {
			$tmpWriter = str_replace ( '，', ',', $writer );
			$tmpWriterArr = explode ( ',', $tmpWriter );
			foreach ( $tmpWriterArr as $key => $value ) {
				$writerId = DB::table ( 'writer' )->where ( 'name', $value )->pluck ( 'id' );
				if (empty ( $writerId ))
					continue;
				if (! DB::table ( 'writepoemrel' )->insert ( array (
						'writerid' => $writerId,
						'poemid' => $poemId,
						'addtime' => $time 
				) )) {
					continue;
				}
			}
		} else {
			$writerId = DB::table ( 'writer' )->where ( 'name', $writer )->pluck ( 'id' );
			if (empty ( $writerId ))
				return false;
			if (! DB::table ( 'writepoemrel' )->insert ( array (
					'writerid' => $writerId,
					'poemid' => $poemId,
					'addtime' => $time 
			) )) {
				return false;
			}
		}
		return true;
	}
	
	// 根据 | 判断出有子类，直接将原创插入子类
	public function hasSubCategory($tmpCat, $poemId) {
		$tmpArr = explode ( '|', $tmpCat );
		if (empty ( $tmpArr ))
			return;
		if (empty ( $tmpArr [0] ))
			return;
		if (empty ( $tmpArr [1] ))
			return;
		
		$parCat = $tmpArr [0];
		$subCat = $tmpArr [1];
		$time = time ();
		// 将诗插入父类下
		$navId = DB::table ( 'navigation' )->where ( 'category', $parCat )->pluck ( 'id' );
		if (empty ( $navId ))
			continue;
			// 将诗插入自子类下
		$navId = DB::table ( 'navigation' )->where ( 'category', $subCat )->pluck ( 'id' );
		if (empty ( $navId ))
			continue;
		try {
			DB::table ( 'navpoemrel' )->insert ( array (
					'navid' => $navId,
					'poemid' => $poemId,
					'addtime' => $time
			) );
		} catch (Exception $e) {
		}
		return;
	}
}