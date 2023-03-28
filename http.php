<?php

use Geekbrains\PhpAdvanced\Blog\Exceptions\AppException;
use Geekbrains\PhpAdvanced\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\PhpAdvanced\Http\Actions\Likes\CreatePostLike;
use Geekbrains\PhpAdvanced\Http\Actions\Posts\CreatePost;
use Geekbrains\PhpAdvanced\Http\Actions\Posts\DeletePost;
use Geekbrains\PhpAdvanced\Http\Actions\User\CreateUser;
use Geekbrains\PhpAdvanced\Http\Actions\User\FindByUsername;
use Geekbrains\PhpAdvanced\Http\ErrorResponse;
use Geekbrains\PhpAdvanced\Http\Request;
use Geekbrains\PhpAdvanced\Http\SuccessfulResponse;

// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';
$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}
try {
    $method = $request->method();
} catch (HttpException) {
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
            '/users/create' => CreateUser::class,
            '/posts/create' => CreatePost::class,
            '/post-likes/create' => CreatePostLike::class,
        ],

        'DELETE' => [
            '/posts' => DeletePost::class,
        ]
];

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}
// Получаем имя класса действия для маршрута
$actionClassName = $routes[$method][$path];
// С помощью контейнера
// создаём объект нужного действия
$action = $container->get($actionClassName);
try {
    $response = $action->handle($request);
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}
$response->send();