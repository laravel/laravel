<?php

use App\Http\Controllers\estantesController;
use App\Http\Controllers\librosController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\registroController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/


//Libros
Route::post('/guardarLib', [librosController::class, 'guardar']); //guardar o actualizar DONE
Route::post('/buscarID', [librosController::class, 'buscarporid']); //buscar libro por id
Route::post('/mostrarLib', [librosController::class, 'mostrar']); //mostrar todos los registros DONE
//por genero
Route::post('/libroAccion', [librosController::class, 'libroAccion']); //DONE mostrar todos los libros de accion DONE
Route::post('/libroFantasia', [librosController::class, 'libroFantasia']); //DONE mostrar todos los libros de fantasia DONE
Route::post('/libroCienciaFiccion', [librosController::class, 'cienciaFiccion']); // DONE mostrar todos los libros de ciencia ficcion DONE
//buscar por autor
Route::post('/annabanks', [librosController::class, 'annaBanks']); //mostrar libros de anna banks DONE
Route::post('/neilGaiman', [librosController::class, 'neilGaiman']); //mostrar libros de Neil Gaiman DONE


//registro
Route::post('/usuarios', [registroController::class, 'mostrarAll']); //mostrar todos los registros DONE
Route::post('/registrar', [registroController::class, 'registrar']); //guardar o actualizar DONE
Route::post('/buscarID', [registroController::class, 'buscarID']); //obtener registro por id

//login
Route::post('/login', [loginController::class, 'login']);

//estantes
Route::post('/guardarEst', [estantesController::class, 'guardar']); //DONE
Route::post('/buscarporID', [estantesController::class, 'buscarporid']);
Route::post('/getEstantes', [estantesController::class, 'getAll']); //DONE 

