<?php

namespace Tests\Unit\App\Domain\Notification\DataTransferObjects;

use Tests\DtoTestCase;
use Illuminate\Http\Request;
use App\Domain\Notification\DataTransferObjects\CreateNotificationDto;

class CreateNotificationDtoTest extends DtoTestCase
{
    /**
     * @group dtos
     * @group notification
     */
    public function test_can_create_from_array_with_snakecase_keys(): void
    {
        $this->runCreationFromSnakecaseArrayAssertions(
            CreateNotificationDto::class,
            [
                'recipient_id' => 5,
                'type' => 'App\\Domain\\User\\Notifications\\WelcomeNotification',
                'channel' => 'mail',
                'response' => null
            ]
        );
    }

    /**
     * @group dtos
     * @group notification
     */
    public function test_can_create_from_array_with_camelcase_keys(): void
    {
        $this->runCreationFromCamelcaseArrayAssertions(
            CreateNotificationDto::class,
            [
                'recipientId' => 5,
                'type' => 'App\\Domain\\User\\Notifications\\WelcomeNotification',
                'channel' => 'mail',
                'response' => null
            ]
        );
    }

    /**
     * @group dtos
     * @group notification
     */
    public function test_cannot_create_from_empty_array(): void
    {
        $this->runCreationFromEmptyArrayAssertions(CreateNotificationDto::class);
    }

    /**
     * @group dtos
     * @group notification
     */
    public function test_can_create_from_request(): void
    {
        $this->runCreationFromRequestAssertions(
            CreateNotificationDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'recipient_id' => 5,
                    'type' => 'App\\Domain\\User\\Notifications\\WelcomeNotification',
                    'channel' => 'mail',
                    'response' => null
                ]
            )
        );
    }

    /**
     * @group dtos
     * @group notification
     */
    public function test_cannot_create_from_empty_request(): void
    {
        $this->runCreationFromEmptyRequestAssertions(CreateNotificationDto::class);
    }

    /**
     * @group dtos
     * @group notification
     */
    public function test_cannot_create_from_request_with_invalid_values(): void
    {
        $this->runCreationFromRequestWithInvalidValuesAssertions(
            CreateNotificationDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'recipient_id' => null
                ]
            )
        );
    }
}
