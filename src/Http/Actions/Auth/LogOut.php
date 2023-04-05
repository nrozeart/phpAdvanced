<?php

namespace Geekbrains\PhpAdvanced\Http\Actions\Auth;

use Geekbrains\PhpAdvanced\Blog\Exceptions\AuthException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\AuthTokenNotFoundException;
use Geekbrains\PhpAdvanced\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Geekbrains\PhpAdvanced\Http\Actions\ActionInterface;
use Geekbrains\PhpAdvanced\Http\Auth\BearerTokenAuthentication;
use Geekbrains\PhpAdvanced\Http\Request;
use Geekbrains\PhpAdvanced\Http\SuccessfulResponse;
use Geekbrains\PhpAdvanced\Http\Response;

class LogOut implements ActionInterface
{
    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository,
        private BearerTokenAuthentication $authentication
    ) {
    }

    /**
     * @throws AuthException
     */
    public function handle(Request $request): Response
    {
        $token = $this->authentication->getAuthTokenString($request);

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException $exception) {
            throw new AuthException($exception->getMessage());
        }

        $authToken->setExpiresOn(new \DateTimeImmutable("now"));


        $this->authTokensRepository->save($authToken);

        return new SuccessfulResponse([
            'token' => $authToken->token()
        ]);
    }
}