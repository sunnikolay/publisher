<?php

namespace App\Controller;

use App\Model\ReviewPage;
use App\Service\ReviewService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    public function __construct(private readonly ReviewService $service)
    {
    }

    /**
     * @OA\Parameter(name="page", in="query", description="Page number", @OA\Schema(type="integer"))
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns page of reviews for the given book",
     *
     *     @Model(type=ReviewPage::class)
     * )
     */
    #[Route(path: '/api/v1/book/{id}/reviews', methods: ['GET'])]
    public function reviews(int $id, Request $request): Response
    {
        return $this->json($this->service->getReviewPageByBookId($id, $request->query->get('page', 1)));
    }
}
