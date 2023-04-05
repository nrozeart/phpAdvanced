<?php


use Geekbrains\PhpAdvanced\Blog\Commands\Users\CreateUser;
use Geekbrains\PhpAdvanced\Blog\Commands\Users\UpdateUser;
use Geekbrains\PhpAdvanced\Http\Actions\Posts\DeletePost;
use Symfony\Component\Console\Application;


// Подключаем файл bootstrap.php
$container = require __DIR__ . '/bootstrap.php';
// Создаём объект приложения
$application = new Application();
// Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
    // Добавили команду удаления статей
    DeletePost::class,
    // Добавили команду обновления пользователя
    UpdateUser::class,
];
foreach ($commandsClasses as $commandClass) {
// Посредством контейнера
// создаём объект команды
    $command = $container->get($commandClass);
// Добавляем команду к приложению
    $application->add($command);
}
// Запускаем приложение
$application->run();