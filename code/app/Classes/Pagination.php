<?php

namespace App\Classes;

class Pagination {

    private int $offset;
    private int $pageItemsCount;
    private ?int $totalItemsCount;

    private ?int $minRequestDelay;
    private ?int $maxRequestDelay;

    private const DELAY_RATE = 1.3;

    /**
     * Getting pagination instance
     *
     * @param int|null $totalItemsCount Total items in all pages <br>
     * Set null if you still don't know total items
     * @param int $offset Current offset
     * @param int $pageItemsCount Count items on page
     */
    public function __construct(
        int|null $totalItemsCount,
        int $offset = 0,
        int $pageItemsCount = 10,
    )
    {
        $this->totalItemsCount = $totalItemsCount;
        $this->offset = $offset;
        $this->pageItemsCount = $pageItemsCount;
    }

    /**
     * Set total items count for this instance
     *
     * @param int $totalItemsCount
     * @return $this
     */
    public function setTotalItemsCount(int $totalItemsCount):Pagination
    {
        $this->totalItemsCount  = $totalItemsCount;
        return  $this;
    }

    /**
     * Get total items count
     *
     * @return null|int
     */
    public function getTotalItemsCount(): null|int
    {
        return $this->totalItemsCount;
    }

    /**
     * Getting current offset
     *
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * Getting items count on this page
     *
     * @return int
     */
    public function getItemsCount(): int
    {
        return $this->pageItemsCount;
    }

    /**
     * Return true if is first page
     *
     * @return bool
     */
    public function isFirstPage(): bool
    {
        return $this->offset === 0;
    }

    /**
     * Return true if is last page
     *
     * @return bool
     */
    private function isLastPage():bool
    {
        $nextOffsetPage = $this->offset + $this->pageItemsCount;

        return (
            !is_null($this->totalItemsCount)
            && $nextOffsetPage > $this->totalItemsCount
        );
    }


    /**
     * Sometimes you need to make slow down your requests,
     * because services will return 422 status code
     *
     * @param int $seconds Minimum delay between requests <br>
     * * Maximum delay will be determined automatically
     * @return self
     */
    public function addRateLimiter(int $seconds):self
    {
        $this->minRequestDelay = $seconds;
        $this->maxRequestDelay = $seconds * self::DELAY_RATE;

        return $this;
    }

    /**
     * Getting current rate limit
     *
     * @return false|int Int - limit in seconds <br>
     * or false if limit is not defined yet
     */
    private function getCurrentRateLimit():false|int
    {
        if (is_null($this->minRequestDelay)) {
            return false;
        }

        return rand($this->minRequestDelay, $this->maxRequestDelay);
    }

    /**
     * Go to next page and getting new page instance
     *
     * @return false|Pagination false -> Is last page, and you cannot to get next page; <br>
     * otherwise -> Getting next page info
     */
    public function nextPage(): bool|Pagination
    {
        if ($delay = $this->getCurrentRateLimit()) {
            sleep($delay);
        }

        if ($this->isLastPage()) {
            return false;
        }

        $this->offset = $this->offset + $this->pageItemsCount;
        return $this;
    }
}
