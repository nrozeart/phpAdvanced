<?php

use Geekbrains\PhpAdvanced\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use Geekbrains\PhpAdvanced\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\PhpAdvanced\Http\Container\DIContainer;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';
// Создаём объект контейнера ..
$container = new DIContainer();

// .. и настраиваем его:
// Добавляем логгер в контейнер
$container->bind(
    // С контрактом логгера из PSR-3 ..
LoggerInterface::class,
    // .. ассоциируем объект логгера из библиотеки monolog
    (new Logger('blog'))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.log'
        ))
        // Добавили новый обработчик:
        ->pushHandler(new StreamHandler(
        // записывать в файл "blog.error.log"
            __DIR__ . '/logs/blog.error.log',
        // события с уровнем ERROR и выше,
            level: Logger::ERROR,
        // при этом событие не должно "всплывать"
            bubble: false,
        ))
        // Добавили ещё один обработчик;
        // он будет вызываться первым …
        ->pushHandler(
        // .. и вести запись в поток php://stdout,
        // то есть в консоль
        new StreamHandler("php://stdout")
        )
);

// 1. подключение к БД
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

$container->bind(
    LikesRepositoryInterface::class,
    SqliteLikesRepository::class
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