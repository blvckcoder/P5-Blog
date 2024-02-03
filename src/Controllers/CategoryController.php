<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Lib\Hydrator;
use App\Lib\Pagination;
use App\Entity\Category;
use App\Lib\HTTPResponse;
use App\Repository\CategoryRepository;

class CategoryController extends DefaultController
{
    public function displayAdminCategories(): void
    {
        $this->auth->checkAdmin();

        $itemsPerPage = 9;
        $currentPage = intval($_GET['page'] ?? 1);

        $categoryRepository = new CategoryRepository();
        $totalItems = $categoryRepository->count();

        $pagination = new Pagination($totalItems, $itemsPerPage, $currentPage);

        $categories = $categoryRepository->getPaginated($itemsPerPage, $pagination->getOffset());

        $paginationHtml = $pagination->renderHtml();

        $flashMessage = $this->getFlash();

        echo $this->twig->getTwig()->render('backend/categories.twig', [
            'categories' => $categories,
            'pagination' => $paginationHtml,
            'flashMessage' => $flashMessage
        ]);
    }

    public function createForm(): void
    {
        $this->auth->checkAdmin();
        echo $this->twig->getTwig()->render('backend/forms/addCategory.twig');
    }

    public function create(array $params): void
    {
        $this->auth->checkAdmin();
        $postData = $_POST;

        if (!isset($postData['name'], $postData['description'], $postData['slug']) || empty($postData['name']) || empty($postData['description']) || empty($postData['slug'])) {
            //donnees vide
            $this->addFlash('error', 'Les données ne sont pas bonnes !');
            HTTPResponse::redirect('/admin/categories');
        }

        $category = new Category();
        $category = Hydrator::hydrate($postData, $category);

        $categoryRepository = new CategoryRepository();
        $success = $categoryRepository->create($category);

        if (!$success) {
            $this->addFlash('error', 'Impossible d\'ajouter la catégorie !');
            HTTPResponse::redirect('/admin/categories');
        } else {
            $this->addFlash('success', 'Catégorie ajoutée avec succès.');
            HTTPResponse::redirect('/admin/categories');
        }
    }

    public function updateForm(array $id): void
    {
        $this->auth->checkAdmin();
        $categoryId = (int)$id['id'];

        $categoryRepository = new CategoryRepository();
        $existingCategory = $categoryRepository->getById($categoryId);

        if (!$existingCategory) {
            header($_SERVER["SERVER_PROTOCOL"] . '404 Not Found');
            echo 'La catégorie n\'existe pas 404 not found baby';
        }

        echo $this->twig->getTwig()->render('backend/forms/editCategory.twig', [
            'category' => $existingCategory
        ]);
    }


    public function update(array $id): void
    {
        $this->auth->checkAdmin();
        $categoryId = (int)$id['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $_POST;
            if (!isset($postData['name'], $postData['description'], $postData['slug'])) {
                //donnees vide
                $this->addFlash('error', 'Les données du formulaire modification catégorie sont invalides.');
            }

            $categoryRepository = new CategoryRepository();
            $category = $categoryRepository->getById($categoryId);

            if ($category) {
                $category = Hydrator::hydrate($postData, $category);
                $success = $categoryRepository->update($category);

                if (!$success) {
                    $this->addFlash('error', 'Impossible de mettre à jour la Catégorie !');
                    HTTPResponse::redirect('/admin/categories');
                } else {
                    $this->addFlash('success', 'La catégorie a bien été modifiée !');
                    HTTPResponse::redirect('/admin/categories');
                }
            } else {
                $this->addFlash('error', 'Catégorie non trouvée !');
                HTTPResponse::redirect('/admin/categories');
            }
        }
    }


    public function delete(array $id): void
    {
        $this->auth->checkAdmin();
        $id = (int)$id['id'];

        $categoryRepository = new CategoryRepository();

        $category = $categoryRepository->getById($id);

        if ($category !== null && $category->getId() === $id) {
            $success = $categoryRepository->delete($category);
            $this->addFlash('success', 'La catégorie a bien été supprimée !');

            if (!$success) {
                $this->addFlash('error', 'Impossible de supprimer la catégorie !');
            }
            
            HTTPResponse::redirect('/admin/categories');
        } else {
            $this->addFlash('error', 'Categorie non trouvée ou ID incorrect.');
            HTTPResponse::redirect('/admin/categories');
        }
    }
}
