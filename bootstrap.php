<?php

use Geekbrains\PhpAdvanced\Blog\Container\DIContainer;
use Geekbrains\PhpAdvanced\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';
// Создаём объект контейнера ..
$container = new DIContainer();
// .. и настраиваем его:
// 1. подключение к БД
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);
// 2. репозиторий статей
$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);
// 3. репозиторий пользователей
$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);
// Возвращаем объект контейнера
return $container;