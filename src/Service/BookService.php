<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookToBookFormat;
use App\Exception\BookCategoryNotFoundException;
use App\Mapper\BookMapper;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookDetails;
use App\Model\BookFormat;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\Collection;

class BookService
{
    public function __construct(
        private readonly BookCategoryRepository $categoryRepo,
        private readonly BookRepository $bookRepo,
        private readonly RatingService $ratingService
    ) {
    }

    public function getBookByCategory(int $categoryId): BookListResponse
    {
        if (!$this->categoryRepo->existsById($categoryId)) {
            throw new BookCategoryNotFoundException();
        }

        return new BookListResponse(array_map(
            fn (Book $book) => BookMapper::map($book, new BookListItem()),
            $this->bookRepo->findBookByCategoryId($categoryId)
        ));
    }

    public function getBookById(int $id): BookDetails
    {
        $book = $this->bookRepo->getById($id);
        $rating = $this->ratingService->calcReviewRatingForBook($id);

        $categories = $book->getCategories()
            ->map(fn (BookCategory $category) => new BookCategoryModel(
                $category->getId(), $category->getTitle(), $category->getSlug()
            ))
            ->toArray();

        $formats = $this->mapFormats($book->getFormats());

        return BookMapper::map($book, new BookDetails())
            ->setRating($rating->getRating())
            ->setReviews($rating->getTotal())
            ->setFormats($formats)
            ->setCategories($categories);
    }

    private function mapFormats(Collection $formats): array
    {
        return $formats->map(
            fn (BookToBookFormat $join) => (new BookFormat())
                ->setId($join->getFormat()->getId())
                ->setTitle($join->getFormat()->getTitle())
                ->setDescription($join->getFormat()->getDescription())
                ->setComment($join->getFormat()->getComment())
                ->setPrice($join->getPrice())
                ->setDiscountPercent($join->getDiscountPercent())
        )
            ->toArray();
    }
}
