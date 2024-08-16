<?php

namespace Tests\Unit\App\Domain\Transfer\Models;

use Tests\ModelTestCase;
use Tests\TestHelpers\DataTransferObjects\ModelConfigurationAssertionParamsDto;
use App\Domain\Transfer\Models\Transfer;

class TransferModelTest extends ModelTestCase
{
    /**
     * @group models
     * @group transfer
     */
    public function test_has_valid_configuration(): void
    {
        $dto = ModelConfigurationAssertionParamsDto::from([
            'model' => new Transfer(),
            'fillable' => [
                'payer_id',
                'payee_id',
                'amount',
                'transfer_status_id',
                'authorized_at'
            ],
            'casts' => [
                'id' => 'int',
                'authorized_at' => 'datetime'
            ],
            'table' => 'transfers'
        ]);

        $this->runConfigurationAssertions($dto);
    }
}
