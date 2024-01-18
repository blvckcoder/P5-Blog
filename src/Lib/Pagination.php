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
        return intval(ceil($this->totalItems / $this->itemsPerPage));
    }
    
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getOffset(): int
    {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }

    public function renderHtml(): string {
        $html = '<div class="row"><div class="col-sm-7 text-center"><ul class="pagination pagination-dark mt-4">';

        // Bouton Précédent
        $prevClass = $this->getCurrentPage() <= 1 ? 'disabled' : '';
        $html .= '<li class="page-item ms-auto ' . $prevClass . '">';
        $html .= '<a class="page-link ' . $prevClass . '" href="?page=' . ($this->getCurrentPage() - 1) . '" aria-label="Previous">';
        $html .= '<span aria-hidden="true"><i class="fa fa-angle-double-left" aria-hidden="true"></i></span></a></li>';

        // Numéros de Page
        for ($i = 1; $i <= $this->getTotalPages(); $i++) {
            $activeClass = $i == $this->getCurrentPage() ? 'active' : '';
            $html .= '<li class="page-item ' . $activeClass . '">';
            $html .= '<a class="page-link ' . $activeClass . '" href="?page=' . $i . '">' . $i . '</a></li>';
        }

        // Bouton Suivant
        $nextClass = $this->getCurrentPage() >= $this->getTotalPages() ? 'disabled' : '';
        $html .= '<li class="page-item ' . $nextClass . '">';
        $html .= '<a class="page-link ' . $nextClass . '" href="?page=' . ($this->getCurrentPage() + 1) . '" aria-label="Next">';
        $html .= '<span aria-hidden="true"><i class="fa fa-angle-double-right" aria-hidden="true"></i></span></a></li>';

        $html .= '</ul></div></div>';

        return $html;
    }


}