<?php

namespace App\Tests\Service;

use App\Entity\Review;
use App\Model\Review as ReviewModel;
use App\Model\ReviewPage;
use App\Repository\ReviewRepository;
use App\Service\RatingService;
use App\Service\ReviewService;
use App\Tests\TestUtility;
use PHPUnit\Framework\TestCase;

class ReviewServiceTest extends TestCase
{
    use TestUtility;

    private ReviewRepository $repo;
    private RatingService $service;

    private const BOOK_ID = 1;
    private const PER_PAGE = 5;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repo = $this->createMock(ReviewRepository::class);
        $this->service = $this->createMock(RatingService::class);
    }

    public function dataProvider(): array
    {
        return [
            [0, 0],
            [-1, 0],
            [-20, 0],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetReviewPageByBookIdInvalidPage(int $page, int $offset): void
    {
        $this->service->expects($this->once())
            ->method('calcReviewRatingForBook')
            ->with(self::BOOK_ID, 0)
            ->willReturn(0.0);

        $this->repo->expects($this->once())
            ->method('getPageByBookId')
            ->with(self::BOOK_ID, $offset, self::PER_PAGE)
            ->willReturn(new \ArrayIterator());

        $service = new ReviewService($this->repo, $this->service);
        $expected = (new ReviewPage())
            ->setTotal(0)
            ->setRating(0)
            ->setPage($page)
            ->setPages(0)
            ->setPerPage(self::PER_PAGE)
            ->setItems([]);

        $this->assertEquals($expected, $service->getReviewPageByBookId(self::BOOK_ID, $page));
    }

    public function testGetReviewPageByBookId(): void
    {
        $this->service->expects($this->once())
            ->method('calcReviewRatingForBook')
            ->with(self::BOOK_ID, 1)
            ->willReturn(4.0);

        $entity = (new Review())->setAuthor('tester')->setContent('test content')
            ->setCreatedAt(new \DateTimeImmutable('2023-10-18'))->setRating(4);
        $this->setField($entity, 1);

        $this->repo->expects($this->once())
            ->method('getPageByBookId')
            ->with(self::BOOK_ID, 0, self::PER_PAGE)
            ->willReturn(new \ArrayIterator([$entity]));

        $service = new ReviewService($this->repo, $this->service);

        $model = (new ReviewModel())
            ->setId(1)
            ->setRating(4)
            ->setCreatedAt(1697587200)
            ->setContent('test content')
            ->setAuthor('tester');
        $expected = (new ReviewPage())
            ->setTotal(1)
            ->setRating(4)
            ->setPage(1)
            ->setPages(1)
            ->setPerPage(self::PER_PAGE)
            ->setItems([$model]);

        $this->assertEquals($expected, $service->getReviewPageByBookId(self::BOOK_ID, 1));
    }
}
