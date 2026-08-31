<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['string'],
            'location_x' => ['numeric'],
            'location_y' => ['numeric'],
            'zone_id' => ['nullable', 'exists:zones,id'],
            'status' => ['in:online,offline'],
        ];
    }
}