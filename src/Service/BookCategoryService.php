<?php

namespace App\Service;

use App\Entity\BookCategory;
use App\Model\BookCategoryList;
use App\Model\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;

class BookCategoryService
{
    public function __construct(private BookCategoryRepository $repository)
    {
    }

    public function getCategories(): BookCategoryListResponse
    {
        $categories = $this->repository->findAllSortedByTitle();
        $items = array_map(
            fn (BookCategory $bc) => new BookCategoryList($bc->getId(), $bc->getTitle(), $bc->getSlug()),
            $categories
        );

        return new BookCategoryListResponse($items);
    }
}
