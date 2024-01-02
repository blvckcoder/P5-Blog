<?php

namespace App\Controllers;

use App\Lib\Twig;
use App\Lib\Auth;
use App\Lib\Hydrator;
use App\Entity\Category;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;

class CategoryController
{
    public $twig;

    public $auth;

    public function __construct()
    {
        $this->twig = new Twig();
        
        $userRepository = new UserRepository();
        $this->auth = new Auth($userRepository);
    }

    public function displayAdminCategories()
    {
        $this->auth->checkAdmin();
        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();

        echo $this->twig->getTwig()->render('backend/categories.twig', [
            'categories' => $categories
        ]);

    }

    public function createForm()
    {
        $this->auth->checkAdmin();
        echo $this->twig->getTwig()->render('backend/forms/addCategory.twig');
    }

    public function create(array $params)
    {
        $this->auth->checkAdmin();
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

    public function updateForm(array $id)
    {
        $this->auth->checkAdmin();
        $categoryId = (int)$id['id'];

        $categoryRepository = new CategoryRepository();
        $existingCategory = $categoryRepository->getById($categoryId);

        if (!$existingCategory) {
            header( $_SERVER["SERVER_PROTOCOL"] . '404 Not Found');
            echo 'La catégorie n\'existe pas 404 not found baby';
            die();
        }

        echo $this->twig->getTwig()->render('backend/forms/editCategory.twig', [
            'category' => $existingCategory
        ]);
    }


    public function update(array $id)
    {
        $this->auth->checkAdmin();
        $categoryId = (int)$id['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $_POST;
            if (!isset($postData['name'], $postData['description'], $postData['slug'])) {
                throw new \Exception('Les données du formulaire sont invalides.');
            }

            $categoryRepository = new CategoryRepository();
            $category = $categoryRepository->getById($categoryId);
    
            if ($category) {
                $category = Hydrator::hydrate($postData, $category);
                $success = $categoryRepository->update($category);
    
                if (!$success) {
                    throw new \Exception('Impossible de mettre à jour la Catégorie!');
                } else {
                    header('Location: /admin/categories');
                }
            } else {
                throw new \Exception('Catégorie non trouvé.');
            }
        }
        
    }


    public function delete(array $id)
    {
        $this->auth->checkAdmin();
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