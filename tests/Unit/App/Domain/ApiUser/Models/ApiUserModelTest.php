<?php

namespace Tests\Unit\App\Domain\User\Models;

use Tests\ModelTestCase;
use Tests\TestHelpers\DataTransferObjects\ModelConfigurationAssertionParamsDto;
use App\Domain\ApiUser\Models\ApiUser;

class UserModelTest extends ModelTestCase
{
    /**
     * @group api_user
     */
    public function test_has_valid_configuration(): void
    {
        $dto = ModelConfigurationAssertionParamsDto::from([
            'model' => new ApiUser(),
            'fillable' => ['name', 'email', 'password'],
            'hidden' => ['password', 'remember_token'],
            'casts' => [
                'id' => 'int',
                'email_verified_at' => 'datetime',
                'password' => 'hashed'
            ],
            'table' => 'api_users'
        ]);

        $this->runConfigurationAssertions($dto);
    }
}