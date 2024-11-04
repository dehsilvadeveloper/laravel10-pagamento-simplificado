<?php

namespace Tests\Unit\App\Infrastructure\Integration\ExtNotifier\Services;

use Tests\TestCase;
use Exception;
use Mockery;
use Mockery\MockInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Response;
use App\Domain\Common\Exceptions\EmptyRequestException;
use App\Infrastructure\Integration\ExtNotifier\DataTransferObjects\SendNotificationDto;
use App\Infrastructure\Integration\ExtNotifier\Services\ExtNotifierNotificationService;
use App\Infrastructure\Integration\ExtNotifier\Services\Interfaces\ExtNotifierRequestServiceInterface;

class ExtNotifierNotificationServiceTest extends TestCase
{
    /** @var ExtNotifierNotificationService */
    private $service;

    /** @var MockInterface */
    private $extNotifierRequestServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extNotifierRequestServiceMock = Mockery::mock(ExtNotifierRequestServiceInterface::class);

        $this->service = app(
            ExtNotifierNotificationService::class,
            [
                'extNotifierRequestService' => $this->extNotifierRequestServiceMock
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
     * @group ext_notifier
     */
    public function test_can_send_notification(): void
    {
        $dto = SendNotificationDto::from([
            'recipient' => 'teste@teste.com',
            'message' => 'Você recebeu uma nova mensagem privada.'
        ]);

        $responseMock = Mockery::mock(ClientResponse::class);

        $this->extNotifierRequestServiceMock
            ->shouldReceive('sendRequest')
            ->once()
            ->andReturn($responseMock);

        $responseMock->shouldReceive('throw')->once();

        $response = $this->service->notify($dto);

        $this->assertInstanceOf(ClientResponse::class, $response);
    }

    /**
     * @group services
     * @group ext_notifier
     */
    public function test_fail_with_empty_request_data(): void
    {
        $this->expectException(EmptyRequestException::class);

        /** @var MockInterface|SendNotificationDto $dtoMock */
        $dtoMock = Mockery::mock(SendNotificationDto::class);
        $dtoMock->shouldReceive('toArray')->andReturn([]);
        $dtoMock->shouldReceive('jsonSerialize');

        $this->service->notify($dtoMock);
    }

    /**
     * @group services
     * @group ext_notifier
     */
    public function test_fail_if_http_connection_exception_is_throw(): void
    {
        $this->expectException(ConnectionException::class);

        $dto = SendNotificationDto::from([
            'recipient' => 'teste@teste.com',
            'message' => 'Você recebeu uma nova mensagem privada.'
        ]);

        $this->extNotifierRequestServiceMock
            ->shouldReceive('sendRequest')
            ->once()
            ->andThrow(new ConnectionException('Connection error'));

        $this->service->notify($dto);
    }

    /**
     * @group services
     * @group ext_notifier
     */
    public function test_fail_if_http_request_exception_is_throw(): void
    {
        $this->expectException(RequestException::class);

        $dto = SendNotificationDto::from([
            'recipient' => 'teste@teste.com',
            'message' => 'Você recebeu uma nova mensagem privada.'
        ]);

        $this->extNotifierRequestServiceMock
            ->shouldReceive('sendRequest')
            ->once()
            ->andThrow(
                new RequestException(
                    new ClientResponse(
                        new GuzzleResponse(
                            Response::HTTP_INTERNAL_SERVER_ERROR,
                            [],
                            'Request error'
                        )
                    )
                )
            );

        $this->service->notify($dto);
    }

    /**
     * @group services
     * @group ext_notifier
     */
    public function test_fail_if_exception_is_throw(): void
    {
        $this->expectException(Exception::class);

        $dto = SendNotificationDto::from([
            'recipient' => 'teste@teste.com',
            'message' => 'Você recebeu uma nova mensagem privada.'
        ]);

        $this->extNotifierRequestServiceMock
            ->shouldReceive('sendRequest')
            ->once()
            ->andThrow(new Exception('Houston, we have a problem.'));

        $this->service->notify($dto);
    }
}
