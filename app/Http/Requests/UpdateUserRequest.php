<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'user_type_id' => ['sometimes', 'required', 'integer', 'exists:user_types,id'],
            'name' => ['sometimes', 'required', 'string', 'max:70'],
            'document_type_id' => ['sometimes', 'required', 'integer', 'exists:document_types,id'],
            'document_number' => ['sometimes', 'required', 'string', 'regex:/^[0-9]+$/', 'unique:users,document_number'],
            'email' => ['sometimes', 'required', 'email', 'unique:users,email'],
            'password' => ['sometimes', 'required', 'string', 'min:8']
        ];
    }

    /**
     * Define body params to be used by the API documentation
     */
    public function bodyParameters(): array
    {
        return [
            'user_type_id' => [
                'description' => 'The type of user. This value can be obtained on the entity user_types.',
                'example' => 1
            ],
            'name' => [
                'description' => 'The name of the user.',
                'example' => 'John Doe'
            ],
            'document_type_id' => [
                'description' => 'The type of document that the user has. This value can be obtained on the entity document_types.',
                'example' => 2
            ],
            'document_number' => [
                'description' => 'The number of the document that the user has. It can only have numbers.',
                'example' => '60796747008'
            ],
            'email' => [
                'description' => 'The email address of the user.',
                'example' => 'john.doe@test.com'
            ],
            'password' => [
                'description' => 'The password of the user.',
                'example' => 'TDwbC4zvy963xa@#hSEDH'
            ]
        ];
    }
}
