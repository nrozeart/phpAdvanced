<?php
namespace Geekbrains\PhpAdvanced\Blog;

use Geekbrains\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\PhpAdvanced\Person\Name;

class User
{
    /**
     * @param UUID $uuid
     * @param Name $name
     * @param string $username
     * @param string $hashedPassword
     */
         //конструктор
    public function __construct(
        private UUID $uuid,
        private Name $name,
        private string $username,
        private string $hashedPassword
    )
    {

    }

    //геттеры и сеттеры
    // Переименовали функцию
    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }

// Функция для создания нового пользователя

    /**
     * @throws InvalidArgumentException
     */
    public static function createFrom(
        string $username,
        string $password,
        Name $name
    ): self {
// Генерируем UUID
        $uuid = UUID::random();
        return new self(
            $uuid,
            $name,
            $username,
// Передаём сгенерированный UUID
// в функцию хеширования пароля
            self::hash($password, $uuid)
        );
    }
    private static function hash(string $password, UUID $uuid): string
    {
    // Используем UUID в качестве соли
            return hash('sha256', $uuid . $password);
        }
        public function checkPassword(string $password): bool
    {
    // Передаём UUID пользователя
    // в функцию хеширования пароля
    return $this->hashedPassword=== self::hash($password, $this->uuid);
    }

    public function uuid(): UUID
    {
        return $this->uuid;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function setName(Name $name): void
    {
        $this->name = $name;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function setLogin(string $username): void
    {
        $this->username = $username;
    }


    public function __toString(): string
    {
        return "Юзер $this->uuid с именем $this->name и логином $this->username";
    }

}