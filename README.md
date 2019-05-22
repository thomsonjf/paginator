# JT Paginator

PHP 7 Paginator Library

**Overview**

The JT Paginator is a simple paginator for use with PHP 7.1.1 and above. It's use of `LengthAwarePaginator` is definitely not a coincidence with a Symfony component of a similar name - it's referring to the fact that the object only accepts Iterators that are also `Countable`.


**Installation &amp; Usage**

Usage is simple, to install, add the following to your project's `composer.json`:

```
"repositories": [
    {
        "url": "https://github.com/thomsonjf/paginator.git",
        "type": "git"
    }
]
```

Then add the library in the usual way using composer:

```
composer require thomsonjf/paginator
```

To use the library:

```
use ThomsonJf\Pagination\Paginator\LengthAwarePaginator;

// Paginate an array of 5 items, 2 per page
$paginator = new LengthAwarePaginator([1, 2, 3, 4, 5], 2);
$paginator->paginate(1);    // 1st page [1, 2]
$paginator->paginate(2);    // 2nd page [3, 4]
$paginator->paginate(3);    // 3rd page [5]

// Also works with \ArrayObject or \ArrayIterator objects
$paginator = new LengthAwarePaginator(new \ArrayObject([1, 2, 3, 4, 5]), 2);
$paginator->paginate(1);    // 1st page [1, 2]
$paginator->paginate(2);    // 2nd page [3, 4]
$paginator->paginate(3);    // 3rd page [5]

// Helper Methods
$paginator->hasNextPage();                  // bool - is there a next page after the current page?
$paginator->hasPreviousPage();              // bool - is there a previous page before the current page?
$paginator->getCurrentPageElementsCount();  // int - get number of elements on current page
$paginator->getTotalElementsCount();        // int - get a count of all elements
$paginator->getPagesList();                 // array - get an integer list of all pages e.g. [1, 2, 3]
$paginator->getPageCount();                 // int - get a count of pages
```

**Helper Methods**



**Tests**
```
$ vendor/bin/phpunit --bootstrap vendor/autoload.php --testdox tests
   PHPUnit 7.5.11-7-g1d4dfbf by Sebastian Bergmann and contributors.
   
   LengthAwarePaginator
    ✔ Paginator valid inputs with data set #0
    ✔ Paginator valid inputs with data set #1
    ✔ Paginator valid inputs with data set #2
    ✔ Paginator valid inputs with data set #3
    ✔ Paginator valid inputs with data set #4
    ✔ Paginator valid inputs with data set #5
    ✔ Paginator properties with different input types with data set #0
    ✔ Paginator properties with different input types with data set #1
    ✔ Paginator properties with different input types with data set #2
    ✔ Paginator properties with shortened last page
   
   Time: 141 ms, Memory: 4.00 MB
   
   OK (10 tests, 135 assertions)
```

