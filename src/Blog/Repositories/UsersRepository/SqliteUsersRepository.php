<?php

namespace Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository;
use Geekbrains\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Geekbrains\PhpAdvanced\Blog\User;
use Geekbrains\PhpAdvanced\Blog\UUID;
use Geekbrains\PhpAdvanced\Person\Name;
use \PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    private PDO $connection;
    private LoggerInterface $logger;

    public function __construct(PDO $connection, LoggerInterface $logger) {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function save(User $user): void
    {
        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'INSERT INTO users (
                   uuid,
                   first_name, 
                   last_name, 
                   username,
                   password) 
            VALUES (
                    :uuid, 
                    :first_name, 
                    :last_name, 
                    :username,
                    :password
                    )
                    ON CONFLICT(uuid) DO UPDATE SET
                    first_name=:first_name,
                    last_name=:last_name
                    '
        );
        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$user->uuid(),
            ':first_name' => $user->name()->first(),
            ':last_name' => $user->name()->last(),
            ':username' => $user->username(),
            // Значения для поля password
            ':password' => $user->hashedPassword(),
        ]);
        // Логируем UUID нового пользователя
        $this->logger->info("User created: {$user->uuid()}");
    }

    // Также добавим метод для получения
// пользователя по его UUID
    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getUser($statement, $uuid);
    }

    // Добавили метод получения пользователя по username

    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );
        $statement->execute([
            ':username' => $username,
        ]);
        return $this->getUser($statement, $username);
    }

    // Вынесли общую логику в отдельный приватный метод

    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    private function getUser(PDOStatement $statement, string $username): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (false === $result) {
            throw new UserNotFoundException(
                "Cannot find user: $username"
            );
        }
// Создаём объект пользователя с полем username
        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username'],
            (string)$result['password']
        );
    }
}