<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\login;
use App\Models\registro;
use App\Models\session;
use Illuminate\Http\Request;
use DateInterval;
use DateTime;

class loginController extends Controller
{
    
     public function log(Request $data)
    {
        $data = (object) $data;

        $username = $data->username;
        $password = $data->password;
    
        $querydata = registro::where('username', $username)->where('password', $password)->where('estado', 1)->get();

        if( $querydata != []  && count($querydata) > 0 ){
            $login = registro::where('username', $username)->get();

        }

        return response()->json($querydata);
    }





    public function login(Request $data)
    {
        $data = (object) $data;
        //echo json_encode($data);
        $username = $data->username;
        $password = $data->password;
       
        $respuesta['valor']= "";
        $respuesta['username']=$username;
        $respuesta['password']=$password;

        $session = new session();
        $token = '';
        //usuario es el nombre la tabla, donde el campo usuario sea igual a la variable usuario, etc. Luego obtener
        $consulta = registro::where('username', $username)->where('password', $password)->where('estado', 1)->get();      

        if($consulta !== null && count($consulta) > 0 ) {
            $sessiones = session::where('username', $username)->get();
            

            if ( count($sessiones) > 0 ) {
                $session = $sessiones[0];
            }

            $time = new DateTime();
            $time->add(new DateInterval('PT' . 10 . 'M')); //agregar 10 min a la hora actual

            $token = $username.'123';
            $session->token = $token;
            $session->useraname = $username;
            $session->fechaval = $time;

            $session->save();

            $respuesta = true;
        } else {
            $respuesta= false;
        }

        if ( $respuesta ) {
            $retorno = (object) array( 'resultado' => $respuesta, 'token' => $token );            
        } else {
            $retorno = (object) array( 'resultado' => $respuesta, 'token' => '' );
        }
        
        return response()->json($retorno);    
        
    }

    //SI EL TOKEN YA NO ES VÁLIDO, REDIRECCIONAR A LOGIN
  public function validarToken(Request $datai){
    $data = (object) $datai;
    $token = $data->token;

    
    $consulta = session::find($token);

    $expira = false;

    if ($consulta != null) {

        $time = $consulta->fechaval;

        $fecha_actual = date('Y-m-d H:i:s');
        if($fecha_actual > $time){
            $expira = false;
        }else{
            $expira = true;
        }
    }
    return response()->json($expira);
     
  }
}
