<?php
namespace Geekbrains\PhpAdvanced\Blog\Commands;

use Geekbrains\PhpAdvanced\Blog\Exceptions\ArgumentsException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\CommandException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\User;
use Geekbrains\PhpAdvanced\Person\Name;
use Psr\Log\LoggerInterface;

//php cli.php username=ivan first_name=Ivan last_name=Nikitin password=123

final class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        // Добавили зависимость от логгера
        private LoggerInterface $logger,
    ) {
}
// Вместо массива принимаем объект типа Arguments

    /**
     * @throws ArgumentsException
     * @throws InvalidArgumentException
     * @throws CommandException
     */
    public function handle(Arguments $arguments): void
    {
        // Логируем информацию о том, что команда запущена
        // Уровень логирования – INFO
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            // Логируем сообщение с уровнем WARNING
            $this->logger->warning("User already exists: $username");
            // Бросаем исключение, если пользователь уже существует
            throw new CommandException("User already exists: $username");
//            // Вместо выбрасывания исключения просто выходим из функции
//            return;
        }

        // Создаём объект пользователя
// Функция createFrom сама создаст UUID
// и захеширует пароль
        $user = User::createFrom(
            $username,
            $arguments->get('password'),
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name')
            )
        );
        $this->usersRepository->save($user);
        // Получаем UUID созданного пользователя
        $this->logger->info('User created: ' . $user->uuid());

    }
    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}