<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronFlower extends Command {
    
    /**
    * The console command name.
    *
    * @var string
    */
    protected $name = 'user:CronFlower';
    
    /**
    * The console command description.
    *
    * @var string
    */
    protected $description = '鲜花静态榜单';
    
    /**
    * Create a new command instance.
    *
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
    * Execute the console command.
    *
    * @return mixed
    */
    public function fire()
    {
        $path = public_path().'/upload/flower/';
        $type = $this->argument('type');
        if($type==0){
            //年榜静态榜单
            $year=date("Y",time())-1;
            $start=strtotime($year."-01-01 00:00:00");
            $end=strtotime($year."-12-31 23:59:59");
            $sql="select uid as fromid,sum(num) as num from flower_time_list where starttime>=? and endtime<=? and flag=1  group by fromid   order by num desc limit ?";
            $list=DB::select($sql,array($start,$end,100));
            $file_path=$path.$year.'.txt';
            $data_path="/upload/flower/".$year.'.txt';
            DB::table('flower_rank_list')->insert(array('name'=>$year,'type'=>0,'url'=>$data_path,'addtime'=>time()));
            $data = serialize($list);
            file_put_contents($file_path,$data);
        }elseif($type==1){
            $data=date('Y-m-d',time());
            $time=$this->getlastMonthDays($data);
            $start=strtotime($time[0]." 00:00:00");
            $end=strtotime($time[1]." 23:59:59");
            $sql="select uid as fromid,sum(num) as num from flower_time_list where starttime>=? and endtime<=? and flag=1  group by fromid order by num desc limit ?";
            $list=DB::select($sql,array($start,$end,100));
            $file_name=date('Y-m',$start);
            $file_path=$path.$file_name.'.txt';
             $data_path="/upload/flower/".$file_name.'.txt';
            DB::table('flower_rank_list')->insert(array('name'=>$file_name,'type'=>1,'url'=>$data_path,'addtime'=>time()));
            $data = serialize($list);
            file_put_contents($file_path,$data);
        }elseif($type==2){
            $start_time=mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
            $end_time=mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));

            $sql="select uid as fromid,sum(num) as num from flower_time_list where starttime>=? and endtime<=? and flag=0  group by fromid order by num desc limit ?";
            $list=DB::select($sql,array($start_time,$end_time,100));
            $file_name=date("Y-m-d",$start_time);
            $file_path=$path.$file_name.'.txt';
             $data_path="/upload/flower/".$file_name.'.txt';
            DB::table('flower_rank_list')->insert(array('name'=>$file_name,'type'=>2,'url'=>$data_path,'addtime'=>time()));
            $data = serialize($list);
            file_put_contents($file_path,$data);
        }
    }
    //得到上个月的时间戳
    function getlastMonthDays($date){
        $timestamp=strtotime($date);
        $firstday=date('Y-m-01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-01'));
        $lastday=date('Y-m-d',strtotime("$firstday +1 month -1 day"));
        return array($firstday,$lastday);
    }
    
    /*
    * Get the console command arguments.
    * type 1 周  2 月   3 年
    */
    protected function getArguments()
    {
        return [
        ['type',InputArgument::REQUIRED, 'year month week'],
        
        ];
    }
    
}