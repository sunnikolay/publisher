<?php

namespace App\Listener;

use App\Model\ErrorDebugDetails;
use App\Model\ErrorResponse;
use App\Service\ExceptionHandler\ExceptionMapping;
use App\Service\ExceptionHandler\ExceptionMappingResolver;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ApiExceptionListener
{
    public function __construct(
        private readonly ExceptionMappingResolver $resolver,
        private readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer,
        private readonly bool $isDebug
    ) {
    }

    /**
     * Этот метод вызывается когда скрипт пытается выполнить объект как функцию.
     */
    public function __invoke(ExceptionEvent $event): void
    {
        // получаем exception из event
        $throwable = $event->getThrowable();

        // Получаем mapping из resolver
        $mapping = $this->getMapping(get_class($throwable));

        // Логируем если это требуется
        $this->writeToLog($mapping, $throwable);

        // Получаем неоюходимый экземпляр ответа
        $response = $this->getResponse($mapping, $throwable);

        $event->setResponse($response);
    }

    private function getMapping(string $className): ExceptionMapping
    {
        $mapping = $this->resolver->resolve($className);

        // Если mapping null тогда создаём свой динамический mapping с кодом 500
        if (null === $mapping) {
            $mapping = ExceptionMapping::fromCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $mapping;
    }

    private function writeToLog(ExceptionMapping $mapping, \Throwable $throwable): void
    {
        if ($mapping->getCode() >= Response::HTTP_INTERNAL_SERVER_ERROR || $mapping->isLoggable()) {
            $this->logger->error($throwable->getMessage(), [
                'trace' => $throwable->getTraceAsString(),
                'previous' => null !== $throwable->getPrevious() ? $throwable->getPrevious()->getMessage() : '',
            ]);
        }
    }

    private function getResponse(ExceptionMapping $mapping, \Throwable $throwable): Response
    {
        if ($mapping->isHidden() && !$this->isDebug) {
            $message = Response::$statusTexts[$mapping->getCode()];
        } else {
            $message = $throwable->getMessage();
        }

        $details = $this->isDebug ? new ErrorDebugDetails($throwable->getTraceAsString()) : null;
        $data = $this->serializer->serialize(new ErrorResponse($message, $details), JsonEncoder::FORMAT);
        $response = new JsonResponse($data, $mapping->getCode(), [], true);

        return $response;
    }
}
