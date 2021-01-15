<?php 

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class MailController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function send(array $data)
    {
    // $data = ['to' => 'address','subject' => 'subject','message' => 'message'];
    try{
      mail($data['to'],$data['subject'],$data['message']);
      }catch(Exception $e){
        $e->getMessage();
      }
    }
    public function sent($to,$subject,$message){
      try{
        mail($to,$subject,$messsage);
      }catch(Exception $e){
        $e->getMessage();
      }
    }
}
