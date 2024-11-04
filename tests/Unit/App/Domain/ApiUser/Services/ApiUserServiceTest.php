<?php

namespace Tests\Unit\App\Domain\ApiUser\Services;

use Tests\TestCase;
use Exception;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Support\Facades\Log;
use App\Domain\ApiUser\Models\ApiUser;
use App\Domain\ApiUser\Repositories\ApiUserRepositoryInterface;
use App\Domain\ApiUser\Services\ApiUserService;

class ApiUserServiceTest extends TestCase
{
    /** @var ApiUserService */
    private $service;

    /** @var MockInterface */
    private $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock(ApiUserRepositoryInterface::class);
        $this->service = app(ApiUserService::class, ['apiUserRepository' => $this->repositoryMock]);
    }

    /**
     * @group services
     * @group api_user
     */
    public function test_can_find_record_by_id(): void
    {
        $generatedRecord = ApiUser::factory()->make();
        $generatedRecord->id = 1;

        $this->repositoryMock
            ->shouldReceive('firstById')
            ->once()
            ->with($generatedRecord->id)
            ->andReturn($generatedRecord);

        $foundRecord = $this->service->firstById($generatedRecord->id);

        $this->assertInstanceOf(ApiUser::class, $foundRecord);
        $this->assertEquals($generatedRecord->name, $foundRecord->name);
        $this->assertEquals($generatedRecord->email, $foundRecord->email);
        $this->assertEquals($generatedRecord->password, $foundRecord->password);
    }

    /**
     * @group services
     * @group api_user
     */
    public function test_generates_log_if_exception_occurs_when_try_find_record_by_id(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Houston, we have a problem.');

        Log::shouldReceive('error')
            ->withArgs(function ($message, $context) {
                return strpos(
                    $message,
                    '[ApiUserService] Error while trying to find a api user with the id provided.'
                ) !== false
                    && strpos($context['error_message'], 'Houston, we have a problem.') !== false;
            });

        $this->repositoryMock
            ->shouldReceive('firstById')
            ->once()
            ->andThrows(new Exception('Houston, we have a problem.'));

        $this->service->firstById(1);
    }

    /**
     * @group services
     * @group api_user
     */
    public function test_can_find_record_by_email(): void
    {
        $generatedRecord = ApiUser::factory()->make();
        $generatedRecord->id = 1;

        $this->repositoryMock
            ->shouldReceive('firstByField')
            ->once()
            ->with('email', $generatedRecord->email, ['id', 'name', 'email', 'password'])
            ->andReturn($generatedRecord);

        $foundRecord = $this->service->firstByEmail($generatedRecord->email);

        $this->assertInstanceOf(ApiUser::class, $foundRecord);
        $this->assertEquals($generatedRecord->name, $foundRecord->name);
        $this->assertEquals($generatedRecord->email, $foundRecord->email);
        $this->assertEquals($generatedRecord->password, $foundRecord->password);
    }

    /**
     * @group services
     * @group api_user
     */
    public function test_generates_log_if_exception_occurs_when_try_find_record_by_email(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Houston, we have a problem.');

        Log::shouldReceive('error')
            ->withArgs(function ($message, $context) {
                return strpos(
                    $message,
                    '[ApiUserService] Error while trying to find a api user with the email provided.'
                ) !== false
                    && strpos($context['error_message'], 'Houston, we have a problem.') !== false;
            });

        $this->repositoryMock
            ->shouldReceive('firstByField')
            ->once()
            ->andThrows(new Exception('Houston, we have a problem.'));

        $this->service->firstByEmail('nonexistent@test.com');
    }
}
