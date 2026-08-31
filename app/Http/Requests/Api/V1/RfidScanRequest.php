<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class RfidScanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rfid_tag' => ['required', 'string'],
            'action' => ['required', 'in:enter,exit'],
            'location' => ['required', 'string'],
        ];
    }
}