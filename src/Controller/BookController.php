<?php

namespace App\Controller;

use App\Exception\BookCategoryNotFoundException;
use App\model\BookListResponse;
use App\Service\BookService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    public function __construct(private BookService $service)
    {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns books inside a category",
     *
     *     @Model(type=BookListResponse::class)
     * )
     */
    #[Route(path: '/api/v1/category/{id}/books')]
    public function bookByCategory(int $id): Response
    {
        try {
            return $this->json($this->service->getBookByCategory($id));
        } catch (BookCategoryNotFoundException $exception) {
            throw new HttpException($exception->getCode(), $exception->getMessage());
        }
    }
}
