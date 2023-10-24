<?php

namespace App\Service;

use App\Repository\ReviewRepository;

class RatingService
{
    public function __construct(private readonly ReviewRepository $repo)
    {
    }

    public function calcReviewRatingForBook(int $id): Rating
    {
        $total = $this->repo->countByBookId($id);

        $rating = new Rating($total, 0);
        if (0 === $total) {
            return $rating;
        }

        $sum = 0;
        try {
            $sum = $this->repo->getBookTotalRatingSum($id);
        } catch (\Exception $ignore) {
        }

        $rating->setRating($sum / $total);

        return $rating;
    }
}
