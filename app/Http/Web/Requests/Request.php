<?php

namespace App\Http\Web\Requests;

use App\Http\Utils\FormRequest;

class Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
