<?php

namespace App\Controller;

use App\Model\ErrorResponse;
use App\Model\SubscriberRequest;
use App\Service\SubscriberService;
use App\utility\Attribute\RequestBody;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriberController extends AbstractController
{
    public function __construct(private readonly SubscriberService $service)
    {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Subscribe email to newsletter mailing list",
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(
     *     @Model(type=SubscriberRequest::class)
     * )
     */
    #[Route(path: '/api/v1/subscribe', name: 'app_subscriber_action', methods: ['POST'])]
    public function action(#[RequestBody] SubscriberRequest $dto): Response
    {
        $this->service->subscribe($dto);

        return $this->json([1 => 1]);
    }
}
