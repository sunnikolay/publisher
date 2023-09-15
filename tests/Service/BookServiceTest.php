<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Exception\BookCategoryNotFoundException;
use App\model\BookListItem;
use App\model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Service\BookService;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class BookServiceTest extends TestCase
{
    /**
     * @testdox Method will throw BookCategoryNotFoundException
     */
    public function testGetBooksByCategoryNotFound(): void
    {
        $bookRepo = $this->createMock(BookRepository::class);
        $bookCategoryRepo = $this->createMock(BookCategoryRepository::class);
        $bookCategoryRepo->expects($this->once())
            ->method('find')
            ->with(130)
            ->willThrowException(new BookCategoryNotFoundException());

        $this->expectException(BookCategoryNotFoundException::class);

        (new BookService($bookCategoryRepo, $bookRepo))->getBookByCategory(130);
    }

    /**
     * @testdox Method getBookByCategory return correct response
     */
    public function testGetBooksByCategory(): void
    {
        $bookRepo = $this->createMock(BookRepository::class);
        $bookRepo->expects($this->once())
            ->method('findBookByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()]);

        $bookCategoryRepo = $this->createMock(BookCategoryRepository::class);
        $bookCategoryRepo->expects($this->once())
            ->method('find')
            ->with(130)
            ->willReturn(new BookCategory());

        $service = new BookService($bookCategoryRepo, $bookRepo);
        $expected = new BookListResponse([$this->createBookItemsEntity()]);

        $this->assertEquals($expected, $service->getBookByCategory(130));
    }

    private function createBookEntity(): Book
    {
        return (new Book())
            ->setId(1)
            ->setTitle('Test Book')
            ->setSlug('This a slug')
            ->setMeap(false)
            ->setAuthors(['Test'])
            ->setImage('This an image URL')
            ->setCategories(new ArrayCollection())
            ->setPublicationDate(new \DateTime('2023-09-14'));
    }

    private function createBookItemsEntity(): BookListItem
    {
        return (new BookListItem())
            ->setId(1)
            ->setTitle('Test Book')
            ->setSlug('This a slug')
            ->setMeap(false)
            ->setAuthors(['Test'])
            ->setImage('This an image URL')
            ->setPublicationDate(1694649600);
    }
}
