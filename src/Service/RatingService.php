<?php

namespace App\Service;

use App\Repository\ReviewRepository;

class RatingService
{
    public function __construct(private readonly ReviewRepository $reviewRepository)
    {
    }

    public function calcReviewRatingForBook(int $id, int $total): float
    {
        if (0 === $total) {
            return 0;
        }

        $sum = 0;
        try {
            $sum = $this->reviewRepository->getBookTotalRatingSum($id);
        } catch (\Exception $ignore) {
        }

        return $sum / $total;
    }
}
