<?php

namespace Geekbrains\PhpAdvanced\Http\Actions\Posts;

use Geekbrains\PhpAdvanced\Blog\Exceptions\AuthException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\PostNotFoundException;
use Geekbrains\PhpAdvanced\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\UUID;
use Geekbrains\PhpAdvanced\Http\Actions\ActionInterface;
use Geekbrains\PhpAdvanced\Http\Auth\TokenAuthenticationInterface;
use Geekbrains\PhpAdvanced\Http\Request;
use Geekbrains\PhpAdvanced\Http\Response;
use Geekbrains\PhpAdvanced\Http\ErrorResponse;
use Geekbrains\PhpAdvanced\Http\SuccessfulResponse;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DeletePost extends Command
{
    public function __construct(
    // Внедряем репозиторий статей
    private PostsRepositoryInterface $postsRepository,
    ) {
    parent::__construct();
    }
    // Конфигурируем команду
    protected function configure(): void
    {
        $this
            ->setName('posts:delete')
            ->setDescription('Deletes a post')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a post to delete')
            // Добавили опцию
            ->addOption(
                // Имя опции
                'check-existence',
                // Сокращённое имя
                'c',
                // Опция не имеет значения
                InputOption::VALUE_NONE,
                // Описание
                'Check if post actually exists',
            );
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
            $question = new ConfirmationQuestion(
        // Вопрос для подтверждения
                'Delete post [Y/n]? ',
        // По умолчанию не удалять
                false
            );
        // Ожидаем подтверждения
            if (!$this->getHelper('question')
                ->ask($input, $output, $question)
            ) {
        // Выходим, если удаление не подтверждено
                return Command::SUCCESS;
            }
        // Получаем UUID статьи
            $uuid = new UUID($input->getArgument('uuid'));
        // Если опция проверки существования статьи установлена
        if ($input->getOption('check-existence')) {
            try {
            // Пытаемся получить статью
                $this->postsRepository->get($uuid);
            } catch (PostNotFoundException $e) {
            // Выходим, если статья не найдена
                $output->writeln($e->getMessage());
                return Command::FAILURE;
            }
        }

        // Удаляем статью из репозитория
            $this->postsRepository->delete($uuid);
            $output->writeln("Post $uuid deleted");
            return Command::SUCCESS;
        }
}



//class DeletePost implements ActionInterface
//{
//    public function __construct(
//        private PostsRepositoryInterface $postsRepository,
//        // Аутентификация по токену
//        private TokenAuthenticationInterface $authentication,
//    )
//    {
//    }
//
//
//    public function handle(Request $request): Response
//    {
//        try {
//            $this->authentication->user($request);
//        } catch (AuthException $exception) {
//            return new ErrorResponse($exception->getMessage());
//        }
//
//        try {
//            $postUuid = $request->query('uuid');
//            $this->postsRepository->get(new UUID($postUuid));
//
//        } catch (PostNotFoundException $error) {
//            return new ErrorResponse($error->getMessage());
//        }
//
//        $this->postsRepository->delete(new UUID($postUuid));
//
//        return new SuccessfulResponse([
//            'uuid' => $postUuid,
//        ]);
//    }
//}