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
        $estante = $data->estante;

        $consulta = estantes::find($estante->idestante);

        if($consulta == null){
            $consulta = new estantes();
        }
        $consulta->idestante = $estante->idestante;
        $consulta->nombre = $estante->nombre;
        $consulta->genero_lib = $estante->genero_lib;
    }

    //obtener por id
    public function buscarporid(Request $data)
    {
        $data = (object) $data;
        $estante = $data->idestante;

        $consulta = estantes::find($estante);
        return response()->json($consulta);
    }

    //mostrar todos los datos
    public function getAll()
    {
        $consulta = estantes::all();
    }
}
