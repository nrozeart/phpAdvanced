<?php

namespace Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository;

use Geekbrains\PhpAdvanced\Blog\User;
use Geekbrains\PhpAdvanced\Blog\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    // Добавили метод
    public function getByUsername(string $username): User;
}