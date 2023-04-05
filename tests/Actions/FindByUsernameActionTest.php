<?php

namespace GeekBrains\Blog\UnitTests\Actions;

use Geekbrains\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Geekbrains\PhpAdvanced\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\PhpAdvanced\Blog\User;
use Geekbrains\PhpAdvanced\Blog\UUID;
use Geekbrains\PhpAdvanced\Http\Actions\User\FindByUsername;
use Geekbrains\PhpAdvanced\Http\ErrorResponse;
use Geekbrains\PhpAdvanced\Http\Request;
use Geekbrains\PhpAdvanced\Http\SuccessfulResponse;
use Geekbrains\PhpAdvanced\Person\Name;
use PHPUnit\Framework\TestCase;

class FindByUsernameActionTest extends TestCase
{
// Запускаем тест в отдельном процессе
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
// Тест, проверяющий, что будет возвращён неудачный ответ,
// если в запросе нет параметра username
    public function testItReturnsErrorResponseIfNoUsernameProvided(): void
    {
// Создаём объект запроса
// Вместо суперглобальных переменных
// передаём простые массивы
        $request = new Request([], [], '');
// Создаём стаб репозитория пользователей
$usersRepository = $this->usersRepository([]);
//Создаём объект действия
$action = new FindByUsername($usersRepository);
// Запускаем действие
$response = $action->handle($request);
// Проверяем, что ответ - неудачный
$this->assertInstanceOf(ErrorResponse::class, $response);
// Описываем ожидание того, что будет отправлено в поток вывода
$this->expectOutputString('{"success":false,"reason":"No such query param in the request: username"}');
// Отправляем ответ в поток вывода
$response->send();
}
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
// Тест, проверяющий, что будет возвращён неудачный ответ,
// если пользователь не найден
    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
// Теперь запрос будет иметь параметр username
        $request = new Request(['username' => 'ivan'], [], '');
// Репозиторий пользователей по-прежнему пуст
        $usersRepository = $this->usersRepository([]);
        $action = new FindByUsername($usersRepository);
        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Not found"}');
        $response->send();
    }
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws InvalidArgumentException
     */
// Тест, проверяющий, что будет возвращён удачный ответ,
// если пользователь найден
    public function testItReturnsSuccessfulResponse(): void
{
$request = new Request(['username' => 'ivan'], [], '');
// На этот раз в репозитории есть нужный нам пользователь
$usersRepository = $this->usersRepository([
new User(
UUID::random(),
new Name('Ivan', 'Nikitin'),
    'ivan',
    '123'
),
]);
$action = new FindByUsername($usersRepository);
$response = $action->handle($request);
// Проверяем, что ответ - удачный
$this->assertInstanceOf(SuccessfulResponse::class, $response);
$this->expectOutputString('{"success":true,"data":{"username":"ivan","name":"Ivan Nikitin"}}');
$response->send();
}
// Функция, создающая стаб репозитория пользователей,
// принимает массив "существующих" пользователей
private function usersRepository(array $users): UsersRepositoryInterface
{
// В конструктор анонимного класса передаём массив пользователей
    return new class($users) implements UsersRepositoryInterface {
        public function __construct(
            private array $users
        ) {
        }
        public function save(User $user): void
        {
        }
        public function get(UUID $uuid): User
        {
            throw new UserNotFoundException("Not found");
        }
        public function getByUsername(string $username): User
        {
            foreach ($this->users as $user) {
                if ($user instanceof User && $username === $user->username())
                {
                    return $user;
}
            }
            throw new UserNotFoundException("Not found");
        }
    };
}
}