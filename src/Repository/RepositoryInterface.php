<?php

declare(strict_types=1);

namespace App\Repository;

interface RepositoryInterface
{
    public function getAll(): array|object;

    public function getBy(string $value): ?object;

    public function getById(int $id): ?object;

    public function create(object $entity): bool;

    public function update(object $entity): bool;

    public function delete(object $entity): bool;

    public function count(): int;
}
