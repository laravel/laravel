<?php

namespace App\Http\Api\Requests;

use App\Http\Utils\FormRequest;

class Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
