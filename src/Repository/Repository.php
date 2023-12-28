<?php

namespace App\Repository;

interface Repository
{
    public function getAll(int $limit, int $offset);

    public function getBy(string $value);

    public function getById(int $id);
    
    public function create(object $entity);

    public function update(object $entity);

    public function delete(object $entity);

    public function count();

}
