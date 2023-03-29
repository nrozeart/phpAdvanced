<?php

namespace Geekbrains\PhpAdvanced\Http\Actions\Likes;

use Geekbrains\PhpAdvanced\Blog\Exceptions\HttpException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\LikeAlreadyExists;
use Geekbrains\PhpAdvanced\Blog\Exceptions\PostNotFoundException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Geekbrains\PhpAdvanced\Blog\Like;
use Geekbrains\PhpAdvanced\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\UUID;
use Geekbrains\PhpAdvanced\Http\Actions\ActionInterface;
use Geekbrains\PhpAdvanced\Http\Request;
use Geekbrains\PhpAdvanced\Http\Response;
use Geekbrains\PhpAdvanced\Http\ErrorResponse;
use Geekbrains\PhpAdvanced\Http\SuccessfulResponse;


class CreatePostLike implements ActionInterface
{
    public   function __construct(
        private LikesRepositoryInterface $likesRepository,
        private PostsRepositoryInterface $postRepository,
        private UsersRepositoryInterface $usersRepository,
    )
    {
    }


    /**
     * @throws InvalidArgumentException
     */
    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->JsonBodyField('post_uuid');
            $userUuid = $request->JsonBodyField('user_uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        //TODO тоже и для юзера
        try {
            $this->usersRepository->get(new UUID($userUuid));
        } catch (UserNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $this->postRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $this->likesRepository->checkUserLikeForPostExists($postUuid, $userUuid);
        } catch (LikeAlreadyExists $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newLikeUuid = UUID::random();

        $like = new Like(
            uuid: $newLikeUuid,
            post_id: new UUID($postUuid),
            user_id: new UUID($userUuid),

        );

        $this->likesRepository->save($like);

        return new SuccessFulResponse(
            ['uuid' => (string)$newLikeUuid]
        );
    }
}