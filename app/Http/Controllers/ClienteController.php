<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\returnCallback;

class ClienteController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:level')->only('index');
    }
    
    public function meus_clientes(User $id){
        $user = User::where('id',$id->id)->first();
        $clientes = $user->customers()->get();

        return view('clientes.meus_clientes',[
            'clientes' => $clientes
        ]);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('clientes.index',[
            'clientes' => Cliente::orderBy('nome')->paginate('5')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view(view: 'clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $cliente = new Cliente();
        $cliente->user_id = $request->user_id;
        $cliente->nome = $request->nome;
        $cliente->email = $request->email;
        $cliente->telefone = $request->telefone;
        $cliente->empresa = $request->empresa;
        $cliente->tel_comercial = $request->tel_comercial;

        $cliente->save();
        return redirect()->route('cliente.create')->with('msg','Cliente Cadastrado com Sucesso!!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        return view('clientes.show',[ 'cliente' => $cliente ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        return view('Clientes.edit', ['cliente' => $cliente]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        Cliente::findOrFail($cliente->id)->update($request->all());
        return redirect()->route('clientes.show', $cliente->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        Cliente::findOrFail($cliente->id)->delete();
        return redirect()->route('meus.clientes', Auth::user()->id);
        // return 'Deletado o Cliente' . $cliente->nome;
    }

    public function confirma_delet(Cliente $id){
        return view('clientes.confirma_delet', ['id'=>$id]);
    }
}
