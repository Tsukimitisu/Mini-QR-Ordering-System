<?php
/**
 * Pagination - Pagination helper for database queries
 * Manages offset, limit, and page calculations
 */

class Pagination
{
    private $total;
    private $perPage;
    private $currentPage;
    private $basePath;

    public function __construct(int $total, int $perPage = 15, int $currentPage = 1, string $basePath = '')
    {
        $this->total = max(0, $total);
        $this->perPage = max(1, $perPage);
        $this->currentPage = max(1, $currentPage);
        $this->basePath = $basePath;
    }

    /**
     * Get total number of items
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Get items per page
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Get current page number
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Get total number of pages
     */
    public function getTotalPages(): int
    {
        return (int)ceil($this->total / $this->perPage);
    }

    /**
     * Get offset for database query
     */
    public function getOffset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    /**
     * Get limit for database query
     */
    public function getLimit(): int
    {
        return $this->perPage;
    }

    /**
     * Check if current page is the first page
     */
    public function isFirstPage(): bool
    {
        return $this->currentPage === 1;
    }

    /**
     * Check if current page is the last page
     */
    public function isLastPage(): bool
    {
        return $this->currentPage >= $this->getTotalPages();
    }

    /**
     * Get next page number
     */
    public function getNextPage(): ?int
    {
        if ($this->isLastPage()) {
            return null;
        }

        return $this->currentPage + 1;
    }

    /**
     * Get previous page number
     */
    public function getPreviousPage(): ?int
    {
        if ($this->isFirstPage()) {
            return null;
        }

        return $this->currentPage - 1;
    }

    /**
     * Get next page URL
     */
    public function getNextUrl(): ?string
    {
        $nextPage = $this->getNextPage();

        if ($nextPage === null) {
            return null;
        }

        return $this->buildUrl($nextPage);
    }

    /**
     * Get previous page URL
     */
    public function getPreviousUrl(): ?string
    {
        $previousPage = $this->getPreviousPage();

        if ($previousPage === null) {
            return null;
        }

        return $this->buildUrl($previousPage);
    }

    /**
     * Get page numbers for pagination display
     */
    public function getPageNumbers(int $window = 5): array
    {
        $totalPages = $this->getTotalPages();
        $start = max(1, $this->currentPage - (int)floor($window / 2));
        $end = min($totalPages, $start + $window - 1);

        if ($end - $start < $window - 1) {
            $start = max(1, $end - $window + 1);
        }

        $pages = [];

        for ($i = $start; $i <= $end; $i++) {
            $pages[] = [
                'number' => $i,
                'url' => $this->buildUrl($i),
                'is_active' => $i === $this->currentPage,
            ];
        }

        return $pages;
    }

    /**
     * Get pagination metadata
     */
    public function getMetadata(): array
    {
        return [
            'total' => $this->total,
            'per_page' => $this->perPage,
            'current_page' => $this->currentPage,
            'total_pages' => $this->getTotalPages(),
            'from' => $this->getOffset() + 1,
            'to' => min($this->getOffset() + $this->perPage, $this->total),
            'has_next' => !$this->isLastPage(),
            'has_previous' => !$this->isFirstPage(),
            'next_page' => $this->getNextPage(),
            'previous_page' => $this->getPreviousPage(),
        ];
    }

    /**
     * Build URL for a specific page
     */
    private function buildUrl(int $page): string
    {
        $separator = strpos($this->basePath, '?') !== false ? '&' : '?';
        return $this->basePath . $separator . 'page=' . $page;
    }

    /**
     * Create pagination from request
     */
    public static function fromRequest(int $total, int $perPage = 15, string $pageParam = 'page', string $basePath = ''): self
    {
        $page = (int)($_GET[$pageParam] ?? 1);
        return new self($total, $perPage, $page, $basePath);
    }
}
?>
