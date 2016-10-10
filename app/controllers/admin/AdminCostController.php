<?php
/**
* 活跃度
*/
 class AdminCostController extends BaseController{

    private $AdminPay = null;
    public function __construct()
    {
        $this->AdminPay = new AdminPay;
    }
     //提现页面
     public function cost(){
        $username=Input::get('username',0);
        $input_startime = Input::get('starttime',0);
        $input_endtime = Input::get('endtime',0);
        $pay_type = Input::get('pay_type',0);
        $starttime = strtotime($input_startime);
        $endtime = strtotime($input_endtime);
        $conn=DB::table('weixin_cash');
        $conn->leftJoin('user', 'user.id', '=', 'weixin_cash.uid');
        $conn->select('weixin_cash.*','user.nick');
        if($starttime && $endtime){
            $conn = $conn->where('weixin_cash.time','>=',$starttime);
            $conn = $conn->where('weixin_cash.time','<=',$endtime+86399);
        }elseif($starttime) {
            $conn = $conn->where('weixin_cash.time','>=',$starttime);
        }elseif($endtime){
            $conn = $conn->where('weixin_cash.time','<=',$endtime+86399);
        }elseif($input_startime == '开始时间' && $input_endtime == '结束时间'){
            $input_startime = 0;
            $input_endtime = 0;
        }
        if($pay_type !='' && $pay_type != -1){
            $conn = $conn->where('weixin_cash.flag',$pay_type);
        }
        if(!empty( $username) && $username!=''){
            $conn = $conn->where('nick','like','%'.$username.'%');
        }
        $count = $conn->count();
        $list = $conn->orderBy('time','desc')->paginate(10);

        if(!empty($list) && $list!=''){
            $list_info=[];
            foreach($list as $k=>$v){
                $list_info[$k]['id']=$v['id'];
                $list_info[$k]['num']=$v['num'];
                $list_info[$k]['uid']=$v['uid'];
                $list_info[$k]['flag']=$v['flag'];
                $list_info[$k]['nick']=$v['nick'];
                $list_info[$k]['type']=$v['flag']?'提现成功':'提现申请中';
                $list_info[$k]['time']=date('Y-m-d H:i:s',$v['time']);
            }
        }
        return  View::make('cost.list')->with('list',$list_info)->with('page',$list)->with('status',1)->with('pay_type',$pay_type)->with('endtime',$input_endtime)->with('starttime',$input_startime)->with('username',$username)->with('count',$count);
     }

     public  function givemoney(){
        $id=Input::get('id',0);
        if($id==0 || $id==''){
            $this->message( -1, 'id 为空');
        }  
        $conn=DB::table('weixin_cash');
        $conn->leftJoin('user', 'user.id', '=', 'weixin_cash.uid');
        $conn->leftJoin('weixin_appuser', 'weixin_appuser.uid', '=', 'weixin_cash.uid');
        $conn->leftJoin('weixin_openid', 'weixin_appuser.unionid', '=', 'weixin_openid.unionid');
        $conn->select('user.real_name','weixin_cash.uid','weixin_cash.num','weixin_openid.openid','weixin_cash.time','weixin_cash.id');
        $conn = $conn->where('weixin_cash.id',$id);
        $user_info = $conn->first();
        //发起提现转账
        $data =  array('re_user_name'=>$user_info['real_name'],'amount' =>$user_info['num'],'openid' =>$user_info['openid']  ,'orderid' => "WX".$user_info['id']."T".$user_info['time']);
        $arr = array(
            'uid' => $user_info['uid'],
            'cash_id'=>$user_info['id'],
            'amount'=> $user_info['num'],
            'create_time' => time(),
            'status'=>2
        );
        DB::table('weixin_cash_log')->insertGetId($arr);
        $apiweixinmoney = $this->AdminPay->tengXun($data);
        if($apiweixinmoney['code']==-1){
            $arr = array(
                'uid' => $user_info['uid'],
                'cash_id'=>$user_info['id'],
                'amount'=> $user_info['num'],
                'create_time' => time(),
                'status'=>2
            );
            DB::table('weixin_cash_log')->insertGetId($arr);
            $this->message(-1, '转账失败');
        }else{
            $arr = array(
                'uid' => $user_info['uid'],
                'cash_id'=>$user_info['id'],
                'amount'=> $user_info['num'],
                'create_time' => time(),
                'status'=>1
            );
            DB::table('weixin_cash_log')->insertGetId($arr);
            DB::table('weixin_cash')->where('id',$id)->update(array('flag'=>1));
            $this->message(1, '转账成功');
        }
    }
    public function message($code=1, $msg=''){
        $arr = array ('code' => $code, 'msg'=> $msg);
        echo json_encode($arr);
        die();
    }

}