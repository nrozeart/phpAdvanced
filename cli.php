<?php

use Geekbrains\PhpAdvanced\Blog\Post;
use Geekbrains\PhpAdvanced\Blog\Repositories\Comments;
use Geekbrains\PhpAdvanced\Blog\User;
use Geekbrains\PhpAdvanced\Person\Person;
use Geekbrains\PhpAdvanced\Person\Name;
use Geekbrains\PhpAdvanced\Blog\Repositories\InMemoryUsersRepository;
use Geekbrains\PhpAdvanced\Blog\Exceptions\UserNotFoundException;

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

$name = new Name($faker->firstName(), $faker->lastName());
$user = new User(1, $name, "Admin");
$person = new Person($name, new DateTimeImmutable());
$comment = new Comments(
    1,
    $person,
    1,
    $faker->realText(rand(10,15))
);

$post = new Post(
    1,
    $person,
    $faker->realText(rand(10,15)),
    $faker->realText(rand(100,150))
);

$userRepository = new InMemoryUsersRepository();
$userRepository->save($user);


try {
    echo $userRepository->get(1);
//    echo $userRepository->get(2);
//    echo $userRepository->get(3);
} catch (UserNotFoundException $e) {
    echo $e->getMessage();
} catch (Exception $e) {
    echo "Ой, что-то не так!\n";
    echo $e->getMessage();
}

echo $post;
echo $comment;