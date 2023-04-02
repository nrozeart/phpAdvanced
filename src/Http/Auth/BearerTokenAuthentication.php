<?php

namespace Geekbrains\PhpAdvanced\Http\Auth;

// Bearer — на предъявителя
use DateTimeImmutable;
use Geekbrains\PhpAdvanced\Blog\Exceptions\AuthException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\AuthTokenNotFoundException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\HttpException;
use Geekbrains\PhpAdvanced\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\PhpAdvanced\Http\Request;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{
    private const HEADER_PREFIX = 'Bearer ';
    public function __construct(
// Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository,
// Репозиторий пользователей
        private UsersRepositoryInterface $usersRepository,
    ) {
    }
    public function user(Request $request): \Geekbrains\PhpAdvanced\Blog\User
    {
// Получаем HTTP-заголовок
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
// Проверяем, что заголовок имеет правильный формат
if (!str_starts_with($header, self::HEADER_PREFIX)) {
    throw new AuthException("Malformed token: [$header]");
}
// Отрезаем префикс Bearer
$token = mb_substr($header, strlen(self::HEADER_PREFIX));
// Ищем токен в репозитории
try {
    $authToken = $this->authTokensRepository->get($token);
} catch (AuthTokenNotFoundException) {
    throw new AuthException("Bad token: [$token]");
}
// Проверяем срок годности токена
if ($authToken->expiresOn() <= new DateTimeImmutable()) {
    throw new AuthException("Token expired: [$token]");
}
// Получаем UUID пользователя из токена
$userUuid = $authToken->userUuid();
// Ищем и возвращаем пользователя
return $this->usersRepository->get($userUuid);
}
}