<?php

namespace Geekbrains\PhpAdvanced\Blog\Repositories\PostsRepository;

use Geekbrains\PhpAdvanced\Blog\Post;
use Geekbrains\PhpAdvanced\Blog\UUID;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;
    public function get(UUID $uuid): Post;
// Добавили метод для удаления статьи
    public function delete(UUID $uuid): void;
}