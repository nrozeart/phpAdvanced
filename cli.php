<?php

use Geekbrains\PhpAdvanced\Blog\Commands\Arguments;
use Geekbrains\PhpAdvanced\Blog\Commands\CreateUserCommand;
use Geekbrains\PhpAdvanced\Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;


// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';
// При помощи контейнера создаём команду
$command = $container->get(CreateUserCommand::class);

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (Exception $e) {
    // Логируем информацию об исключении.
// Объект исключения передаётся логгеру
// с ключом "exception".
// Уровень логирования – ERROR
    $logger->error($e->getMessage(), ['exception' => $e]);
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