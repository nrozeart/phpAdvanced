<?php

use Geekbrains\PhpAdvanced\Blog\Repositories\Post;
use Geekbrains\PhpAdvanced\Blog\Repositories\Comment;
use Geekbrains\PhpAdvanced\Blog\Repositories\User;
use Geekbrains\PhpAdvanced\Person\Name;

//сокр. для use src\Blog\User as User;
// сокр. для use Person\Name as Name; и use Person\Person as Person;

//spl_autoload_register('load') автозагрузчик вручную;
function load ($classname) {
    $file = $classname . ".php";
    $file = str_replace('\\', '/', $file);
//    $file = str_replace('Geekbrains', 'src', $file);

    if(file_exists($file)) {
        include $file;
    }
}

include __DIR__ . "/vendor/autoload.php";

$faker = Faker\Factory::create('ru_RU');

$name = new Name(
    $faker->firstName(),
    $faker->lastName()
);
$user = new User(
    $faker->randomDigitNotNull(),
    $name,
    $faker->word(1));

$route = $argv[1] ?? null;

switch($argv[1]) {
    case "user":
        echo $user;
        break;
    case "post":
        $post = new Post(
            $faker->randomDigitNotNull(),
            $user,
            $faker->realText(rand(10,15)),
            $faker->realText(rand(50,100))
        );
        echo $post;
        break;
    case "comment":
        $post = new Post(
            $faker->randomDigitNotNull(),
            $user,
            $faker->realText(rand(10,15)),
            $faker->realText(rand(50,100))
        );
        $comment = new Comment(
            $faker->randomDigitNotNull(),
            $user,
            $post,
            $faker->realText(rand(50,100))
        );
        echo $comment;
        break;
    default:
        echo 'Error! Try user, post or comment as arguments for success';
}


