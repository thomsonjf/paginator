<?php

namespace ThomsonJf\Pagination;

/**
 * Interface PaginationInterface
 * @package ThomsonJf\Pagination
 */
interface PaginationInterface
{
    /**
     * Get a list of elements for the current page, paginated
     * according to the perPage settings.
     *
     * @param int $page
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function paginate(int $page): array;

    /**
     * Determine if there is another page after the current one
     *
     * @return bool
     */
    public function hasNextPage(): bool;

    /**
     * Determine if there is a previous page before the current one
     *
     * @return bool
     */
    public function hasPreviousPage(): bool;

    /**
     * Return the count of elements on the current page
     *
     * @return int
     */
    public function getCurrentPageElementsCount(): int ;

    /**
     * Return the total count of elements accross all pages
     *
     * @return int
     */
    public function getTotalElementsCount(): int;

    /**
     * Get an array list of pages
     *
     * @return array
     */
    public function getPagesList(): array;

    /**
     * Get the current per page setting
     *
     * @return int
     */
    public function getPageCount(): int;
}
