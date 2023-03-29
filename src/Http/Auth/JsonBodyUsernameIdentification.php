<?php

namespace Geekbrains\PhpAdvanced\Http\Auth;

use Geekbrains\PhpAdvanced\Blog\Exceptions\AuthException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\HttpException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\User;
use Geekbrains\PhpAdvanced\Http\Request;
use JsonException;

class JsonBodyUsernameIdentification implements IdentificationInterface
{
    public function __construct(
    private UsersRepositoryInterface $usersRepository
) {
}

    /**
     * @throws AuthException
     * @throws JsonException
     */
    public function user(Request $request): User
{
    try {
// Получаем имя пользователя из JSON-тела запроса;
// ожидаем, что имя пользователя находится в поле username
        $username = $request->jsonBodyField('username');
    } catch (HttpException $e) {
// Если невозможно получить имя пользователя из запроса -
// бросаем исключение
        throw new AuthException($e->getMessage());
    }
    try {
// Ищем пользователя в репозитории и возвращаем его
        return $this->usersRepository->getByUsername($username);
    } catch (UserNotFoundException $e) {
// Если пользователь не найден -
// бросаем исключение
        throw new AuthException($e->getMessage());
    }
}
}