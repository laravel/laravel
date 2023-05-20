<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\libros;

class librosController extends Controller
{

    //guardar o actualizar
    public function guardar(Request $data)
    {
        $data = (object) $data;
        $libro = $data->libro;

        $consulta = libros::find($libro->idlibro);

        if($consulta == null){
            $consulta = new libros();
        }
        $consulta->idlibro = $libro->idlibro;
        $consulta->nombre = $libro->nombre;
        $consulta->autor = $libro->autor;
        $consulta->genero = $libro->genero;
        $consulta->editorial = $libro->editorial;
        $consulta->id_estante= $libro->id_estante;

        $resultado = $consulta->save();
        return response()->json($resultado);
    }


    //buscar registro por id
    public function buscarporid(Request $data)
    {
        $data = (object) $data;
        $libro = $data->idlibro;

        $consulta = libros::find($libro);

    return response()->json($consulta);
    }

    //mostrar todos ls registros
    public function mostrar()
    {
        $consulta = libros::all();

        return response()->json($consulta);
    }
}
