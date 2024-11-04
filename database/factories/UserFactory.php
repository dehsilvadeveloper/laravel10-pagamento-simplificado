<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Domain\DocumentType\Enums\DocumentTypeEnum;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $now = Carbon::now();
        $userType = fake()->randomElement([UserTypeEnum::COMMON, UserTypeEnum::SHOPKEEPER]);
        $documentType = $this->generateDocumentType($userType);

        return [
            'user_type_id' => $userType->value,
            'name' => $this->generateName($userType),
            'document_type_id' => $documentType->value,
            'document_number' => $this->generateDocumentNumber($documentType),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'created_at' => $now,
            'updated_at' => $now
        ];
    }

    /**
     * Indicate that the user is deleted.
     */
    public function deleted(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'deleted_at' => now()
            ];
        });
    }

    /**
     * Generate a document number based on the document type.
     *
     * @param DocumentTypeEnum $documentType
     * @return string
     */
    private function generateDocumentNumber(DocumentTypeEnum $documentType): string
    {
        return match ($documentType) {
            DocumentTypeEnum::CPF => fake()->cpf(false),
            DocumentTypeEnum::CNPJ => fake()->cnpj(false),
            default => fake()->unique()->numerify('##############')
        };
    }

    /**
     * Generate a document type based on the user type.
     *
     * @param UserTypeEnum $userType
     * @return DocumentTypeEnum
     */
    private function generateDocumentType(UserTypeEnum $userType): DocumentTypeEnum
    {
        return match ($userType) {
            UserTypeEnum::COMMON => fake()->randomElement([DocumentTypeEnum::CPF]),
            UserTypeEnum::SHOPKEEPER => fake()->randomElement([DocumentTypeEnum::CNPJ]),
            default => fake()->randomElement([DocumentTypeEnum::CPF, DocumentTypeEnum::CNPJ])
        };
    }

    /**
     * Generate a name based on the user type.
     *
     * @param UserTypeEnum $userType
     * @return string
     */
    private function generateName(UserTypeEnum $userType): string
    {
        return match ($userType) {
            UserTypeEnum::COMMON => fake()->name(),
            UserTypeEnum::SHOPKEEPER => fake()->company(),
            default => fake()->name()
        };
    }
}
