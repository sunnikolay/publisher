<?php

namespace App\Tests\Listener;

use App\Model\ErrorDebugDetails;
use App\Model\ErrorResponse;
use App\Service\ExceptionHandler\ExceptionMapping;
use App\Service\ExceptionHandler\ExceptionMappingResolver;
use App\Tests\TestListenerHelper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ApiExceptionListenerTest extends TestCase
{
    use TestListenerHelper;

    private ExceptionMappingResolver $resolver;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = $this->createMock(ExceptionMappingResolver::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function testNon500MappingWithHiddenMessage(): void
    {
        $mapping = ExceptionMapping::fromCode(Response::HTTP_NOT_FOUND);
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody = json_encode(['error' => $responseMessage]);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        $event = $this->createExceptionEvent(new \InvalidArgumentException('test'));

        $this->runListener($this->resolver, $this->logger, $this->serializer, $event, false);

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    public function testNon500MappingWithPublicMessage(): void
    {
        $mapping = new ExceptionMapping(Response::HTTP_NOT_FOUND, false, false);
        $responseMessage = 'test';
        $responseBody = json_encode(['error' => $responseMessage]);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        $event = $this->createExceptionEvent(new \InvalidArgumentException('test'));

        $this->runListener($this->resolver, $this->logger, $this->serializer, $event, false);

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    public function testNon500LoggableMappingTriggerLogger(): void
    {
        $mapping = new ExceptionMapping(Response::HTTP_NOT_FOUND, false, true);
        $responseMessage = 'test';
        $responseBody = json_encode(['error' => $responseMessage]);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        $this->logger->expects($this->once())
            ->method('error');

        $event = $this->createExceptionEvent(new \InvalidArgumentException('test'));

        $this->runListener($this->resolver, $this->logger, $this->serializer, $event, false);

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    public function test500IsLoggable(): void
    {
        $mapping = ExceptionMapping::fromCode(Response::HTTP_BAD_GATEWAY);
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody = json_encode(['error' => $responseMessage]);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('error message', $this->anything());

        $event = $this->createExceptionEvent(new \InvalidArgumentException('error message'));

        $this->runListener($this->resolver, $this->logger, $this->serializer, $event, false);

        $this->assertResponse(Response::HTTP_BAD_GATEWAY, $responseBody, $event->getResponse());
    }

    public function test500IsDefaultWhenMappingNotFound(): void
    {
        $responseMessage = Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR];
        $responseBody = json_encode(['error' => $responseMessage]);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn(null);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('error message', $this->anything());

        $event = $this->createExceptionEvent(new \InvalidArgumentException('error message'));

        $this->runListener($this->resolver, $this->logger, $this->serializer, $event, false);

        $this->assertResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $responseBody, $event->getResponse());
    }

    public function testShowTraceWhenDebug(): void
    {
        $mapping = ExceptionMapping::fromCode(Response::HTTP_NOT_FOUND);
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody = json_encode(['error' => $responseMessage, 'trace' => 'something']);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(
                $this->callback(function (ErrorResponse $response) use ($responseMessage) {
                    /** @var ErrorDebugDetails $details */
                    $details = $response->getDetails();

                    return $response->getMessage() == $responseMessage
                        && $details instanceof ErrorDebugDetails
                        && !empty($details->getTrace());
                }),
                JsonEncoder::FORMAT
            )
            ->willReturn($responseBody);

        $event = $this->createExceptionEvent(new \InvalidArgumentException('error message'));

        $this->runListener($this->resolver, $this->logger, $this->serializer, $event, true);

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }
}
