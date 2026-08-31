<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreDisasterEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:fire,earthquake'],
            'severity' => ['required', 'in:critical,cautionary'],
            'node_id' => ['required', 'exists:nodes,id'],
            'location' => ['required', 'string'],
        ];
    }
}