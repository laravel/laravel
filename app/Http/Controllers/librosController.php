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
        $libro = (object) $data->libro;

        $consulta = libros::find($libro->id_libro);

        if(!$consulta){
            $consulta = new libros();
        }
        $consulta->id_libro = $libro->id_libro;
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



           //  BUSCAR POR GENERO  //

    //mostrar libros donde el genero sea accion
    public function libroAccion()
    {
        $consulta = libros::where('genero', "accion")->get();

        return response()->json($consulta);
    }

     //mostrar libros donde el genero sea fantasia
     public function libroFantasia()
     {
         $consulta = libros::where('genero', "fantasia")->get();
 
         return response()->json($consulta);
     }

      //mostrar libros donde el genero sea ciencia ficcion
      public function cienciaFiccion()
      {
          $consulta = libros::where('genero', "ciencia ficcion")->get();
  
          return response()->json($consulta);
      }  


      // BUSCAR LIBRO POR AUTOR //
      
      //mostrar libros donde el autor sea Anna Banks
      public function annaBanks()
      {
          $consulta = libros::where('autor', "Anna Banks")->get();
  
          return response()->json($consulta);
      }  

      public function neilGaiman()
      {
          $consulta = libros::where('autor', "Neil Gaiman")->get();
  
          return response()->json($consulta);
      }  
}
