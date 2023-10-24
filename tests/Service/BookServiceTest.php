<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookFormat;
use App\Entity\BookToBookFormat;
use App\Exception\BookCategoryNotFoundException;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookDetails;
use App\Model\BookFormat as BookFormatModel;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Service\BookService;
use App\Service\Rating;
use App\Service\RatingService;
use App\Tests\TestUtility;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class BookServiceTest extends TestCase
{
    use TestUtility;

    private BookCategoryRepository $bookCategoryRepo;
    private BookRepository $bookRepo;
    private RatingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookCategoryRepo = $this->createMock(BookCategoryRepository::class);
        $this->bookRepo = $this->createMock(BookRepository::class);
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
            ->with(123)
            ->willReturn([$this->createBookEntity()]);

        $this->bookCategoryRepo->expects($this->once())
            ->method('existsById')
            ->with(123)
            ->willReturn(true);

        $service = $this->createService();
        $expected = new BookListResponse([$this->createBookItemsEntity()]);

        $this->assertEquals($expected, $service->getBookByCategory(123));
    }

    public function testGetBookById(): void
    {
        $this->bookRepo->expects($this->once())
            ->method('getById')
            ->with(123)
            ->willReturn($this->createBookEntity());

        $this->service->expects($this->once())
            ->method('calcReviewRatingForBook')
            ->with(123)
            ->willReturn(new Rating(10, 5.5));

        $format = (new BookFormatModel())
            ->setId(1)
            ->setTitle('format')
            ->setDescription('description format')
            ->setComment(null)
            ->setPrice(123.55)
            ->setDiscountPercent(5);

        $expected = (new BookDetails())
            ->setId(123)
            ->setRating(5.5)
            ->setReviews(10)
            ->setSlug('This a slug')
            ->setTitle('Test Book')
            ->setImage('http://localhost/test.png')
            ->setAuthors(['tester'])
            ->setMeap(false)
            ->setCategories([
                new BookCategoryModel(1, 'Category', 'category'),
            ])
            ->setPublicationDate(1602288000)
            ->setFormats([$format]);

        $this->assertEquals($expected, $this->createService()->getBookById(123));
    }

    private function createBookEntity(): Book
    {
        $category = (new BookCategory())->setTitle('Category')->setSlug('category');
        $this->setField($category, 1);

        $format = (new BookFormat())
            ->setTitle('format')
            ->setDescription('description format')
            ->setComment(null);
        $this->setField($format, 1);

        $join = (new BookToBookFormat())->setPrice(123.55)->setFormat($format)->setDiscountPercent(5);
        $this->setField($join, 1);

        $book = (new Book())
            ->setTitle('Test Book')
            ->setSlug('This a slug')
            ->setMeap(false)
            ->setIsbn('123123')
            ->setDescription('Test description')
            ->setAuthors(['tester'])
            ->setImage('http://localhost/test.png')
            ->setCategories(new ArrayCollection([$category]))
            ->setPublicationDate(new \DateTime('2020-10-10'))
            ->setFormats(new ArrayCollection([$join]));
        $this->setField($book, 123);

        return $book;
    }

    private function createBookItemsEntity(): BookListItem
    {
        return (new BookListItem())
            ->setId(123)
            ->setTitle('Test Book')
            ->setSlug('This a slug')
            ->setMeap(false)
            ->setAuthors(['tester'])
            ->setImage('http://localhost/test.png')
            ->setPublicationDate(1602288000);
    }

    private function createService(): BookService
    {
        return new BookService($this->bookCategoryRepo, $this->bookRepo, $this->service);
    }
}
