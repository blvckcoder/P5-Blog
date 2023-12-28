<?php

namespace App\Lib;

class Pagination
{
    private $totalItems;
    private $itemsPerPage;
    private $currentPage;

    public function __construct(int $totalItems, int $itemsPerPage, int $currentPage)
    {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = max(1, min($currentPage, $this->getTotalPages()));
    }
    
    public function getTotalPages(): int 
    {
        return ceil($this->totalItems / $this->itemsPerPage);
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