<?php

namespace Tests\Unit\App\Domain\User\Models;

use Tests\ModelTestCase;
use Tests\TestHelpers\DataTransferObjects\ModelConfigurationAssertionParamsDto;
use App\Domain\User\Models\User;

class UserModelTest extends ModelTestCase
{
    /**
     * @group models
     * @group user
     */
    public function test_has_valid_configuration(): void
    {
        $dto = ModelConfigurationAssertionParamsDto::from([
            'model' => new User(),
            'fillable' => [
                'user_type_id',
                'name',
                'document_type_id',
                'document_number',
                'email',
                'password'
            ],
            'hidden' => ['password'],
            'casts' => [
                'id' => 'int',
                'password' => 'hashed',
                'deleted_at' => 'datetime'
            ],
            'table' => 'users'
        ]);

        $this->runConfigurationAssertions($dto);
    }
}
