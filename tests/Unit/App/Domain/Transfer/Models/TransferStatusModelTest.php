<?php

namespace Tests\Unit\App\Domain\Transfer\Models;

use Tests\ModelTestCase;
use Tests\TestHelpers\DataTransferObjects\ModelConfigurationAssertionParamsDto;
use App\Domain\Transfer\Models\TransferStatus;

class TransferStatusModelTest extends ModelTestCase
{
    /**
     * @group models
     * @group transfer
     */
    public function test_has_valid_configuration(): void
    {
        $dto = ModelConfigurationAssertionParamsDto::from([
            'model' => new TransferStatus(),
            'fillable' => ['name'],
            'casts' => [
                'id' => 'int'
            ],
            'dates' => [],
            'table' => 'transfer_statuses'
        ]);

        $this->runConfigurationAssertions($dto);
    }
}
