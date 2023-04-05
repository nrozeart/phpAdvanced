<?php

use Dotenv\Dotenv;
use Faker\Provider\Lorem;
use Faker\Provider\ro_RO\Person;
use Faker\Provider\ru_RU\Internet;
use Faker\Provider\ru_RU\Text;
use Geekbrains\PhpAdvanced\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
use Geekbrains\PhpAdvanced\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Geekbrains\PhpAdvanced\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use Geekbrains\PhpAdvanced\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\PhpAdvanced\Http\Auth\AuthenticationInterface;
use Geekbrains\PhpAdvanced\Http\Auth\BearerTokenAuthentication;
use Geekbrains\PhpAdvanced\Http\Auth\IdentificationInterface;
use Geekbrains\PhpAdvanced\Http\Auth\JsonBodyUsernameIdentification;
use Geekbrains\PhpAdvanced\Http\Auth\PasswordAuthentication;
use Geekbrains\PhpAdvanced\Http\Auth\PasswordAuthenticationInterface;
use Geekbrains\PhpAdvanced\Http\Auth\TokenAuthenticationInterface;
use Geekbrains\PhpAdvanced\Http\Container\DIContainer;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';

// Загружаем переменные окружения из файла .env
Dotenv::createImmutable(__DIR__)->safeLoad();

// Создаём объект контейнера ..
$container = new DIContainer();

// .. и настраиваем его:
// 1. подключение к БД


$container->bind(
    PDO::class,
// Берём путь до файла базы данных SQLite
// из переменной окружения SQLITE_DB_PATH
    new PDO('sqlite:' . __DIR__ . '/' . $_ENV['SQLITE_DB_PATH']));

// Выносим объект логгера в переменную
$logger = (new Logger('blog'));

// Включаем логирование в файлы,
// если переменная окружения LOG_TO_FILES
// содержит значение 'yes'
if ('yes' === $_ENV['LOG_TO_FILES']) {
    $logger
->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.log'
    ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}
// Включаем логирование в консоль,
// если переменная окружения LOG_TO_CONSOLE
// содержит значение 'yes'
if ('yes' === $_ENV['LOG_TO_CONSOLE']) {
    $logger
        ->pushHandler(
            new StreamHandler("php://stdout")
        );
}

$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);
$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);

$container->bind(
    AuthenticationInterface::class,
PasswordAuthentication::class
);

$container->bind(
    IdentificationInterface::class,
    JsonBodyUsernameIdentification::class
);

$container->bind(
    LoggerInterface::class,
    $logger
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
// 3. репозиторий пользователей
$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);


// Создаём объект генератора тестовых данных
$faker = new \Faker\Generator();
// Инициализируем необходимые нам виды данных
$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));
// Добавляем генератор тестовых данных
// в контейнер внедрения зависимостей
$container->bind(
    \Faker\Generator::class,
    $faker
);



// Возвращаем объект контейнера
return $container;