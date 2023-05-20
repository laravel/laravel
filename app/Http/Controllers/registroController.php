<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\registro;
use Illuminate\Http\Request;

class registroController extends Controller
{
    //registrar
    
    public function registrar(Request $data)
    {
        $data = (object) $data;
        $registro = (object) $data->registro;

        $consulta = registro::find($registro->idusuario);

        if(!$consulta){
            $consulta = new registro();
        }
        $consulta->idusuario = $registro->idusuario;
        $consulta->nombre = $registro->nombre;
        $consulta->apellido = $registro->apellido;
        $consulta->email = $registro->email;
        $consulta->username = $registro->username;
        $consulta->password = $registro->password;
        $consulta->estado = $registro->estado;

        $resultado = $consulta->save();
        return response()->json($resultado);
    }


    //buscar por id
    public function buscarID(Request $data)
    {
        $data = (object) $data;
        $usuario = $data->idusuario;

        $consulta = registro::find($usuario);
        return response()->json($consulta);

    }

    //mostrar todos los datos 
    public function mostrarAll()
    {
        $consulta = registro::all();
        return response()->json($consulta);
    }

  
}
