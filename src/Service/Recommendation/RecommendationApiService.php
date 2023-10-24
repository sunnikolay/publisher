<?php

namespace App\Service\Recommendation;

use App\Service\Recommendation\Exception\AccessDeniedException;
use App\Service\Recommendation\Exception\RequestException;
use App\Service\Recommendation\Model\RecommendationResponse;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RecommendationApiService
{
    /**
     * @param HttpClientInterface $recommendationClient - framework.yaml -> recommendation.client
     */
    public function __construct(
        private readonly HttpClientInterface $recommendationClient,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * @throws AccessDeniedException
     * @throws RequestException
     */
    public function getRecommendationByBookId(int $bookId): RecommendationResponse
    {
        try {
            $response = $this->recommendationClient->request('GET', '/api/v1/book/'.$bookId.'/recommendations');

            return $this->serializer->deserialize(
                $response->getContent(),
                RecommendationResponse::class,
                JsonEncoder::FORMAT
            );
        } catch (\Throwable $throwable) {
            if ($throwable instanceof ClientException && Response::HTTP_FORBIDDEN === $throwable->getCode()) {
                throw new AccessDeniedException();
            }

            throw new RequestException($throwable->getMessage(), $throwable);
        }
    }
}
