<?php

use Geekbrains\PhpAdvanced\Blog\Post;
use Geekbrains\PhpAdvanced\Blog\User;
use Geekbrains\PhpAdvanced\Person\Person;
use Geekbrains\PhpAdvanced\Person\Name;
use Geekbrains\PhpAdvanced\Blog\Repositories\InMemoryUsersRepository;
use Geekbrains\PhpAdvanced\Blog\Exceptions\UserNotFoundException;



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

$faker = Faker\Factory::create('ru_RU');
echo $faker->name() . PHP_EOL;
echo $faker->realText(rand(100,150)) . PHP_EOL;


$name = new Name('Peter', 'Sidorov');
$user = new User(1, $name, "Admin");
echo $user;

$person = new Person($name, new DateTimeImmutable());

$post = new Post(
    1,
    $person,
    'Мой новый пост');
echo $post;



$name2 = new Name('Ivan', 'Petrov');
$user2 = new User(2, $name2, "Vanya");
$userRepository = new InMemoryUsersRepository();

$userRepository->save($user);
$userRepository->save($user2);


try {
    echo $userRepository->get(1);
    echo $userRepository->get(2);
    echo $userRepository->get(3);
} catch (UserNotFoundException $e) {
    echo $e->getMessage();
} catch (Exception $e) {
    echo "Ой, что-то не так!\n";
    echo $e->getMessage();
}

