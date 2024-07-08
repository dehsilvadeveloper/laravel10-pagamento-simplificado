<?php

namespace Tests\Unit\App\Http\Controllers;

use Tests\TestCase;
use Exception;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use App\Domain\User\Models\UserType;
use App\Domain\User\Services\Interfaces\UserTypeServiceInterface;
use App\Http\Controllers\UserTypeController;

class UserTypeControllerTest extends TestCase
{
    /** @var UserTypeController */
    private $controller;

    /** @var MockInterface */
    private $serviceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serviceMock = Mockery::mock(UserTypeServiceInterface::class);
        $this->controller = app(UserTypeController::class, ['userTypeService' => $this->serviceMock]);
    }

    /**
     * @group controllers
     * @group user
     */
    public function test_can_get_list_of_records(): void
    {
        Mail::fake();
        Event::fake();
        Notification::fake();
        Queue::fake();

        $recordsCount = 3;
        $generatedRecords = UserType::factory()->count($recordsCount)->make();

        $this->serviceMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($generatedRecords);

        $response = $this->controller->index();
        $responseAsArray = $response->getData(true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $responseAsArray);
        $this->assertCount($recordsCount, $responseAsArray['data']);

        for ($i = 0; $i <= ($recordsCount - 1); $i++) {
            $this->assertArrayHasKey('id', $responseAsArray['data'][$i]);
        }
    }

    /**
     * @group controllers
     * @group user
     */
    public function test_can_get_empty_list_of_records(): void
    {
        Mail::fake();
        Event::fake();
        Notification::fake();
        Queue::fake();

        $this->serviceMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn(new EloquentCollection());

        $response = $this->controller->index();
        $responseAsArray = $response->getData(true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(0, $responseAsArray['data']);
        $this->assertEmpty($responseAsArray['data']);
    }

    /**
     * @group controllers
     * @group user
     */
    public function test_cannot_get_list_of_records_if_exception_occurs(): void
    {
        Mail::fake();
        Event::fake();
        Notification::fake();
        Queue::fake();

        Log::shouldReceive('error')
            ->withArgs(function ($message, $context) {
                return strpos($message, '[UserTypeController] Failed to get list of user types.') !== false
                    && strpos($context['error_message'], 'Houston, we have a problem.') !== false;
            });

        $this->serviceMock
            ->shouldReceive('getAll')
            ->once()
            ->andThrows(new Exception('Houston, we have a problem.', Response::HTTP_BAD_REQUEST));

        $response = $this->controller->index();
        $responseAsArray = $response->getData(true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(
            'An error has occurred. Could not get the user types list as requested.',
            $responseAsArray['message']
        );
    }

    /**
     * @group controllers
     * @group user
     */
    public function test_can_find_by_id(): void
    {
        Mail::fake();
        Event::fake();
        Notification::fake();
        Queue::fake();

        $existingRecord = UserType::factory()->make();
        $existingRecord->id = 1;

        $this->serviceMock
            ->shouldReceive('firstById')
            ->once()
            ->with($existingRecord->id)
            ->andReturn($existingRecord);

        $response = $this->controller->show($existingRecord->id);
        $responseAsArray = $response->getData(true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($responseAsArray['data']);
        $this->assertArrayHasKey('name', $responseAsArray['data']);
    }

    /**
     * @group controllers
     * @group user
     */
    public function test_cannot_find_by_id_a_nonexistent_record(): void
    {
        Mail::fake();
        Event::fake();
        Notification::fake();
        Queue::fake();

        $nonExistentId = 3;

        $this->serviceMock
            ->shouldReceive('firstById')
            ->once()
            ->with($nonExistentId)
            ->andReturn(null);

        $response = $this->controller->show($nonExistentId);
        $responseAsArray = $response->getData(true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals('The user type could not be found.', $responseAsArray['message']);
    }

    /**
     * @group controllers
     * @group user
     */
    public function test_cannot_find_by_id_if_exception_occurs(): void
    {
        Mail::fake();
        Event::fake();
        Notification::fake();
        Queue::fake();

        Log::shouldReceive('error')
            ->withArgs(function ($message, $context) {
                return strpos($message, '[UserTypeController] Failed to find the requested user type.') !== false
                    && strpos($context['error_message'], 'Houston, we have a problem.') !== false;
            });

        $this->serviceMock
            ->shouldReceive('firstById')
            ->once()
            ->andThrows(new Exception('Houston, we have a problem.', Response::HTTP_BAD_REQUEST));

        $response = $this->controller->show(1);
        $responseAsArray = $response->getData(true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(
            'An error has occurred. Could not find the user type as requested.',
            $responseAsArray['message']
        );
    }
}
