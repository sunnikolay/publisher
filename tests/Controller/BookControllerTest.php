<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Tests\AbstractControllerTest;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class BookControllerTest extends AbstractControllerTest
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testBookByCategory()
    {
        $categoryId = $this->createCategory();

        $this->client->request('GET', '/api/v1/category/'.$categoryId.'/books');
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($response, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug', 'image', 'authors', 'meap', 'publicationDate'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                            'publicationDate' => ['type' => 'integer'],
                            'image' => ['type' => 'string'],
                            'meap' => ['type' => 'boolean'],
                            'authors' => ['type' => 'array', 'items' => ['type' => 'string']],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    private function createCategory(): int
    {
        $bc = (new BookCategory())->setTitle('Devices')->setSlug('devices');
        $this->em->persist($bc);

        $this->em->persist((new Book())
            ->setTitle('Test Book')
            ->setImage('This an image')
            ->setMeap(false)
            ->setIsbn('123123')
            ->setDescription('Test description')
            ->setPublicationDate(new \DateTimeImmutable())
            ->setAuthors(['Tester'])
            ->setCategories(new ArrayCollection([$bc]))
            ->setSlug('test-book')
        );

        $this->em->flush();

        return $bc->getId();
    }
}
