<?php
/**
* 活跃度
*/
 class AdminActiveController extends BaseController{
        /**
        * 添加活跃度
        */
        public function addActive(){
                $sql="select count(*) as num from user";
                $user=DB::select($sql);
                return View::make('active.add')->with('user',$user);

            }

        /**
        * 执行添加
        */  
        public function doaddActive(){

            $_POST['addtime']=time();
            DB::table('Active_data')->insert($_POST);
            return Redirect::to('/admin/listActive');

        }
        /**
        * 活跃度报表
        */
        public function Activelist(){



            $year=isset($_POST['year'])?$_POST['year']:'2014';
            $sql='select * from Active_data where time like "'.$year.'%" order by  time asc';
            $list=DB::select($sql);
            $time=array();
            foreach ($list as $key => $value) {
                    $time['time'][]=$value['time'];
                    $time['day_hot'][]=(int)$value['day_hot'];
                    $time['day_per'][]=(double)$value['day_per'];
                    $time['week_hot'][]=(int)$value['week_hot'];
                    $time['week_per'][]=(double)$value['week_per'];
                    $time['alluser'][]=$value['alluser'];
            }
            $user_time= json_encode($time['time'],true);
            $user_day_hot=json_encode($time['day_hot'],true);
            $user_day_per=json_encode($time['day_per'],true);
            $user_week_hot=json_encode($time['week_hot'],true);
            $user_week_per=json_encode($time['week_per'],true);
            $alluser=json_encode($time['alluser'],true);
        return View::make('active.list')->with('time',$user_time)->with('day_per',$user_day_per)->with('day_hot',$user_day_hot)->with('week_per',$user_week_per)->with('week_hot',$user_week_hot)->with('year',$year)->with('alluser',$alluser);

        }
        /**
        * 活跃度列表
        */
        public function listActive(){
            $year=isset($_POST['year'])?$_POST['year']:'2014';
            $sql='select * from Active_data where time like "'.$year.'%" order by time asc';
            $list=DB::select($sql);

        return View::make('active.list_year')->with('list',$list)->with('year',$year);
        }

        /**
        * 修改活跃度
        */
        public function changeActive(){
                $id=$_GET['id'];
                $sql='select * from Active_data where id=?';
                $one=DB::select($sql ,array($id));
                return View::make('active.change')->with('one',$one[0]); 
            
            }
        /**
        * 执行修改
        */

        public function dochangeActive(){

                $id=$_POST['id'];
                unset($_POST['id']);
                DB::table('Active_data')->where("id",'=',$id)->update($_POST);
                return Redirect::to('/admin/listActive');
            }
        /**
        * 付费活跃度展示
        */
        public function moneyActive(){
            $year=isset($_POST['year'])?$_POST['year']:'2014';
            $this->countMoney($year);   
            $sql='select * from Active_money where time like "'.$year.'%" order by time asc';
            $list=DB::select($sql);
            $time=array();
            foreach ($list as $key => $value) {
                    $time['time'][]=$value['time'];
                    $time['user_buy'][]=(int)$value['user_buy'];
                    $time['alluser'][]=(int)$value['alluser'];
                    if($value['alluser']){
                    $per=(double)((int)$value['user_buy']/(int)$value['alluser']*100);
                    }else{ 
                    $per=(double)0;
                    }
                $time['buy_per'][]= round($per,4);
                    $time['money'][]=(int)$value['money'];
            }
            $user_time= json_encode($time['time'],true);
            $user_buy=json_encode($time['user_buy'],true);
            $buy_per=json_encode($time['buy_per'],true);
            $money=json_encode($time['money'],true);
         
        return View::make('active.list_money')->with('year',$year)->with('time',$user_time)->with('money',$money)->with('user_buy',$user_buy)->with('buy_per',$buy_per);

        }

        /**
        * 付费活跃度统计
        */
        public function countMoney($year=2014){
         
            for($i=1;$i<=12;$i++){
                if($i<10){
                     $Active_money=DB::table('Active_money')->where('time','=',$year.'-0'.$i)->first();
                }else{
                     $Active_money=DB::table('Active_money')->where('time','=',$year.'-'.$i)->first();
                }

                $now=date("Y-m",time());
                if(!$Active_money){
                    $start=$year.'-'.$i.'-01 00:00:00';
                    $starttime=strtotime($start);
                    $end=$this->getendmouthtime($year.'-'.$i);
                    $endtime=strtotime($end);
                    $sql="select count(*) as user_buy,sum(total_price) as money from order_list where status=2 and  updatetime >=$starttime and updatetime <=$endtime";
                    $order= DB::select($sql);
                    $sql1="select count(*) as num from user where addtime <= $endtime";
                    $alluser=DB::select($sql1);
                    if($i<10){
                        $data['time']=$year.'-0'.$i;
                    }else{
                        $data['time']=$year.'-'.$i;
                    }
                    $data['alluser']=$alluser[0]['num'];
                    $data['user_buy']=$order[0]['user_buy'];
                    $data['money']=!empty($order[0]['money'])?$order[0]['money']:0;
                    $data['addtime']=time();
                    $data['buy_per']=0;
                    DB::table('Active_money')->insert($data);
                }else{
                     if($i<10){
                        $data['time']=$year.'-0'.$i;
                    }else{
                        $data['time']=$year.'-'.$i;
                    }
                    if( $now==$data['time']){
                    $start=$year.'-'.$i.'-01 00:00:00';
                    $starttime=strtotime($start);
                    $end=$this->getendmouthtime($year.'-'.$i);
                    $endtime=strtotime($end);
                    $sql="select count(*) as user_buy,sum(total_price) as money from order_list where status=2 and  updatetime >=$starttime and updatetime <=$endtime";
                    $order= DB::select($sql);
                    $sql1="select count(*) as num from user where addtime <= $endtime";
                    $alluser=DB::select($sql1);
                
                    $data['alluser']=$alluser[0]['num'];
                    $data['user_buy']=$order[0]['user_buy'];
                    $data['money']=!empty($order[0]['money'])?$order[0]['money']:0;
                    $data['addtime']=time();
                    $data['buy_per']=0;
                    DB::table('Active_money')->where('time','=',$now)->update($data);
                    }
                }
            }
            
        }
        // 得到付费活跃度统计列表
        public function moneyList(){
            $year=Input::get('year','2014');
            $sql='select * from Active_money where time like "'.$year.'%" order by id asc';
            $list=DB::select($sql);

                return View::make('active.listmoney')->with('list',$list)->with('year',$year);
        }

   // 修改
        public function changeMoneyActive(){
                $id=$_GET['id'];
                $sql='select * from Active_money where id=?';
                $one=DB::select($sql ,array($id));
                 if( $one[0]['alluser']){
                       $per=(double)((int) $one[0]['user_buy']/(int) $one[0]['alluser']*100);
                    }else{ 
                    $per=(double)0;
                    }
              
                      $one[0]["buy_per"]= round($per,4);
                return View::make('active.changeMoney')->with('one',$one[0]); 

        }
    //执行修改
        public function dochangeMoneyActive(){
                 $id=$_POST['id'];
                unset($_POST['id']);
                unset($_POST['buy_per']);
                DB::table('Active_money')->where("id",'=',$id)->update($_POST);
                return Redirect::to('/admin/moneyList');
        }




        //  $date=date("Y-m",time());
            public function getendmouthtime( $date){
                    $date_arr=explode('-',$date);
                    $year=$date_arr[0];
                    $month=$date_arr[1];
                    $days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
                    
                    if ($month < 1 OR $month > 12)
                    {
                    return 0;
                    }
                    // Is the year a leap year?
                    if ($month == 2)
                    {
                        if ($year%400 == 0 OR ($year%4 == 0 AND $year%100 != 0))
                        {
                        return $year.'-'.$month.'-29 23:59:59';
                        }
                    }
                    return $endstro =$year.'-'.$month.'-'.$days_in_month[$month - 1]." 23:59:59";
                }





}