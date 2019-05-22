<?php

namespace ThomsonJf\Pagination\Paginator;

use ThomsonJf\Pagination\PaginationInterface;

/**
 * Class LengthAwarePaginator
 * @package ThomsonJf\Paginator
 */
class LengthAwarePaginator implements PaginationInterface
{
    /**
     * The total count of elements
     *
     * @var int
     */
    protected $totalCount;

    /**
     * Number of elements per page
     *
     * @var int
     */
    protected $perPage = 10;

    /**
     * Holds the number of the current page
     *
     * @var int
     */
    protected $currentPage = 1;

    /**
     * Iterator object if instantiated with array object or iterator
     *
     * @var \ArrayIterator
     */
    protected $iterator;

    /**
     * Paginator constructor.
     * @param mixed $elements
     * @param int $perPage
     */
    public function __construct($elements, $perPage = 10)
    {
        $this->perPage = $perPage;
        if (is_array($elements)) {
            $this->iterator = new \ArrayIterator($elements);
        } else if ($elements instanceof \ArrayObject) {
            $this->iterator = $elements->getIterator();
        } else if ($elements instanceof \ArrayIterator) {
            $this->iterator = $elements;
        } else {
            throw new \InvalidArgumentException(
                'Invalid type supplied to Paginator constructor - must be ArrayObject, Traversable or array'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $page): array
    {
        // Set current page (used in other calculations)
        $this->currentPage = $page;

        // seek to offset (one based index)
        $offset = ($page - 1) * $this->perPage;

        try {
            if ($offset > 0) {
                $this->iterator->seek($offset);
            } else {
                $this->iterator->rewind();
            }
        } catch(\OutOfBoundsException $ex) {
            throw new \InvalidArgumentException(
                sprintf('The page requested [%d] does not exist', $page)
            );
        }

        $pageElements = [];
        $count = 0;
        while ($this->iterator->valid() && $count < $this->perPage) {
            $pageElements[] = $this->iterator->current();
            $this->iterator->next();
            ++$count;
        }

        return $pageElements;
    }

    /**
     * {@inheritdoc}
     */
    public function hasNextPage(): bool
    {
        return $this->getPageCount() > 0 ? $this->getPageCount() > $this->currentPage : false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPreviousPage(): bool
    {
        return $this->getPageCount() > 1 && $this->currentPage > 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPageElementsCount(): int
    {
        if ($this->getPageCount() > 0) {
            // It's only the last page that's likely to have less than perPage records
            if ($this->currentPage === $this->getPageCount()) {
                return $this->getTotalElementsCount() - (($this->getPageCount() - 1) * $this->perPage);
            }

            return $this->perPage;
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalElementsCount(): int
    {
        if (null === $this->totalCount) {
            $this->totalCount = iterator_count($this->iterator);
        }

       return $this->totalCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getPagesList(): array
    {
        if ($this->getPageCount() > 0) {
            return range(1, $this->getPageCount());
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getPageCount(): int
    {
        if ($this->getTotalElementsCount() > 0) {
            return ceil($this->getTotalElementsCount() / $this->perPage);
        }

        return 0;
    }
}
