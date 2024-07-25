<?php

namespace Tests\Unit\App\Domain\Notification\Models;

use Tests\ModelTestCase;
use Tests\TestHelpers\DataTransferObjects\ModelConfigurationAssertionParamsDto;
use App\Domain\Notification\Models\Notification;

class NotificationModelTest extends ModelTestCase
{
    /**
     * @group models
     * @group notification
     */
    public function test_has_valid_configuration(): void
    {
        $dto = ModelConfigurationAssertionParamsDto::from([
            'model' => new Notification(),
            'fillable' => [
                'recipient_id',
                'type',
                'channel',
                'response'
            ],
            'hidden' => [],
            'casts' => [
                'id' => 'int'
            ],
            'table' => 'notifications'
        ]);

        $this->runConfigurationAssertions($dto);
    }
}
