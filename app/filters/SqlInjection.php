<?php

/**
 * sql注入检测过滤器
 * @author wanghongli
 * @since:2016/04/01
 *
 */
class SqlInjection {
	public function checkInputFilter($request) {
			//对用户GET，POST的值进行检测
			$getfilter="'|(and|or)\\b.+?(>|<|=|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
			$postfilter="\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
			$cookiefilter="\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
			if(!empty($_GET)){
				foreach($_GET as $k=>$v){
					if($this->checkSql($v, $getfilter)){
						return true;
					}
				}
			}
			if(!empty($_POST)){
				foreach($_POST as $k=>$v){
					if($this->checkSql($v, $postfilter)){
						return true;
					}
				}
			}
			if(!empty($_COOKIE)){
				foreach($_COOKIE as $k=>$v){
					if($this->checkSql($v, $cookiefilter)){
						return true;
					}
				}
			}
			return;
	}
	
	protected function checkSql($strFiltValue,$arrFiltReq){
		if(is_array($strFiltValue))
		{
			$strFiltValue = implode($strFiltValue);
		}
		if(preg_match('/'.$arrFiltReq.'/is', $strFiltValue) == 1){
			return true;
		}
	}
}