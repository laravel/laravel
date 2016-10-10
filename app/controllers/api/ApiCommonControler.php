<?php 
	class ApiCommonController extends Controller {
		protected  $poem_url = '';
		protected $url = '';
		function __construct(){
			$this->poem_url = Config::get('app.poem_url');
			$this->url = Config::get('app.url');
		}
		//私有属性
    	protected $return = array('status'=>'','message'=>'','data'=>null,'hasmore'=>null);
    	//析构函数，在对象消除的时候执行。
	    public function __destruct() {
	        $this->getReturn();
	    }
	    protected function setReturn($status,$message='',$data='',$hasmore='') {
	        $this->return['status'] = $status;
	        $this->return['message'] = $message;
	        $this->return['data'] = $data;
	        $this->return['hasmore']  = $hasmore;
    	}
    	protected function getReturn() {
	        echo json_encode($this->return); 
	        exit;
    	}

	}