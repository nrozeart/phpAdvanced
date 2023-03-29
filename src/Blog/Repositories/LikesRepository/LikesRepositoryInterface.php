<?php

namespace Geekbrains\PhpAdvanced\Blog\Repositories\LikesRepository;

use Geekbrains\PhpAdvanced\Blog\Like;
use Geekbrains\PhpAdvanced\Blog\UUID;

interface LikesRepositoryInterface
{
    public function save(Like $like) : void;
    public function getByPostUuid(UUID $uuid) : array;
}