<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class MercadoLivreController extends Controller
{
    public function redirectToML()
    {
        $url = "https://auth.mercadolivre.com.br/authorization?response_type=code&client_id=".env('MERCADO_LIVRE_CLIENT_ID')."&redirect_uri=".env('MERCADO_LIVRE_REDIRECT_URI');
        return redirect($url);
    }

    public function handleCallback(Request $request)
    {
        $code = $request->input('code');
        $client = new Client();
        $response = $client->post('https://api.mercadolibre.com/oauth/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => env('MERCADO_LIVRE_CLIENT_ID'),
                'client_secret' => env('MERCADO_LIVRE_CLIENT_SECRET'),
                'code' => $code,
                'redirect_uri' => env('MERCADO_LIVRE_REDIRECT_URI')
            ]
        ]);

        $token = json_decode($response->getBody()->getContents(), true)['access_token'];
        session(['ml_token' => $token]);

        return redirect('/produto/create');
    }
}

?>