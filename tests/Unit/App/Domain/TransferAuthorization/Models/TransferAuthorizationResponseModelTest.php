<?php

namespace Tests\Unit\App\Domain\TransferAuthorization\Models;

use Tests\ModelTestCase;
use Tests\TestHelpers\DataTransferObjects\ModelConfigurationAssertionParamsDto;
use App\Domain\TransferAuthorization\Models\TransferAuthorizationResponse;

class TransferAuthorizationResponseModelTest extends ModelTestCase
{
    /**
     * @group models
     * @group transfer_authorization
     */
    public function test_has_valid_configuration(): void
    {
        $dto = ModelConfigurationAssertionParamsDto::from([
            'model' => new TransferAuthorizationResponse(),
            'fillable' => [
                'transfer_id',
                'response'
            ],
            'casts' => [
                'id' => 'int'
            ],
            'table' => 'transfer_authorization_responses'
        ]);

        $this->runConfigurationAssertions($dto);
    }
}
