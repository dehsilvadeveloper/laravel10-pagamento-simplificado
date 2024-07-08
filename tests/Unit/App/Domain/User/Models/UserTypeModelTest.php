<?php

namespace Tests\Unit\App\Domain\User\Models;

use Tests\ModelTestCase;
use Tests\TestHelpers\DataTransferObjects\ModelConfigurationAssertionParamsDto;
use App\Domain\User\Models\UserType;

class UserTypeModelTest extends ModelTestCase
{
    /**
     * @group models
     * @group user
     */
    public function test_has_valid_configuration(): void
    {
        $dto = ModelConfigurationAssertionParamsDto::from([
            'model' => new UserType(),
            'fillable' => ['name'],
            'hidden' => [],
            'casts' => [
                'id' => 'int'
            ],
            'table' => 'user_types'
        ]);

        $this->runConfigurationAssertions($dto);
    }
}
