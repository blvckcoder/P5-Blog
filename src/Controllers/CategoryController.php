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

    public function createForm()
    {
        echo $this->twig->getTwig()->render('backend/forms/addCategory.twig');
    }

    public function create(array $params)
    {
        if (!isset($params['post']['name'], $params['post']['description'], $params['post']['slug'])) {
            throw new \Exception('Les données du formulaire sont invalides.');
        }

        $category = new Category();
        $category = Hydrator::hydrate($params['post'], $category);

        $categoryRepository = new CategoryRepository();
        $success = $categoryRepository->create($category);

        if (!$success) {
            throw new \Exception('Impossible d\'ajouter la catégorie !');
        } else {
            header('Location: /admin/categories');
        }
    }

    public function delete(array $id)
    {
        $id = (int)$id['id']; 

        $categoryRepository = new CategoryRepository();

        $category = $categoryRepository->getById($id);

        if($category->getId() === $id) {
            $success = $categoryRepository->delete($category);
        } else {
            return false;
        }

        if (!$success) {
            throw new \Exception('Impossible de supprimer la categorie!');
        } else {
            header('Location: /admin/categories');
        }
    }

}