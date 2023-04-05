<?php

namespace Geekbrains\PhpAdvanced\Blog\Commands\FakeData;

use Geekbrains\PhpAdvanced\Blog\Comment;
use Geekbrains\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\PhpAdvanced\Blog\Post;
use Geekbrains\PhpAdvanced\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\User;
use Geekbrains\PhpAdvanced\Blog\UUID;
use Geekbrains\PhpAdvanced\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
// Внедряем генератор тестовых данных и
// репозитории пользователей и статей
    public function __construct(
        private \Faker\Generator $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
//            ->addOption(
//        // Имя опции
//            'users-number',
//            // Сокращённое имя
//            'un',
//            // Опция имеет значения
//            InputOption::VALUE_REQUIRED,
//            // Описание
//            'Numbers of users are created',
//        )
//            ->addOption(
//        // Имя опции
//            'posts-number',
//            // Сокращённое имя
//            'pn',
//            // Опция имеет значения
//            InputOption::VALUE_REQUIRED,
//            // Описание
//            'Numbers of posts are created',
//        )
;
    }


    /**
     * @throws InvalidArgumentException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        // Получаем значения опций
//        $usersNumber = $input->getOption('users-number');
//        $postsNumber = $input->getOption('posts-number');

// Создаём users-number пользователей
        $users = [];
        for ($i = 0; $i < 5; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->username());
        }
// От имени каждого пользователя
// создаём по posts-number статей
        $posts = [];
        foreach ($users as $user) {
            for ($i = 0; $i < 5; $i++) {
                $post = $this->createFakePost($user);
                $posts[] = $post;
                $output->writeln('Post created: ' . $post->title());
}
        }
// К каждому посту создаем
//  по 2 комментария
        foreach ($posts as $post) {
            for ($i = 0; $i < 2; $i++) {
                $comment = $this->createFakeComment($post, $user);
                $output->writeln('Comment created: ' . $comment->getText());
            }
        }
        return Command::SUCCESS;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function createFakeUser(): User
    {
        $user = User::createFrom(
// Генерируем имя пользователя
            $this->faker->userName,
// Генерируем пароль
            $this->faker->password,
            new Name(
// Генерируем имя
                $this->faker->firstName,
// Генерируем фамилию
                $this->faker->lastName
            )
        );
// Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);
        return $user;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
// Генерируем предложение не длиннее шести слов
            $this->faker->sentence(6, true),
// Генерируем текст
            $this->faker->realText
        );
// Сохраняем статью в репозиторий
        $this->postsRepository->save($post);
        return $post;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function createFakeComment(Post $post, User $user): Post
    {
        $comment = new Comment(
            UUID::random(),
            $post,
            $user,
// Генерируем текст
            $this->faker->realText
        );
// Сохраняем комментарий в репозиторий
        $this->commentsRepository->save($comment);
        return $post;
    }
}