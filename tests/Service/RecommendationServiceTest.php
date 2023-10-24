<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Model\RecommendedBook;
use App\Model\RecommendedBookListResponse;
use App\Repository\BookRepository;
use App\Service\Recommendation\Model\RecommendationItem;
use App\Service\Recommendation\Model\RecommendationResponse;
use App\Service\Recommendation\RecommendationApiService;
use App\Service\RecommendationService;
use App\Tests\TestUtility;
use PHPUnit\Framework\TestCase;

class RecommendationServiceTest extends TestCase
{
    use TestUtility;

    private BookRepository $repo;
    private RecommendationApiService $api;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repo = $this->createMock(BookRepository::class);
        $this->api = $this->createMock(RecommendationApiService::class);
    }

    private function dataProvider(): array
    {
        return [
            ['short description', 'short description'],
            [
                <<<EOF
begin long description long description
long description long description long
long description long description
long description long description
description
EOF,
                <<<EOF
begin long description long description
long description long description long
long description long description
long description long description
...
EOF
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetRecommendationsByBookId(string $actualDescription, string $expectedDescription): void
    {
        $entity = (new Book())
            ->setImage('image')
            ->setSlug('slug')
            ->setTitle('title')
            ->setDescription($actualDescription);
        $this->setField($entity, 2);

        $this->repo->expects($this->once())
            ->method('findBooksByIds')
            ->with([2])
            ->willReturn([$entity]);

        $this->api->expects($this->once())
            ->method('getRecommendationByBookId')
            ->with(1)
            ->willReturn(
                new RecommendationResponse(1, 1234567890, [new RecommendationItem(2)])
            );

        $expected = new RecommendedBookListResponse([
            (new RecommendedBook())->setTitle('title')->setSlug('slug')->setImage('image')
                ->setId(2)->setShortDescription($expectedDescription),
        ]);

        $this->assertEquals($expected, $this->createService()->getRecommendationsByBookId(1));
    }

    private function createService(): RecommendationService
    {
        return new RecommendationService($this->repo, $this->api);
    }
}
