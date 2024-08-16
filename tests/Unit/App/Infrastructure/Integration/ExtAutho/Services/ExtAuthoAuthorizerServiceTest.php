<?php

namespace Tests\Unit\App\Infrastructure\Integration\ExtAutho\Services;

use Tests\TestCase;
use Exception;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Response;
use App\Domain\TransferAuthorization\DataTransferObjects\AuthorizeTransferDto;
use App\Domain\TransferAuthorization\DataTransferObjects\CreateTransferAuthorizationResponseDto;
use App\Domain\TransferAuthorization\Models\TransferAuthorizationResponse;
use App\Domain\TransferAuthorization\Repositories\TransferAuthorizationResponseRepositoryInterface;
use App\Infrastructure\Integration\ExtAutho\Services\ExtAuthoAuthorizerService;
use App\Infrastructure\Integration\ExtAutho\Services\Interfaces\ExtAuthoRequestServiceInterface;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\TransferStatusSeeder;
use Database\Seeders\UserTypeSeeder;

class ExtAuthoAuthorizerServiceTest extends TestCase
{
    /** @var ExtAuthoAuthorizerService */
    private $service;

    /** @var MockInterface */
    private $extAuthoRequestServiceMock;

    /** @var MockInterface */
    private $transferAuthorizationResponseRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(TransferStatusSeeder::class);
        $this->seed(UserTypeSeeder::class);

        $this->extAuthoRequestServiceMock = Mockery::mock(ExtAuthoRequestServiceInterface::class);
        $this->transferAuthorizationResponseRepositoryMock = Mockery::mock(TransferAuthorizationResponseRepositoryInterface::class);

        $this->service = app(
            ExtAuthoAuthorizerService::class,
            [
                'extAuthoRequestService' => $this->extAuthoRequestServiceMock,
                'transferAuthorizationResponseRepository' => $this->transferAuthorizationResponseRepositoryMock
            ]
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @group services
     * @group ext_autho
     */
    public function test_receive_true_on_successful_authorization(): void
    {
        $fakeTransferAuthorizationResponseRecord = TransferAuthorizationResponse::factory()->make();
        $fakeResponse = [
            'status' => 'success',
            'data' => [
                'authorization' => true
            ]
        ];

        /** @var MockInterface|ClientResponse $mockedResponse */
        $mockedResponse = Mockery::mock(ClientResponse::class);
        $mockedResponse->shouldReceive('json')->andReturn($fakeResponse);
        $mockedResponse->shouldReceive('throwUnlessStatus')->with(Response::HTTP_FORBIDDEN)->andReturnSelf();
        $mockedResponse->shouldReceive('forbidden')->andReturnFalse();
        $mockedResponse->shouldReceive('object')->andReturn(json_decode(json_encode($fakeResponse)));

        $this->extAuthoRequestServiceMock
            ->shouldReceive('sendRequest')
            ->once()
            ->andReturn($mockedResponse);

        $this->transferAuthorizationResponseRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with(
                Mockery::on(function (CreateTransferAuthorizationResponseDto $dto) use ($fakeResponse) {
                    return $dto->transferId === 1
                        && $dto->response === json_encode($fakeResponse);
                })
            )
            ->andReturn($fakeTransferAuthorizationResponseRecord);
        
        $dto = AuthorizeTransferDto::from([
            'transfer_id' => 1,
            'payer_id' => 5,
            'payee_id' => 6,
            'amount' => 25.50
        ]);

        $result = $this->service->authorize($dto);

        $this->assertTrue($result);
    }

    /**
     * @group services
     * @group ext_autho
     */
    public function test_receive_false_on_denied_authorization(): void
    {
        $fakeTransferAuthorizationResponseRecord = TransferAuthorizationResponse::factory()->make();
        $fakeResponse = [
            'status' => 'fail',
            'data' => [
                'authorization' => false
            ]
        ];

        /** @var MockInterface|ClientResponse $mockedResponse */
        $mockedResponse = Mockery::mock(ClientResponse::class);
        $mockedResponse->shouldReceive('json')->andReturn($fakeResponse);
        $mockedResponse->shouldReceive('throwUnlessStatus')->with(Response::HTTP_FORBIDDEN)->andReturnSelf();
        $mockedResponse->shouldReceive('forbidden')->andReturnTrue();
        $mockedResponse->shouldReceive('object')->andReturn(json_decode(json_encode($fakeResponse)));

        $this->extAuthoRequestServiceMock
            ->shouldReceive('sendRequest')
            ->once()
            ->andReturn($mockedResponse);

        $this->transferAuthorizationResponseRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with(
                Mockery::on(function (CreateTransferAuthorizationResponseDto $dto) use ($fakeResponse) {
                    return $dto->transferId === 1
                        && $dto->response === json_encode($fakeResponse);
                })
            )
            ->andReturn($fakeTransferAuthorizationResponseRecord);
        
        $dto = AuthorizeTransferDto::from([
            'transfer_id' => 1,
            'payer_id' => 5,
            'payee_id' => 6,
            'amount' => 25.50
        ]);

        $result = $this->service->authorize($dto);

        $this->assertFalse($result);
    }

    /**
     * @group services
     * @group ext_autho
     */
    public function test_receive_false_on_empty_dto_data(): void
    {
        /** @var MockInterface|AuthorizeTransferDto $dtoMock */
        $dtoMock = Mockery::mock(AuthorizeTransferDto::class);
        $dtoMock->shouldReceive('toArray')->andReturn([]);
        $dtoMock->shouldReceive('jsonSerialize');

        $result = $this->service->authorize($dtoMock);

        $this->assertFalse($result);
    }

    /**
     * @group services
     * @group ext_autho
     */
    public function test_receive_false_on_empty_authorization_response(): void
    {
        /** @var MockInterface|ClientResponse $mockedResponse */
        $mockedResponse = Mockery::mock(ClientResponse::class);
        $mockedResponse->shouldReceive('json')->andReturn([]);
        $mockedResponse->shouldReceive('throwUnlessStatus')->with(Response::HTTP_FORBIDDEN)->andReturnSelf();
        $mockedResponse->shouldReceive('forbidden')->andReturnFalse();
        $mockedResponse->shouldReceive('object')->andReturnNull();

        $this->extAuthoRequestServiceMock
            ->shouldReceive('sendRequest')
            ->once()
            ->andReturn($mockedResponse);

        $this->transferAuthorizationResponseRepositoryMock
            ->shouldReceive('create')
            ->once();

        $dto = AuthorizeTransferDto::from([
            'transfer_id' => 1,
            'payer_id' => 5,
            'payee_id' => 6,
            'amount' => 25.50
        ]);

        $result = $this->service->authorize($dto);

        $this->assertFalse($result);
    }

    /**
     * @group services
     * @group ext_autho
     */
    public function test_receive_false_on_exception(): void
    {
        $this->extAuthoRequestServiceMock
            ->shouldReceive('sendRequest')
            ->once()
            ->andThrow(new Exception('Houston, we have a problem.'));

        $dto = AuthorizeTransferDto::from([
            'transfer_id' => 1,
            'payer_id' => 5,
            'payee_id' => 6,
            'amount' => 25.50
        ]);

        $result = $this->service->authorize($dto);

        $this->assertFalse($result);
    }
}
