<?php

namespace Geekbrains\PhpAdvanced\Http\Auth;

use Geekbrains\PhpAdvanced\Blog\Exceptions\AuthException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\HttpException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\PhpAdvanced\Http\Request;
use Geekbrains\PhpAdvanced\Blog\User;

class PasswordAuthentication implements PasswordAuthenticationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }
    public function user(Request $request): User
    {
    // 1. Идентифицируем пользователя
    try {
    $username = $request->jsonBodyField('username');
    } catch (HttpException $e) {
        throw new AuthException($e->getMessage());
    }
    try {
        $user = $this->usersRepository->getByUsername($username);
    } catch (UserNotFoundException $e) {
        throw new AuthException($e->getMessage());
    }
    // 2. Аутентифицируем пользователя
    // Проверяем, что предъявленный пароль
    // соответствует сохранённому в БД
        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
// Проверяем пароль методом пользователя
        if (!$user->checkPassword($password)) {
            throw new AuthException('Wrong password');
        }
return $user;

    }
}