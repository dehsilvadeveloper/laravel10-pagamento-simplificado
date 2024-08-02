<?php

namespace Tests\Unit\App\Http\Controllers;

use Tests\TestCase;
use Exception;
use Carbon\Carbon;
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
use App\Domain\User\Models\User;
use App\Domain\User\Services\Interfaces\UserServiceInterface;
use App\Domain\Wallet\Models\Wallet;
use App\Http\Controllers\UserController;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class UserControllerTest extends TestCase
{
    /** @var UserController */
    private $controller;

    /** @var MockInterface */
    private $serviceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(UserTypeSeeder::class);

        $this->serviceMock = Mockery::mock(UserServiceInterface::class);
        $this->controller = app(UserController::class, ['userService' => $this->serviceMock]);
    }

    /**
     * @group controllers
     * @group user
     */
    public function test_can_create(): void
    {
        Mail::fake();
        Event::fake();
        Notification::fake();
        Queue::fake();

        $password = fake()->password(12);
        $starterBalance = fake()->randomFloat(2, 10, 900);
        $fakeUserRecord = User::factory()->make(['password' => $password]);
        $fakeUserRecord->id = 1;
        $fakeWalletRecord = Wallet::factory()->for($fakeUserRecord)->make([
            'balance' => $starterBalance
        ]);
        $fakeWalletRecord->id = 1;
        $fakeUserRecord->setRelation('wallet', $fakeWalletRecord);

        $request = CreateUserRequest::create(
            route('user.create'),
            'POST',
            [
                'user_type_id' => $fakeUserRecord->user_type_id,
                'name' => $fakeUserRecord->name,
                'document_type_id' => $fakeUserRecord->document_type_id,
                'document_number' => $fakeUserRecord->document_number,
                'email' => $fakeUserRecord->email,
                'password' => $password,
                'starter_balance' => $starterBalance
            ]
        );
        $request->setLaravelSession($this->app['session']);
        $request->setContainer($this->app);
        $request->setRedirector($this->app['redirect']);
        $request->validateResolved();

        $this->serviceMock
            ->shouldReceive('create')
            ->once()
            ->andReturn($fakeUserRecord);

        $response = $this->controller->create($request);
        $responseAsArray = $response->getData(true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertNotEmpty($responseAsArray['data']);
        $this->assertArrayHasKey('id', $responseAsArray['data']);
        $this->assertArrayHasKey('id', $responseAsArray['data']['user_type']);
        $this->assertArrayHasKey('name', $responseAsArray['data']['user_type']);
        $this->assertArrayHasKey('name', $responseAsArray['data']);
        $this->assertArrayHasKey('id', $responseAsArray['data']['document_type']);
        $this->assertArrayHasKey('name', $responseAsArray['data']['document_type']);
        $this->assertArrayHasKey('document_number', $responseAsArray['data']);
        $this->assertArrayHasKey('email', $responseAsArray['data']);
        $this->assertArrayHasKey('id', $responseAsArray['data']['wallet']);
        $this->assertArrayHasKey('balance', $responseAsArray['data']['wallet']);
    }

    /**
     * @group controllers
     * @group user
     */
    public function test_can_update(): void
    {
        Mail::fake();
        Event::fake();
        Notification::fake();
        Queue::fake();

        $password = fake()->password(12);
        $fakeRecord = User::factory()->make(['password' => $password]);
        $fakeRecord->id = 1;

        $request = UpdateUserRequest::create(
            route('user.update', ['id' => $fakeRecord->id]),
            'PATCH',
            [
                'name' => 'Updated name',
                'document_number' => fake()->cpf(false)
            ]
        );
        $request->setLaravelSession($this->app['session']);
        $request->setContainer($this->app);
        $request->setRedirector($this->app['redirect']);
        $request->validateResolved();

        $fakeUpdatedRecord = $fakeRecord->replicate();
        $fakeUpdatedRecord->id = 1;
        $fakeUpdatedRecord->name = 'Updated name';
        $fakeUpdatedRecord->email = fake()->cpf(false);
        $fakeUpdatedRecord->created_at = Carbon::now();
        $fakeUpdatedRecord->updated_at = Carbon::now();

        $this->serviceMock
            ->shouldReceive('update')
            ->once()
            ->andReturn($fakeUpdatedRecord);

        $response = $this->controller->update($fakeRecord->id, $request);
        $responseAsArray = $response->getData(true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($responseAsArray['data']);
        $this->assertArrayHasKey('name', $responseAsArray['data']);
        $this->assertArrayHasKey('document_number', $responseAsArray['data']);
        $this->assertNotEquals($fakeRecord->name, $responseAsArray['data']['name']);
        $this->assertNotEquals($fakeRecord->email, $responseAsArray['data']['email']);
    }

    /**
     * @group controllers
     * @group user
     */
    public function test_can_delete(): void
    {
        Mail::fake();
        Event::fake();
        Notification::fake();
        Queue::fake();

        $fakeRecord = User::factory()->make();
        $fakeRecord->id = 1;

        $this->serviceMock
            ->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $response = $this->controller->delete($fakeRecord->id);
        $responseAsArray = $response->getData(true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('message', $responseAsArray);
        $this->assertArrayNotHasKey('data', $responseAsArray);
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
        $generatedRecords = User::factory()->count($recordsCount)->make();

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
            $this->assertArrayHasKey('id', $responseAsArray['data'][$i]['user_type']);
            $this->assertArrayHasKey('name', $responseAsArray['data'][$i]['user_type']);
            $this->assertArrayHasKey('name', $responseAsArray['data'][$i]);
            $this->assertArrayHasKey('id', $responseAsArray['data'][$i]['document_type']);
            $this->assertArrayHasKey('name', $responseAsArray['data'][$i]['document_type']);
            $this->assertArrayHasKey('document_number', $responseAsArray['data'][$i]);
            $this->assertArrayHasKey('email', $responseAsArray['data'][$i]);
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
                return strpos($message, '[UserController] Failed to get list of users.') !== false
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
            'An error has occurred. Could not get the users list as requested.',
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

        $existingRecord = User::factory()->make();
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
        $this->assertArrayHasKey('id', $responseAsArray['data']);
        $this->assertArrayHasKey('id', $responseAsArray['data']['user_type']);
        $this->assertArrayHasKey('name', $responseAsArray['data']['user_type']);
        $this->assertArrayHasKey('name', $responseAsArray['data']);
        $this->assertArrayHasKey('id', $responseAsArray['data']['document_type']);
        $this->assertArrayHasKey('name', $responseAsArray['data']['document_type']);
        $this->assertArrayHasKey('document_number', $responseAsArray['data']);
        $this->assertArrayHasKey('email', $responseAsArray['data']);
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
        $this->assertEquals('The user could not be found.', $responseAsArray['message']);
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
                return strpos($message, '[UserController] Failed to find the user.') !== false
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
            'An error has occurred. Could not find the user as requested.',
            $responseAsArray['message']
        );
    }
}
