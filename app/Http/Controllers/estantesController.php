<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\estantes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class estantesController extends Controller
{

    //guardar
    public function guardar(Request $data)
    {
        $data = (object) $data;
        $estante = (object) $data->estante;

        $consulta = estantes::find($estante->id_estante);

        if(!$consulta){
            $consulta = new estantes();
        }
        $consulta->id_estante = $estante->id_estante;
        $consulta->nombre = $estante->nombre;
        $consulta->genero_lib = $estante->genero_lib;

       $resultado = $consulta->save();
       return response()->json($resultado);

    }

    //obtener por id
    public function buscarporid(Request $data)
    {

        $data = (object) $data;
        $estante = $data->id_estante;

        $consulta = estantes::find($estante);
        return response()->json($consulta);


    }

    //mostrar todos los datos
    public function getAll()
    {
        $consulta = estantes::all();

        return response()->json($consulta);
        
    }
}
