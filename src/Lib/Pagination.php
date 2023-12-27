<?php

namespace App\Lib;

class Pagination
{
    private $totalItems;
    private $itemsPerPage;
    private $currentPage;
    private $totalPages;

    public function __construct(int $totalItems, int $itemsPerPage, int $currentPage)
    {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->setCurrentPage($currentPage);
        $this->calculateTotalPages();
    }

    private function calculateTotalPages()
    {
        $this->totalPages = ceil($this->totalItems / $this->itemsPerPage);
    }

    private function setCurrentPage($currentPage)
    {
        $this->currentPage = max(1, min($currentPage, $this->totalPages));
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getOffset(): int
    {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }
}