<?php
namespace Geekbrains\PhpAdvanced\Blog;

use Geekbrains\PhpAdvanced\Person\Name;

class User
{
    private int $id;
    private Name $username;
    private string $login;

    /**
     * @param int $id
     * @param Name $username
     * @param string $login
     */
    public function __construct(int $id, Name $username, string $login)
    {
        $this->id = $id;
        $this->username = $username;
        $this->login = $login;
    }
    public function __toString(): string
    {
        return "Юзер $this->id с именем $this->username и логином $this->login." . PHP_EOL;
    }

}