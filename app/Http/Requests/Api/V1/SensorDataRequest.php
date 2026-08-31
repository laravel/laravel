<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class SensorDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'node_id' => ['required', 'exists:nodes,id'],
            'sensor_type' => ['required', 'in:smoke,temperature,seismic'],
            'value' => ['required', 'numeric'],
            'unit' => ['required', 'string'],
        ];
    }
}