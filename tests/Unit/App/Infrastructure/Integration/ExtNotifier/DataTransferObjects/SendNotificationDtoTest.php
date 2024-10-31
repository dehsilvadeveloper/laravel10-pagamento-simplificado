<?php

namespace Tests\Unit\App\Infrastructure\Integration\ExtNotifier\DataTransferObjects;

use Tests\DtoTestCase;
use Illuminate\Http\Request;
use App\Infrastructure\Integration\ExtNotifier\DataTransferObjects\SendNotificationDto;

class SendNotificationDtoTest extends DtoTestCase
{
    /**
     * @group dtos
     * @group ext_notifier
     */
    public function test_can_create_from_array_with_snakecase_keys(): void
    {
        $this->runCreationFromSnakecaseArrayAssertions(
            SendNotificationDto::class,
            [
                'recipient' => 'test@test.com',
                'message' => 'This is a sample message.'
            ]
        );
    }

    /**
     * @group dtos
     * @group ext_notifier
     */
    public function test_can_create_from_array_with_camelcase_keys(): void
    {
        $this->runCreationFromCamelcaseArrayAssertions(
            SendNotificationDto::class,
            [
                'recipient' => 'test@test.com',
                'message' => 'This is a sample message.'
            ]
        );
    }

    /**
     * @group dtos
     * @group ext_notifier
     */
    public function test_cannot_create_from_empty_array(): void
    {
        $this->runCreationFromEmptyArrayAssertions(SendNotificationDto::class);
    }

    /**
     * @group dtos
     * @group ext_notifier
     */
    public function test_can_create_from_request(): void
    {
        $this->runCreationFromRequestAssertions(
            SendNotificationDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'recipient' => 'test@test.com',
                    'message' => 'This is a sample message.'
                ]
            )
        );
    }

    /**
     * @group dtos
     * @group ext_notifier
     */
    public function test_cannot_create_from_empty_request(): void
    {
        $this->runCreationFromEmptyRequestAssertions(SendNotificationDto::class);
    }

    /**
     * @group dtos
     * @group ext_notifier
     */
    public function test_cannot_create_from_request_with_invalid_values(): void
    {
        $this->runCreationFromRequestWithInvalidValuesAssertions(
            SendNotificationDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'recipient' => 123.50,
                    'message' => true
                ]
            )
        );
    }
}
