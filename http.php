<?php

use Geekbrains\PhpAdvanced\Blog\Exceptions\AppException;
use Geekbrains\PhpAdvanced\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\PhpAdvanced\Http\Actions\Posts\CreatePost;
use Geekbrains\PhpAdvanced\Http\Actions\User\CreateUser;
use Geekbrains\PhpAdvanced\Http\Actions\User\FindByUsername;
use Geekbrains\PhpAdvanced\Http\ErrorResponse;
use Geekbrains\PhpAdvanced\Http\Request;
use Geekbrains\PhpAdvanced\Http\SuccessfulResponse;

$container = require __DIR__ . '/bootstrap.php';

$request = new Request($_GET, $_SERVER, file_get_contents('php://input'));

try {
// Пытаемся получить путь из запроса
    $path = $request->path();
} catch (HttpException) {
// Отправляем неудачный ответ,
// если по какой-то причине
// не можем получить путь
    (new ErrorResponse)->send();
// Выходим из программы
    return;
}

try {
// Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException) {
// Возвращаем неудачный ответ,
// если по какой-то причине
// не можем получить метод
    (new ErrorResponse)->send();
    return;
}

$routes = [
// Добавили ещё один уровень вложенности
// для отделения маршрутов,
// применяемых к запросам с разными методами
//    'GET' => [
//        '/users/show' => new FindByUsername(
//            new SqliteUsersRepository(
//                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
//        )
//        ),
////        '/posts/show' => new FindByUuid(
////            new SqlitePostsRepository(
////                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
////            )
////        ),
//    ],
//    'POST' => [
//    // Добавили новый маршрут
//        '/posts/create' => new CreatePost(
//            new SqlitePostsRepository(
//                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
//            ),
//            new SqliteUsersRepository(
//                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
//            )
//        ),
//],
//];

        'GET' => [
            '/users/show' => FindByUsername::class,
        ],
        'POST' => [
        '/users/create' => CreateUser::class,
        '/posts/create' => CreatePost::class,
        ],
    ];
//    'DELETE' => [
//    '/posts' => DeletePost::class,
//],


// Если у нас нет маршрутов для метода запроса -
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Route not found'))->send();
    return;
}
// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Route not found: $method $path'))->send();
    return;
}
// Выбираем действие по методу и пути
$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);


try {
    $response = $action->handle($request);

} catch (Exception $e) {
    (new ErrorResponse($e->getMessage()))->send();
}
$response->send();