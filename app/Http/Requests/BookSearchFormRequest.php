<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Nette\Schema\ValidationException;

class BookSearchFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'author' => ['nullable', 'string', 'max:2'],
            'title' => ['nullable', 'string'],
            'isbn' => ['nullable', 'string', 'min:2'],
            'offset' => [
                'nullable',
                'numeric'
            ]
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new ValidationException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
