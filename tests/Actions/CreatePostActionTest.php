<?php

namespace Actions;

use Geekbrains\PhpAdvanced\Blog\Exceptions\PostNotFoundException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Geekbrains\PhpAdvanced\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\User;
use Geekbrains\PhpAdvanced\Http\Actions\Posts\CreatePost;
use Geekbrains\PhpAdvanced\Http\ErrorResponse;
use Geekbrains\PhpAdvanced\Http\Request;
use Geekbrains\PhpAdvanced\Http\SuccessfulResponse;
use Geekbrains\PhpAdvanced\Person\Name;
use JsonException;
use PHPUnit\Framework\TestCase;
use Geekbrains\PhpAdvanced\Blog\Post;
use Geekbrains\PhpAdvanced\Blog\UUID;


class CreatePostActionTest extends TestCase
{
    private function postsRepository(): PostsRepositoryInterface
    {
        return new class() implements PostsRepositoryInterface {
            private bool $called = false;

            public function __construct()
            {
            }

            public function save(Post $post): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): Post
            {
                throw new PostNotFoundException('Not found');
            }

            public function getByTitle(string $title): Post
            {
                throw new PostNotFoundException('Not found');
            }

            public function getCalled(): bool
            {
                return $this->called;
            }

            public function delete(UUID $uuid): void
            {
            }
        };
    }
    private function usersRepository(array $users): UsersRepositoryInterface
    {
        return new class($users) implements UsersRepositoryInterface
        {
            public function __construct(
                private array $users
            )
            {
            }

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && (string)$uuid == $user->uuid()) {
                        return $user;
                    }
                }
                throw new UserNotFoundException('Cannot find user: ' . $uuid);
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException('Not found');
            }
        };
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request([], [], '{"author_uuid":"10373537-0805-4d7a-830e-22b481b4859c","title":"title","text":"text"}');

        $postsRepository = $this->postsRepository();

        $usersRepository = $this->usersRepository([
            new User(
                new UUID('10373537-0805-4d7a-830e-22b481b4859c'),
                new Name('name', 'surname'),
                'username',

            ),
        ]);

        $action = new CreatePost($usersRepository, $postsRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);

        $this->setOutputCallback(function ($data){
            $dataDecode = json_decode(
                $data,
                associative: true,
                flags: JSON_THROW_ON_ERROR
            );

            $dataDecode['data']['uuid'] = "351739ab-fc33-49ae-a62d-b606b7038c87";
            return json_encode(
                $dataDecode,
                JSON_THROW_ON_ERROR
            );
        });

        $this->expectOutputString('{"success":true,"data":{"uuid":"351739ab-fc33-49ae-a62d-b606b7038c87"}}');


        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfNotFoundUser(): void
    {
        $request = new Request([], [], '{"author_uuid":"10373537-0805-4d7a-830e-22b481b4859c","title":"title","text":"text"}');

        $postsRepository = $this->postsRepository();
        $usersRepository = $this->usersRepository([]);

        $action = new CreatePost($usersRepository, $postsRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Cannot find user: 10373537-0805-4d7a-830e-22b481b4859c"}');

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws JsonException
     */
    public function testItReturnsErrorResponseIfNoTextProvided(): void
    {
        $request = new Request([], [], '{"author_uuid":"10373537-0805-4d7a-830e-22b481b4859c","title":"title"}');

        $postsRepository = $this->postsRepository([]);
        $usersRepository = $this->usersRepository([
            new User(
                new UUID('10373537-0805-4d7a-830e-22b481b4859c'),
                new Name('Ivan', 'Nikitin'), 'ivan',
            ),
        ]);

        $action = new CreatePost($usersRepository, $postsRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No such field: text"}');

        $response->send();
    }
}