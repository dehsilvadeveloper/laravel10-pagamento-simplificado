<?php

use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\DocumentType\Enums\DocumentTypeEnum;

return [
    'default' => [
        [
            'user' => [
                'user_type_id' => UserTypeEnum::COMMON,
                'name' => 'John Doe',
                'document_type_id' => DocumentTypeEnum::CPF,
                'document_number' => fake()->cpf(false),
                'email' => 'john.doe@test.com',
                'password' => 'defaultpassword'
            ],
            'wallet' => [
                'balance' => 455.30
            ]
        ],
        [
            'user' => [
                'user_type_id' => UserTypeEnum::COMMON,
                'name' => 'Jane Doe',
                'document_type_id' => DocumentTypeEnum::CPF,
                'document_number' => fake()->cpf(false),
                'email' => 'jane.doe@test.com',
                'password' => 'defaultpassword'
            ],
            'wallet' => [
                'balance' => 455.30
            ]
        ],
        [
            'user' => [
                'user_type_id' => UserTypeEnum::SHOPKEEPER,
                'name' => 'Pokemon Company',
                'document_type_id' => DocumentTypeEnum::CNPJ,
                'document_number' => fake()->cnpj(false),
                'email' => 'pokemon.company@fake.com',
                'password' => 'defaultpassword'
            ],
            'wallet' => [
                'balance' => 1255.50
            ]
        ],
        [
            'user' => [
                'user_type_id' => UserTypeEnum::SHOPKEEPER,
                'name' => 'Stark Industries',
                'document_type_id' => DocumentTypeEnum::CNPJ,
                'document_number' => fake()->cnpj(false),
                'email' => 'stark.industries@fake.com',
                'password' => 'defaultpassword'
            ],
            'wallet' => [
                'balance' => 1255.50
            ]
        ]
    ]
];
