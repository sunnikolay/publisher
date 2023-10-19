<?php

namespace App\Tests\Service;

use App\Entity\BookCategory;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;
use App\Service\BookCategoryService;
use App\Tests\TestUtility;
use PHPUnit\Framework\TestCase;

class BookCategoryServiceTest extends TestCase
{
    use TestUtility;

    public function testGetCategories(): void
    {
        $obj = (new BookCategory())->setTitle('Test')->setSlug('test');
        $this->setField($obj, 2);

        $repo = $this->createMock(BookCategoryRepository::class);
        $repo->expects($this->once())
            ->method('findAllSortedByTitle')
            ->willReturn([$obj]);

        $service = new BookCategoryService($repo);
        $expected = new BookCategoryListResponse([new BookCategoryModel(2, 'Test', 'test')]);

        $this->assertEquals($expected, $service->getCategories());
    }
}
