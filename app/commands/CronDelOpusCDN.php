<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronDelOpusCDN extends Command {
    /**
    * The console command name.
    *
    * @var string
    */
    protected $name = 'user:CronDelOpusCDN';
    /**
    * The console command description.
    *
    * @var string
    */
    protected $description = '删除cdn中缓存的文件音频文件';
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
        $poem_url=Config::get('app.poem_url');
        $URL="http://api.mmtrix.com/proxytask/purge";
        $AK="be43d8c5550a510d7e077b8b9fc5740f";//真
        $SK="5fb2d702317d7583c84bbcbe3628b195";//真
        $PURGEDIR="";
        $OP=0;
        $host="poem.weinidushi.com.cn";
        $list=DB::table('opus_del_list')->where('isdel','=',0)->orderBy("id",'asc')->take('50')->get();
        //50个
        if($list){
            $ch = curl_init (); // 启动一个CURL会话
            $id_arr=array();
            $purge_url ="";
            $purge_url1="";
        foreach ($list as $key => $value) {
                $purge_url .=$poem_url.$value['url']." ";
                $purge_url1 .=$poem_url.$value['url']."+";
                array_push($id_arr,$value['id']);
        }
         
            $str="ak=".$AK."&host=".$host."&op=".$OP."&purge_dir=&purge_url=".trim($purge_url," ")."&sk=".$SK;
            $MD5=md5($str);
            $param_string="ak=".$AK."&host=".$host."&op=".$OP."&purge_dir=&purge_url=".trim($purge_url1,"+")."&sign=".$MD5;
            $url = $URL."?".$param_string;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); 
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST" );
            curl_setopt($ch, CURLOPT_POSTFIELDS, "");
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT,5);
            $result = curl_exec($ch);
            
            $json=json_decode($result,true);
          
            //删除成功
            if(isset($json['requestid']) && $json['requestid']!=1 && $json['requestid']!= 2 && $json['requestid']!= 99999 ){
                DB::table('opus_del_list')->whereIn('id',$id_arr)->update(array('isdel'=>1));
            }else{
               // continue;
            }
            curl_close($ch);
       }
    }
}