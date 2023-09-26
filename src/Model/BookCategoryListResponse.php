<?php

namespace App\Model;

class BookCategoryListResponse
{
    /**
     * @var BookCategoryList[]
     */
    private array $items;

    /**
     * @param BookCategoryList[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return BookCategoryList[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
