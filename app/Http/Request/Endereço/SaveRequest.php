<?php

namespace App\Http\Request\Endereço;

class SaveRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
         'cep' => 'required|string',
         'logradouro' => 'required|string',
         'numero' => 'required|string',
         'bairro' => 'required|string',
         'cidade' => 'required|string',
         'estado' => 'required|string',
        ];
    }

}
