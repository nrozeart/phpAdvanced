<?php

namespace Geekbrains\PhpAdvanced\Http\Auth;

use Geekbrains\PhpAdvanced\Blog\Exceptions\AuthException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\HttpException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\UUID;
use Geekbrains\PhpAdvanced\Http\Request;
use Geekbrains\PhpAdvanced\Blog\User;
use JsonException;

class JsonBodyUuidIdentification implements IdentificationInterface
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
// Получаем UUID пользователя из JSON-тела запроса;
// ожидаем, что корректный UUID находится в поле user_uuid
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException|InvalidArgumentException $e) {
// Если невозможно получить UUID из запроса -
// бросаем исключение
            throw new AuthException($e->getMessage());
        }
        try {
// Ищем пользователя в репозитории и возвращаем его
            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
// Если пользователь с таким UUID не найден -
// бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }
}