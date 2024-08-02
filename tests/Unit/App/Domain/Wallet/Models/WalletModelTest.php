<?php

namespace Tests\Unit\App\Domain\Wallet\Models;

use Tests\ModelTestCase;
use Tests\TestHelpers\DataTransferObjects\ModelConfigurationAssertionParamsDto;
use App\Domain\Wallet\Models\Wallet;

class WalletModelTest extends ModelTestCase
{
    /**
     * @group models
     * @group wallet
     */
    public function test_has_valid_configuration(): void
    {
        $dto = ModelConfigurationAssertionParamsDto::from([
            'model' => new Wallet(),
            'fillable' => ['user_id', 'balance'],
            'hidden' => [],
            'casts' => [
                'id' => 'int'
            ],
            'dates' => [],
            'table' => 'wallets'
        ]);

        $this->runConfigurationAssertions($dto);
    }
}
