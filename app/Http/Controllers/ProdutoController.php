<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ProdutoController extends Controller
{
    public function create()
    {
        // Obter categorias da API do Mercado Livre
        try {
            $client = new Client();
            $response = $client->get('https://api.mercadolibre.com/sites/MLB/categories'); // Substitua 'MLB' pelo ID do seu site, se necessário
            $categorias = json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return back()->withErrors(['error' => 'Erro ao obter categorias: ' . $e->getMessage()]);
        }

        return view('produto.create', compact('categorias'));
    }

    public function store(Request $request)
    {      
        // Validação do formulário
        $request->validate([
            'nome' => 'required|max:255',
            'descricao' => 'required',
            'preco' => 'required|numeric',
            'quantidade' => 'required|integer',
            'categoria' => 'required',
            'imagem' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
    
        // Criação do produto no banco de dados
        $produto = new Produto();
        $produto->nome = $request->input('nome');
        $produto->descricao = $request->input('descricao');
        $produto->preco = $request->input('preco');
        $produto->quantidade = $request->input('quantidade');
        $produto->categoria = $request->input('categoria');
        
        // Upload da imagem
        if ($request->hasFile('imagem')) {
            $path = $request->file('imagem')->store('produtos');
            $produto->imagem = $path;
        }

        // Tentativa de salvar o produto no banco de dados
        try {
            $produto->save(); // Salvando o produto no banco de dados
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao salvar o produto: ' . $e->getMessage()]);
        }

        // Envia os dados para a API do Mercado Livre
        $client = new Client();
        try {
            $response = $client->post('https://api.mercadolibre.com/items', [
                'headers' => [
                    'Authorization' => 'Bearer ' . session('ml_token'), // Token salvo na sessão
                ],
                'json' => [
                    'title' => $produto->nome,
                    'category_id' => $produto->categoria,
                    'price' => $produto->preco,
                    'currency_id' => 'BRL',
                    'available_quantity' => $produto->quantidade,
                    'condition' => 'new',
                    'pictures' => [
                        [
                            'source' => asset('storage/' . $produto->imagem),
                        ]
                    ]
                ]
            ]);
        } catch (RequestException $e) {
            return back()->withErrors(['error' => 'Erro ao cadastrar o produto na API: ' . $e->getMessage()]);
        }

        // Armazena o resultado da API em $result
        $result = json_decode($response->getBody(), true);

        // Adicione a mensagem de sucesso à sessão
        session()->flash('success', 'O produto foi cadastrado com sucesso!');

        // Redireciona para a página de listagem de produtos
        return redirect()->route('produtos.index');
    }
}
