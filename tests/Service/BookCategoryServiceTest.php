<?php

namespace App\Tests\Service;

use App\Entity\BookCategory;
use App\model\BookCategoryList;
use App\model\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;
use App\Service\BookCategoryService;
use Doctrine\Common\Collections\Criteria;
use PHPUnit\Framework\TestCase;

class BookCategoryServiceTest extends TestCase
{

    public function testGetCategories(): void
    {
        $repo = $this->createMock(BookCategoryRepository::class);
        $repo->expects($this->once())
            ->method('findBy')
            ->with([], ['title' => Criteria::ASC])
            ->willReturn([(new BookCategory())->setId(2)->setTitle('Test')->setSlug('test')]);

        $service = new BookCategoryService($repo);
        $expected = new BookCategoryListResponse([new BookCategoryList(2, 'Test', 'test')]);

        $this->assertEquals($expected, $service->getCategories());
    }
}
