<?php

use PHPUnit\Framework\TestCase;
use ThomsonJf\Pagination\Paginator\LengthAwarePaginator;

/**
 * Class PaginatorTest
 */
final class LengthAwarePaginatorTest extends TestCase
{
    /**
     * Test a range of input types to anchor input validation
     *
     * @dataProvider dataProviderPaginatorValidInputs
     */
    public function testPaginatorValidInputs($input, $expectedException, $expectedCount): void
    {
        if (null !== $expectedException) {
            $this->expectException($expectedException);
        }

        $paginator = new LengthAwarePaginator($input);

        if (null !== $expectedCount) {
            $this->assertEquals($paginator->getTotalElementsCount(), $expectedCount);
        }
    }

    /**
     * Test assertion of paginator properties with different input types
     *
     * @dataProvider dataProviderPropertiesWithDifferentInputTypes
     * @param mixed $input
     */
    public function testPaginatorPropertiesWithDifferentInputTypes($input): void
    {
        $paginator = new LengthAwarePaginator($input, 10);

        // initial state, no previous page
        $this->assertFalse($paginator->hasPreviousPage());

        $this->assertEquals($paginator->getTotalElementsCount(), 100);
        $this->assertEquals($paginator->getPageCount(), 10);
        $this->assertCount(10, $paginator->getPagesList());

        foreach ($paginator->getPagesList() as $page) {
            $elements = $paginator->paginate($page);
            $this->assertEquals(10, $paginator->getCurrentPageElementsCount());

            if ($page !== 10) {
                // if not last page, we should have a next page
                $this->assertTrue($paginator->hasNextPage());
            } else {
                $this->assertTrue($paginator->hasPreviousPage());
            }
        }

        // end state, no next page
        $this->assertFalse($paginator->hasNextPage());
    }

    /**
     * Test to ensure calculations of last page size are correct
     */
    public function testPaginatorPropertiesWithShortenedLastPage(): void
    {
        $paginator = new LengthAwarePaginator(range(1, 945), 60);

        // initial state, no previous page
        $this->assertFalse($paginator->hasPreviousPage());

        $this->assertEquals($paginator->getTotalElementsCount(), 945);
        $this->assertEquals($paginator->getPageCount(), 16);
        $this->assertCount(16, $paginator->getPagesList());

        foreach ($paginator->getPagesList() as $page) {
            $elements = $paginator->paginate($page);
            if ($page !== 16) {
                // if not last page, we should have a next page, and 60 elements
                $this->assertTrue($paginator->hasNextPage());
                $this->assertEquals(60, $paginator->getCurrentPageElementsCount());
                $this->assertCount(60, $elements);
            } else {
                // if it's last page, we shouldn't have a next page, and only 15 elements
                $this->assertFalse($paginator->hasNextPage());
                $this->assertTrue($paginator->hasPreviousPage());
                $this->assertEquals(45, $paginator->getCurrentPageElementsCount());
                $this->assertCount(45, $elements);
            }
        }

        // end state, no next page
        $this->assertFalse($paginator->hasNextPage());
    }

    /**
     * Test defensive behaviour with out of range pages
     */
    public function testPaginatorAccessNotExistingPages(): void
    {
        $paginator = new LengthAwarePaginator(range(1, 23), 5);

        // initial state, no previous page
        $this->assertFalse($paginator->hasPreviousPage());

        $this->assertEquals($paginator->getTotalElementsCount(), 23);
        $this->assertEquals($paginator->getPageCount(), 5);
        $this->assertCount(5, $paginator->getPagesList());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The page requested [10] does not exist');

        // access out of bounds pages
        $elements = $paginator->paginate(10);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The page requested [-10] does not exist');

        // access out of bounds pages
        $elements = $paginator->paginate(-10);
    }

    /**
     * Provides test scenarios with varying input types
     *
     * @return array
     */
    public function dataProviderPropertiesWithDifferentInputTypes(): array
    {
        return [
            // array
            [range(1, 100)],
            // ArrayObject
            [new ArrayObject(range(1, 100))],
            // ArrayIterator
            [(new ArrayObject(range(1, 100)))->getIterator()],
        ];
    }

    /**
     * Provides test scenarios to the test paginator inputs test
     *
     * @return array
     */
    public function dataProviderPaginatorValidInputs(): array
    {
        return [
            // null input
            [null, \InvalidArgumentException::class, null],
            // scalar input
            ['hello', \InvalidArgumentException::class, null],
            // incompatible object input
            [new \stdClass(), \InvalidArgumentException::class, null],
            // array input
            [[1, 2, 3, 4, 5, 'hello'], null, 6],
            // array object input
            [new \ArrayObject([1, 2, 3]), null, 3],
            // array iterator input
            [new \ArrayIterator(['hello', 'you']), null, 2]
        ];
    }
}
