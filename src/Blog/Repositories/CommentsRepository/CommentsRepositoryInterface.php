<?php

namespace Geekbrains\PhpAdvanced\Blog\Repositories\CommentsRepository;

use Geekbrains\PhpAdvanced\Blog\Comment;
use Geekbrains\PhpAdvanced\Blog\UUID;

interface CommentsRepositoryInterface
{
    public function save(Comment $user): void;
    public function get(UUID $uuid): Comment;
}