<?php

namespace Geekbrains\PhpAdvanced\Http\Actions\Posts;

use Geekbrains\PhpAdvanced\Blog\Exceptions\PostNotFoundException;
use Geekbrains\PhpAdvanced\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\UUID;
use Geekbrains\PhpAdvanced\Http\Actions\ActionInterface;
use Geekbrains\PhpAdvanced\Http\Request;
use Geekbrains\PhpAdvanced\Http\Response;
use Geekbrains\PhpAdvanced\Http\ErrorResponse;
use Geekbrains\PhpAdvanced\Http\SuccessfulResponse;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository
    )
    {
    }


    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->query('uuid');
            $this->postsRepository->get(new UUID($postUuid));

        } catch (PostNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        $this->postsRepository->delete(new UUID($postUuid));

        return new SuccessfulResponse([
            'uuid' => $postUuid,
        ]);
    }
}