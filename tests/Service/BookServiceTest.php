<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Exception\BookCategoryNotFoundException;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use App\Service\BookService;
use App\Service\RatingService;
use App\Tests\TestUtility;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class BookServiceTest extends TestCase
{
    use TestUtility;

    private BookCategoryRepository $bookCategoryRepo;
    private BookRepository $bookRepo;
    private ReviewRepository $reviewRepo;
    private RatingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookCategoryRepo = $this->createMock(BookCategoryRepository::class);
        $this->bookRepo = $this->createMock(BookRepository::class);
        $this->reviewRepo = $this->createMock(ReviewRepository::class);
        $this->service = $this->createMock(RatingService::class);
    }

    /**
     * @testdox Method will throw BookCategoryNotFoundException
     */
    public function testGetBooksByCategoryNotFound(): void
    {
        $this->bookCategoryRepo->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(false);

        $this->expectException(BookCategoryNotFoundException::class);
        $service = $this->createService();
        $service->getBookByCategory(130);
    }

    /**
     * @testdox Method getBookByCategory return correct response
     */
    public function testGetBooksByCategory(): void
    {
        $this->bookRepo->expects($this->once())
            ->method('findBookByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()]);

        $this->bookCategoryRepo->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(true);

        $service = $this->createService();
        $expected = new BookListResponse([$this->createBookItemsEntity()]);

        $this->assertEquals($expected, $service->getBookByCategory(130));
    }

    private function createBookEntity(): Book
    {
        $book = (new Book())
            ->setTitle('Test Book')
            ->setSlug('This a slug')
            ->setMeap(false)
            ->setIsbn('123123')
            ->setDescription('Test description')
            ->setAuthors(['Test'])
            ->setImage('This an image URL')
            ->setCategories(new ArrayCollection())
            ->setPublicationDate(new \DateTime('2023-09-14'));
        $this->setField($book, 130);

        return $book;
    }

    private function createBookItemsEntity(): BookListItem
    {
        return (new BookListItem())
            ->setId(130)
            ->setTitle('Test Book')
            ->setSlug('This a slug')
            ->setMeap(false)
            ->setAuthors(['Test'])
            ->setImage('This an image URL')
            ->setPublicationDate(1694649600);
    }

    private function createService(): BookService
    {
        return new BookService($this->bookCategoryRepo, $this->bookRepo, $this->reviewRepo, $this->service);
    }
}
