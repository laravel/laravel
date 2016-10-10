<?php
/**
* 活跃度
*/
 class AdminOrderController extends BaseController{
       public  function OrderList(){
           //每页数量
            $pagesize=20;

            //获取所有省份
            $data['allprovince'] = ApiCity::getAllProvince();
            //获取所有城市
            $data['allcity'] = ApiCity::getAllCity();
            //获取所有地区
            $data['allarea'] = ApiCity::getAllArea();
            
            //筛选传值
            $plat_from=Input::has('plat_from')?Input::get('plat_from'):-1;
            $pay_type=Input::has('pay_type')?Input::get('pay_type'):0;
            $good=Input::has('good')?trim(Input::get('good')):"";
            $province_id=Input::has('province_id')?Input::get('province_id'):"";
            $city_id =Input::has('city_id')?Input::get('city_id'):"";
            $area_id=Input::has('area_id')?Input::get('area_id'):"";
            $orderid=Input::has('orderid')?trim(Input::get('orderid')):"" ;
            $nick=Input::has('nick')?trim(Input::get('nick')):"" ;
            $starttime=Input::has('starttime')?trim(Input::get('starttime')):"" ;
            $endtime=Input::has('endtime')?trim(Input::get('endtime')):"" ;
            $buyname=Input::has('buyname')?trim(Input::get('buyname')):"" ;
            $send=Input::has('send')?trim(Input::get('send')):"-1sss" ;


            $goods=DB::table('goods')->whereIn('flag',array(3,4,5))->where('isdel',0)->get();
            
          
            $goodsid=[];
            $good_name="";
           foreach($goods as $k=>$v){
               $goodsid[]=$v['id'];
               $good_name[$v['id']]=$v['name'];

           }
           try{
            $order=DB::table('order_list')->where('status',2);
             if($orderid){
                 $orderid="%".$orderid."%";
                $order->where('orderid','like',$orderid); 
            }
            if($province_id){
                 $buy_province_id=DB::table('user_address')->where('province_id',$province_id)->get();
                 $buy_province_ids=[];
                 foreach($buy_province_id as $k=>$v){
                    $buy_province_ids[]=$v['id'];
                      
                 }
                 $order->whereIn('address_id',$buy_province_ids); 
            }
            if($city_id){
                 $buy_city_id=DB::table('user_address')->where('province_id',$province_id)->where('city_id',$city_id)->get();
                 $city_ids=[];
                 foreach($buy_city_id as $k=>$v){
                    $city_ids[]=$v['id'];
                     
                 }
                  $order->whereIn('address_id',$city_ids); 
            }
            if($area_id){
                 $buy_area_id=DB::table('user_address')->where('province_id',$province_id)->where('city_id',$city_id)->where('area_id',$area_id)->get();
                 $area_ids=[];
                 foreach($buy_area_id as $k=>$v){
                    $area_ids[]=$v['id'];
                     
                 }
                  $order->whereIn('address_id',$area_ids); 
            }
 
          
            if($buyname){
                $buy=DB::table('user_address')->where('name',$buyname)->first();
                 $order->where('address_id',$buy['id']); 
            }
             if($good){
                 $good_search=DB::table('goods')->where('name',$good)->first();
                $order->where('goods_id',$good_search['id']);
            }
            if($plat_from != -1){
                $order->where('plat_from',$plat_from);
            }
            if($nick){
                 $user_search=DB::table('user')->where('nick',$nick)->first();
                  $order->where('uid',$user_search['id']);
            }
            if($starttime){
                  $order->where('updatetime',">=",strtotime($starttime));
            }
            if($endtime){     
                  $order->where('updatetime',"<=",strtotime($endtime));
                 
            }
            if($send != -1){
                 $order->where('send_out',$send); 
            }
             if($pay_type){
                $order->where('pay_type',$pay_type);
            }
            
            $order_list= $order->whereIn('goods_id',$goodsid)->paginate($pagesize);
            if($order_list){
            $uids=[];
            $address=[];
            foreach($order_list as $k=>$v){
                $uids[]=$v['uid'];
                $address[]=$v['address_id'];
            }
            $users=DB::table('user')->whereIn('id',$uids)->get();
            $users_info="";
            foreach($users as $k=>$v){
                $users_info[$v['id']]=$v['nick'];
           
            }
            $address_info="";
            $address=DB::table('user_address')->whereIn('id',$address)->get();
                 
            foreach($address as $k=>$v){
            $address_info[$v['id']]['province']=$v['province_id']?$data['allprovince'][$v['province_id']]:'';
			$address_info[$v['id']]['city']=$v['city_id']?$data['allcity'][$v['province_id']][$v['city_id']]:"";
			$address_info[$v['id']]['area']=$v['area_id']?$data['allarea'][$v['city_id']][$v['area_id']]:"";
            $address_info[$v['id']]['name']=$v['name'];
            $address_info[$v['id']]['address']=$v['address'];
            $address_info[$v['id']]['tel']=$v['tel'];
            }
            }else{
                 $address_info="";$users_info="";
            }
          	} catch (Exception $e) {
                  $order_list="";
                $address_info="";$users_info="";
           }
       

            return View::make('AppOrderList.orderlist')->with('good_name',$good_name)->with('address_info',$address_info)->with('order_list',$order_list)
            ->with('pay_type',$pay_type)->with('plat_from',$plat_from)->with('users_info',$users_info)->with('allprovince',$data['allprovince'])
            ->with('allcity',$data['allcity'])->with('allarea', $data['allarea'])->with('province_id', $province_id)->with('city_id', $city_id )
            ->with('area_id', $area_id )->with('orderid', trim($orderid,'%') )->with('nick', $nick)->with('good', $good)
            ->with('starttime', $starttime)->with('endtime', $endtime)->with('buyname', $buyname)->with('send', $send);
       }

       public function changeOrderList(){
            $id=Input::get('id');
            $flag=Input::get('flag');
            if($flag==1){
                $rs=DB::table('order_list')->where('id',$id)->update(array('send_out'=>0));
                if($rs){
                    echo  true;
                }else{
                    echo  'error';
                }
            }else{
                $rs=DB::table('order_list')->where('id',$id)->update(array('send_out'=>1));
                if($rs){
                    echo  true;
                }else{
                    echo  'error';
                }
            }

       }



    public function execlOrderList(){

                    //获取所有省份
            $data['allprovince'] = ApiCity::getAllProvince();
            //获取所有城市
            $data['allcity'] = ApiCity::getAllCity();
            //获取所有地区
            $data['allarea'] = ApiCity::getAllArea();
            
            //筛选传值
            $plat_from=Input::has('plat_from')?Input::get('plat_from'):-1;
            $pay_type=Input::has('pay_type')?Input::get('pay_type'):0;
            $good=Input::has('good')?trim(Input::get('good')):"";
            $province_id=Input::has('province_id')?Input::get('province_id'):"";
            $city_id =Input::has('city_id')?Input::get('city_id'):"";
            $area_id=Input::has('area_id')?Input::get('area_id'):"";
            $orderid=Input::has('orderid')?trim(Input::get('orderid')):"" ;
            $nick=Input::has('nick')?trim(Input::get('nick')):"" ;
            $starttime=Input::has('starttime')?trim(Input::get('starttime'))." 00:00:00":"" ;
            $endtime=Input::has('endtime')?trim(Input::get('endtime'))."  23:59:59":"" ;
            $buyname=Input::has('buyname')?trim(Input::get('buyname')):"" ;
            $goods=DB::table('goods')->whereIn('flag',array(3,4,5))->where('isdel',0)->get();
            $goodsid=[];
            $good_name="";
           foreach($goods as $k=>$v){
               $goodsid[]=$v['id'];
               $good_name[$v['id']]=$v['name'];

           }
           try{
            $order=DB::table('order_list')->where('status',2);
             if($orderid){
                 $orderid="%".$orderid."%";
                $order->where('orderid','like',$orderid); 
            }
            if($province_id){
                 $buy_province_id=DB::table('user_address')->where('province_id',$province_id)->get();
                 $buy_province_ids=[];
                 foreach($buy_province_id as $k=>$v){
                    $buy_province_ids[]=$v['id'];
                      
                 }
                 $order->whereIn('address_id',$buy_province_ids); 
            }
            if($city_id){
                 $buy_city_id=DB::table('user_address')->where('province_id',$province_id)->where('city_id',$city_id)->get();
                 $city_ids=[];
                 foreach($buy_city_id as $k=>$v){
                    $city_ids[]=$v['id'];
                     
                 }
                  $order->whereIn('address_id',$city_ids); 
            }
            if($area_id){
                 $buy_area_id=DB::table('user_address')->where('province_id',$province_id)->where('city_id',$city_id)->where('area_id',$area_id)->get();
                 $area_ids=[];
                 foreach($buy_area_id as $k=>$v){
                    $area_ids[]=$v['id'];
                     
                 }
                  $order->whereIn('address_id',$area_ids); 
            }


            if($buyname){
                $buy=DB::table('user_address')->where('name',$buyname)->first();
                 $order->where('address_id',$buy['id']); 
            }
             if($good){
                 $good_search=DB::table('goods')->where('name',$good)->first();
                $order->where('goods_id',$good_search['id']);
            }
            if($plat_from != -1){
                $order->where('plat_from',$plat_from);
            }
            if($nick){
                 $user_search=DB::table('user')->where('nick',$nick)->first();
                  $order->where('uid',$user_search['id']);
            }
            if($starttime){
                  $order->where('updatetime',">=",strtotime($starttime));
            }
            if($endtime){     
                  $order->where('updatetime',"<=",strtotime($endtime));
                 
            }
             if($pay_type){
                $order->where('pay_type',$pay_type);
            }
            
            $order_list= $order->whereIn('goods_id',$goodsid)->get();
            if($order_list){
            $uids=[];
            $address=[];
            foreach($order_list as $k=>$v){
                $uids[]=$v['uid'];
                $address[]=$v['address_id'];
            }
            $users=DB::table('user')->whereIn('id',$uids)->get();
            $users_info="";
            foreach($users as $k=>$v){
                $users_info[$v['id']]=$v['nick'];
           
            }
            $address_info="";
            $address=DB::table('user_address')->whereIn('id',$address)->get();
                 
            foreach($address as $k=>$v){
            $address_info[$v['id']]['province']=$v['province_id']?$data['allprovince'][$v['province_id']]:'';
			$address_info[$v['id']]['city']=$v['city_id']?$data['allcity'][$v['province_id']][$v['city_id']]:"";
			$address_info[$v['id']]['area']=$v['area_id']?$data['allarea'][$v['city_id']][$v['area_id']]:"";
            $address_info[$v['id']]['name']=$v['name'];
            $address_info[$v['id']]['address']=$v['address'];
            $address_info[$v['id']]['tel']=$v['tel'];
            }
            }else{
                 $address_info="";$users_info="";
            }
          	} catch (Exception $e) {
                  $order_list="";
                $address_info="";$users_info="";
           }
           $tmp=array();
       foreach($order_list as $k=>$v){
            $tmp[$k]['orderid']=$v['orderid'];
            $tmp[$k]['nick']=$users_info[$v['uid']];
            $tmp[$k]['good']=$good_name[$v['goods_id']];
            $tmp[$k]['num']= $v['num'];
            $tmp[$k]['price']= $v['price'];
            $tmp[$k]['old_price']= $v['old_price'];
            $tmp[$k]['total_price']= $v['total_price'];
            $tmp[$k]['attach_price']= $v['attach_price'];
            $tmp[$k]['plat_from']=$v['plat_from']?'安卓':'苹果';
            if($v['pay_type']==1){$tmp[$k]['pay_type']= '银联';}elseif($v['pay_type']==2){  $tmp[$k]['pay_type']= '支付宝';}elseif($v['pay_type']==3){ $tmp[$k]['pay_type']= '支付宝网页';}elseif($v['pay_type']==4){$tmp[$k]['pay_type']= '财付通';} 
            $tmp[$k]['time']=date('Y-m-d H:i:s',$v['updatetime']);
            $tmp[$k]['send_out']=$v['send_out']?'已发货':'未发货';
            $tmp[$k]['province']=isset($address_info[$v['address_id']]['province'])?$address_info[$v['address_id']]['province']:"";
            $tmp[$k]['city']=isset($address_info[$v['address_id']]['city'])?$address_info[$v['address_id']]['city']:"";
            $tmp[$k]['area']=isset($address_info[$v['address_id']]['area'])?$address_info[$v['address_id']]['area']:"";
            $tmp[$k]['address']=isset($address_info[$v['address_id']]['address'])?$address_info[$v['address_id']]['address']:"";
             $tmp[$k]['name']=isset($address_info[$v['address_id']]['name'])?$address_info[$v['address_id']]['name']:"";
            $tmp[$k]['tel']=isset($address_info[$v['address_id']]['tel'])?$address_info[$v['address_id']]['tel']:"";

       }

	//生成xls文件==========================================
		require_once ("../app/ext/PHPExcel.php");
		$excel=new PHPExcel();
		$objWriter = new PHPExcel_Writer_Excel5($excel);
		$excel->setActiveSheetIndex(0);
		$sheet=$excel->getActiveSheet();
		$sheet->setTitle('订单列表');		
		$sheetTitle=array('订单号','购买人',"商品名",'数量','购买单价','商品原价','总价','邮费','客户端平台','支付平台','时间','是否发货','省','市','县',"地址",'购买人','
        购买人电话');	
		$cNum=0;
		foreach($sheetTitle as $val){
		  $sheet->setCellValueByColumnAndRow($cNum,1,$val);
		  $cNum++;
		}
		$rNum=2;
		foreach($tmp as $val){
		  $cNum=0;
		  foreach($val as $row){
			  $sheet->setCellValueByColumnAndRow($cNum,$rNum," ".$row);
			  $cNum++;
		  }
		  $rNum++;
		}
		$outputFileName = "FinishList.xls";
		$file='upload/'.$outputFileName;
		$objWriter->save($file);
		$excel_url = '/upload/'.$outputFileName;
		echo "<a href='".$excel_url."'>下载</a>";
	}



}   