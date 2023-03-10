<?php

use Geekbrains\PhpAdvanced\Blog\Commands\Arguments;
use Geekbrains\PhpAdvanced\Blog\Commands\CreateUserCommand;
use Geekbrains\PhpAdvanced\Blog\Exceptions\AppException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\CommandException;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\PhpAdvanced\Blog\User;
use Geekbrains\PhpAdvanced\Blog\UUID;
use Geekbrains\PhpAdvanced\Person\Name;

    require_once __DIR__ . '/vendor/autoload.php';
    // Создаём объект SQLite-репозитория
$usersRepository = new SqliteUsersRepository(
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);
$command = new CreateUserCommand($usersRepository);
try {
// "Заворачиваем" $argv в объект типа Arguments
    $command->handle(Arguments::fromArgv($argv));
}
// Так как мы добавили исключение ArgumentsException
// имеет смысл обрабатывать все исключения приложения,
// а не только исключение CommandException
catch (AppException $e) {
    echo "{$e->getMessage()}\n";
}











//include __DIR__ . "/vendor/autoload.php";
//
////Создаём объект подключения к SQLite
//$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');
//
////Создаём объект репозитория
//$usersRepository = new SqliteUsersRepository($connection);
////Добавляем в репозиторий несколько пользователей
////$usersRepository->save(new User(UUID::random(), new Name('Ivan', 'Nikitin'), "admin"));
////$usersRepository->save(new User(UUID::random(), new Name('Anna', 'Petrova'), "user"));
//
//try {
//    //$usersRepository->save(new User(UUID::random(), new Name('Ivan', 'Nikitin'), "admin"));
//    echo $usersRepository->getByUsername("admin");
//} catch (Exception $e) {
//    echo $e->getMessage();
//}