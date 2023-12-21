<?php

namespace App\Controllers;

use App\Lib\Database;
use App\Lib\Twig;
use App\Lib\Hydrator;
use App\Entity\Tag;
use App\Repository\TagRepository;

class TagController
{
    public $twig;

    public function __construct()
    {
        $this->twig = new Twig();
    }

    public function displayAdminTags()
    {
        $tagRepository = new TagRepository;
        $tags = $tagRepository->getAll();

        echo $this->twig->getTwig()->render('backend/tags.twig', [
            'tags' => $tags
        ]);
    }

    public function createForm()
    {
        echo $this->twig->getTwig()->render('backend/forms/addTag.twig');
    }

    public function create(array $params)
    {
        if (!isset($params['post']['name'], $params['post']['description'], $params['post']['slug'])) {
            throw new \Exception('Les données du formulaire sont invalides.');
        }

        $tag = new Tag();
        $tag = Hydrator::hydrate($params['post'], $tag);

        $tagRepository = new TagRepository();
        $success = $tagRepository->create($tag);

        if (!$success) {
            throw new \Exception('Impossible d\'ajouter le tag!');
        } else {
            header('Location: /admin/tags');
        }
    }

    public function updateForm(array $id)
    {
        $tagId = (int)$id['id'];

        $tagRepository = new TagRepository();
        $existingTag = $tagRepository->getById($tagId);

        if (!$existingTag) {
            header( $_SERVER["SERVER_PROTOCOL"] . '404 Not Found');
            echo 'Le tag n\'existe pas 404 not found baby';
            die();
        }

        echo $this->twig->getTwig()->render('backend/forms/editTag.twig', [
            'tag' => $existingTag
        ]);
    }


    public function update(array $id)
    {
        $tagId = (int)$id['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $_POST;
            if (!isset($postData['name'], $postData['description'], $postData['slug'])) {
                throw new \Exception('Les données du formulaire sont invalides.');
            }

            $tagRepository = new TagRepository();
            $tag = $tagRepository->getById($tagId);
    
            if ($tag) {
                $tag = Hydrator::hydrate($postData, $tag);
                $success = $tagRepository->update($tag);
    
                if (!$success) {
                    throw new \Exception('Impossible de mettre à jour le tag!');
                } else {
                    header('Location: /admin/tags');
                }
            } else {
                throw new \Exception('Tag non trouvé.');
            }
        }
        
    }

    public function delete(array $id)
    {
        $id = (int)$id['id']; 

        $tagRepository = new TagRepository();

        $tag = $tagRepository->getById($id);

        if($tag->getId() === $id) {
            $success = $tagRepository->delete($tag);
        } else {
            return false;
        }

        if (!$success) {
            throw new \Exception('Impossible de supprimer le tag!');
        } else {
            header('Location: /admin/tags');
        }
    }




}