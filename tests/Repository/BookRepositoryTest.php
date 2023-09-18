<?php

namespace App\Tests\Repository;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Repository\BookRepository;
use App\Tests\AbstractRepositoryTest;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Exception\ORMException;

class BookRepositoryTest extends AbstractRepositoryTest
{
    private BookRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repo = $this->getRepositoryForEntity(Book::class);
    }

    /**
     * @throws ORMException
     */
    public function testFindBookByCategoryId()
    {
        $category = (new BookCategory())->setTitle('Devices')->setSlug('devices');
        $this->em->persist($category);

        for ($i = 0; $i < 5; ++$i) {
            $book = $this->createBook('device-'.$i, $category);
            $this->em->persist($book);
        }

        $this->em->flush();

        $this->assertCount(5, $this->repo->findBookByCategoryId($category->getId()));
    }

    private function createBook(string $title, BookCategory $category): Book
    {
        return (new Book())
            ->setPublicationDate(new \DateTime())
            ->setAuthors(['author'])
            ->setMeap(false)
            ->setSlug($title)
            ->setCategories(new ArrayCollection([$category]))
            ->setTitle($title)
            ->setImage('http://localhost/'.$title.'.png');
    }
}
