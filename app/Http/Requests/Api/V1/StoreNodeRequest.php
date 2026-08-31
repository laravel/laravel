<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreNodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'location_x' => ['required', 'numeric'],
            'location_y' => ['required', 'numeric'],
            'zone_id' => ['nullable', 'exists:zones,id'],
        ];
    }
}