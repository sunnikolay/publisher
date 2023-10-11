<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookToBookFormat;
use App\Exception\BookCategoryNotFoundException;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookDetails;
use App\Model\BookFormat;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class BookService
{
    public function __construct(
        private BookCategoryRepository $categoryRepo,
        private BookRepository $bookRepo,
        private ReviewRepository $reviewRepository
    ) {
    }

    public function getBookByCategory(int $categoryId): BookListResponse
    {
        if (!$this->categoryRepo->existsById($categoryId)) {
            throw new BookCategoryNotFoundException();
        }

        return new BookListResponse(array_map(
            [$this, 'map'],
            $this->bookRepo->findBookByCategoryId($categoryId)
        ));
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getBookById(int $id): BookDetails
    {
        $book = $this->bookRepo->getById($id);
        $reviews = $this->reviewRepository->countByBookId($id);

        $rating = 0;
        if ($reviews > 0) {
            $rating = $this->reviewRepository->getBookTotalRatingSum($id) / $reviews;
        }

        $categories = $book->getCategories()
            ->map(fn (BookCategory $category) => new BookCategoryModel(
                $category->getId(), $category->getTitle(), $category->getSlug()
            ))
            ->toArray();

        $formats = $this->mapFormats($book->getFormats());

        $details = (new BookDetails())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setAuthors($book->getAuthors())
            ->setMeap($book->isMeap())
            ->setPublicationDate($book->getPublicationDate()->getTimestamp())
            ->setRating($rating)
            ->setReviews($reviews)
            ->setFormats($formats)
            ->setCategories($categories);

        return $details;
    }

    private function map(Book $book): BookListItem
    {
        return (new BookListItem())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setAuthors($book->getAuthors())
            ->setMeap($book->isMeap())
            ->setPublicationDate($book->getPublicationDate()->getTimestamp());
    }

    /**
     * @param Collection $formats
     * @return array
     */
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
