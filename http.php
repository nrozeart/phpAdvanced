<?php

use Geekbrains\PhpAdvanced\Blog\Exceptions\AppException;
use Geekbrains\PhpAdvanced\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\PhpAdvanced\Http\Actions\Auth\LogIn;
use Geekbrains\PhpAdvanced\Http\Actions\Likes\CreatePostLike;
use Geekbrains\PhpAdvanced\Http\Actions\Posts\CreatePost;
use Geekbrains\PhpAdvanced\Http\Actions\Posts\DeletePost;
use Geekbrains\PhpAdvanced\Http\Actions\User\CreateUser;
use Geekbrains\PhpAdvanced\Http\Actions\User\FindByUsername;
use Geekbrains\PhpAdvanced\Http\ErrorResponse;
use Geekbrains\PhpAdvanced\Http\Request;
use Geekbrains\PhpAdvanced\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';
$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);
try {
    $path = $request->path();
} catch (HttpException $e) {
// Логируем сообщение с уровнем WARNING
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
return;
}
try {
    $method = $request->method();
} catch (HttpException $e) {
// Логируем сообщение с уровнем WARNING
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

// Ассоциируем маршруты с именами классов действий,
// вместо готовых объектов
$routes = [
        'GET' => [
            '/users/show' => FindByUsername::class,
        ],
        'POST' => [
            // Добавили маршрут обмена пароля на токен
            '/login' => LogIn::class,
            '/users/create' => CreateUser::class,
            '/posts/create' => CreatePost::class,
            '/post-likes/create' => CreatePostLike::class,
        ],

        'DELETE' => [
            '/posts' => DeletePost::class,
        ]
];

if (!array_key_exists($method, $routes)
    || !array_key_exists($path, $routes[$method])) {
// Логируем сообщение с уровнем NOTICE
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}
$actionClassName = $routes[$method][$path];
try {
    $action = $container->get($actionClassName);
    $response = $action->handle($request);
} catch (Exception $e) {
// Логируем сообщение с уровнем ERROR
    $logger->error($e->getMessage(), ['exception' => $e]);
// Больше не отправляем пользователю
// конкретное сообщение об ошибке,
// а только логируем его
    (new ErrorResponse($e->getMessage()))->send();
    return;
}
$response->send();