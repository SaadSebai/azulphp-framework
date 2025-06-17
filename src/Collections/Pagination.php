<?php

namespace Azulphp\Collections;

/**
 * @template T
 */
class Pagination implements Arrayable
{
    /**
     * @var Collection<T>
     */
    protected Collection $collection;
    protected int $total;

    public function __construct(
        array $items = [],
        protected int $perPage = 15,
        protected int $currentPage = 1,
        protected int $totalPages = 1
    )
    {
        $this->collection = new Collection($items);
        $this->total = count($items);
    }

    /**
     * @return Collection<T>
     */
    public function getCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * Transform Pagination's collection to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->collection->toArray();
    }

    /**
     * Total elements in the collection.
     *
     * @return int
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * Number of elements in the collection.
     *
     * @return int
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * The current Page offset.
     *
     * @return int
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Number of pages.
     *
     * @return int
     */
    public function totalPages(): int
    {
        return $this->totalPages;
    }

    public function nextPage(): int
    {
        return $this->currentPage + 1;
    }

    public function previousPage(): int
    {
        return max($this->currentPage - 1, 0);
    }

    /**
     * Is the collection empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->collection->isEmpty();
    }

    /**
     * Add pagination to the view.
     * Make sure you have the correct path to the pagination.
     *
     * @return void
     */
    public function component(): void
    {
        $data = $this;

        require base_path('resources/views/layouts/partials/pagination.layout.php');
    }

    public function toArrayTree(): array
    {
        return $this->collection->toArrayTree();
    }
}