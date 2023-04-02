<?php

namespace Geekbrains\PhpAdvanced\Http\Actions\Auth;

use DateTimeImmutable;
use Geekbrains\PhpAdvanced\Blog\AuthToken;
use Geekbrains\PhpAdvanced\Blog\Exceptions\AuthException;
use Geekbrains\PhpAdvanced\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Geekbrains\PhpAdvanced\Http\Actions\ActionInterface;
use Geekbrains\PhpAdvanced\Http\Auth\PasswordAuthenticationInterface;
use Geekbrains\PhpAdvanced\Http\Request;
use Geekbrains\PhpAdvanced\Http\ErrorResponse;
use Geekbrains\PhpAdvanced\Http\SuccessfulResponse;

class LogIn implements ActionInterface
{
    public function __construct(
// Авторизация по паролю
        private PasswordAuthenticationInterface $passwordAuthentication,
// Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {
    }
    public function handle(Request $request): SuccessfulResponse
    {
// Аутентифицируем пользователя
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
// Генерируем токен
        $authToken = new AuthToken(
// Случайная строка длиной 40 символов
bin2hex(random_bytes(40)),
$user->uuid(),
// Срок годности - 1 день
(new DateTimeImmutable())->modify('+1 day')
);
// Сохраняем токен в репозиторий
$this->authTokensRepository->save($authToken);
// Возвращаем токен
return new SuccessfulResponse([
    'token' => (string)$authToken->token(),
]);
}
}