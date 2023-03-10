<?php

use Geekbrains\PhpAdvanced\Blog\Repositories\User;
use Geekbrains\PhpAdvanced\Person\Name;

use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\SqliteUsersRepository;

include __DIR__ . "/vendor/autoload.php";

//Создаём объект подключения к SQLite
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

//Создаём объект репозитория
$usersRepository = new SqliteUsersRepository($connection);
//Добавляем в репозиторий несколько пользователей
$usersRepository->save(new User(1, new Name('Ivan', 'Nikitin'), "admin"));
$usersRepository->save(new User(2, new Name('Anna', 'Petrova'), "user"));