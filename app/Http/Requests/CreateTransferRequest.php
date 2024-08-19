<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransferRequest extends FormRequest
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
            'payer_id' => ['required', 'integer', 'exists:users,id'],
            'payee_id' => ['required', 'integer', 'different:payer_id', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'gt:0']
        ];
    }

    /**
     * Define body params to be used by the API documentation
     */
    public function bodyParameters(): array
    {
        return [
            'payer_id' => [
                'description' => 'The user that will transfer the amount. This value can be obtained on the entity users.',
                'example' => 1
            ],
            'payee_id' => [
                'description' => 'The user that will receive the amount. This value can be obtained on the entity users.',
                'example' => 2
            ],
            'amount' => [
                'description' => 'The amount to be transferred between users.',
                'example' => 200.50
            ]
        ];
    }
}
