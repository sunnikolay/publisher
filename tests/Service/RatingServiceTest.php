<?php

namespace App\Tests\Service;

use App\Repository\ReviewRepository;
use App\Service\Rating;
use App\Service\RatingService;
use PHPUnit\Framework\TestCase;

class RatingServiceTest extends TestCase
{
    private ReviewRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repo = $this->createMock(ReviewRepository::class);
    }

    public function provider(): array
    {
        return [
            [25, 20, 1.25],
            [0, 5, 0],
        ];
    }

    /**
     * @dataProvider provider
     */
    public function testCalcReviewRatingForBook(int $sum, int $total, float $expect): void
    {
        $this->repo->expects($this->once())
            ->method('getBookTotalRatingSum')
            ->with(1)
            ->willReturn($sum);

        $this->repo->expects($this->once())
            ->method('countByBookId')
            ->with(1)
            ->willReturn($total);

        $this->assertEquals(
            new Rating($total, $expect),
            (new RatingService($this->repo))->calcReviewRatingForBook(1)
        );
    }

    public function testCalcReviewRatingForBookZeroTotal(): void
    {
        $this->repo->expects($this->never())->method('getBookTotalRatingSum');
        $this->repo->expects($this->once())
            ->method('countByBookId')
            ->with(1)
            ->willReturn(0);

        $this->assertEquals(
            new Rating(0, 0),
            (new RatingService($this->repo))->calcReviewRatingForBook(1)
        );
    }
}
