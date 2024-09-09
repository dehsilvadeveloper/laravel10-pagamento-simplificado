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
            'payer' => ['required', 'integer', 'exists:users,id'],
            'payee' => ['required', 'integer', 'different:payer', 'exists:users,id'],
            'value' => ['required', 'numeric', 'gt:0']
        ];
    }

    /**
     * Define body params to be used by the API documentation
     */
    public function bodyParameters(): array
    {
        return [
            'payer' => [
                'description' => 'The id of the user that will transfer the amount. This value can be obtained on the '
                    . 'entity users. Payer and payee of a transfer cannot be the same. Users of type SHOPKEEPER '
                    . 'cannot make transfers, only receive them.',
                'example' => 1
            ],
            'payee' => [
                'description' => 'The id of the user that will receive the amount. This value can be obtained on the '
                . 'entity users. Payee and payer of a transfer cannot be the same',
                'example' => 2
            ],
            'value' => [
                'description' => 'The amount to be transferred between users.',
                'example' => 200.50
            ]
        ];
    }
}
