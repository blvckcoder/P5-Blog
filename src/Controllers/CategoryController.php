<?php

namespace App\Controllers;

use App\Lib\Database;
use App\Lib\Twig;
use App\Lib\Hydrator;
use App\Entity\Category;
use App\Repository\CategoryRepository;

class CategoryController
{
    public $twig;

    public function __construct()
    {
        $this->twig = new Twig();
    }

    public function displayAdminCategories()
    {
        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();

        echo $this->twig->getTwig()->render('backend/categories.twig', [
            'categories' => $categories
        ]);

    }

}