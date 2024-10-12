<?php

namespace App\Http\Requests;

use App\Rules\IsMultipleOfTwenty;
use App\Rules\IsbnIsEndingWithSemicolonRule;
use App\Rules\IsbnLengthRule;
use App\Traits\ApiResponses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookSearchFormRequest extends FormRequest
{
    use ApiResponses;

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
            'author' => ['nullable', 'string'],
            'title' => ['nullable', 'string'],
            'isbn' => [
                'nullable',
                'array',
                new IsbnIsEndingWithSemicolonRule(),
                new IsbnLengthRule()
            ],
            'offset' => [
                'nullable',
                'numeric',
                new IsMultipleOfTwenty()
            ]
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->error([
                $validator->errors()
            ], 422)
        );
    }
}
