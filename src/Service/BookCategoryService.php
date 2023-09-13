<?php

namespace App\Service;

use App\Entity\BookCategory;
use App\model\BookCategoryList;
use App\model\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;
use Doctrine\Common\Collections\Criteria;

class BookCategoryService
{
    public function __construct(private BookCategoryRepository $repository)
    {
    }

    public function getCategories(): BookCategoryListResponse
    {
        $categories = $this->repository->findBy([], ['title' => Criteria::ASC]);
        $items = array_map(
            fn (BookCategory $bc) => new BookCategoryList($bc->getId(), $bc->getTitle(), $bc->getSlug()),
            $categories
        );

        return new BookCategoryListResponse($items);
    }
}
