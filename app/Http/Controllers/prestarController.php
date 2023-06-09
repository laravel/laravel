<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\prestar;
use Illuminate\Http\Request;

class prestarController extends Controller
{
    //guardar o actualizar
    public function prestar(Request $data)
    {
        $data = (object) $data;
        $prestar = (object) $data->prestar;

        $consulta = prestar::find($prestar->idprestar);

        if($consulta == null){
            $consulta = new prestar();
        }

        $consulta->idlibro = $prestar->idlibro;
        $consulta->nombre = $prestar->nombre;
        $consulta->nombre_usuario = $prestar->nombre_usuario;
        $consulta->fecha = $prestar->fecha;
        $consulta->fechaFinal = $prestar->fechaFinal;
        $consulta->estado = $prestar->estado;
        $consulta->idprestar = $prestar->idprestar;

        $respuesta = $consulta->save();
        return response()->json($respuesta);
    }


    //mostrar todos los datos
    public function mostrarPrest()
    {
        $consulta = prestar::all();
        return response()->json($consulta);
    }
}