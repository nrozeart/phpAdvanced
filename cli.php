<?php

use Geekbrains\PhpAdvanced\Blog\Post;
use Geekbrains\PhpAdvanced\Blog\User;
use Geekbrains\PhpAdvanced\Person\{Person};
use Geekbrains\PhpAdvanced\Person\Name;

//сокр. для use src\Blog\User as User;
// сокр. для use Person\Name as Name; и use Person\Person as Person;

include __DIR__ . "/vendor/autoload.php";

//spl_autoload_register('load');
function load ($classname) {
    $file = $classname . ".php";
    $file = str_replace('\\', '/', $file);
//    $file = str_replace('Geekbrains', 'src', $file);

    if(file_exists($file)) {
        include $file;
    }
}

$name = new Name('Peter', 'Sidorov');
$user = new User(1, $name, "Admin");
echo $user;

$person = new Person($name, new DateTimeImmutable());

$post = new Post(
    1,
    $person,
    'Мой новый пост');
echo $post;