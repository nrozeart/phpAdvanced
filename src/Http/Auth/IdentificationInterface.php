<?php

namespace Geekbrains\PhpAdvanced\Http\Auth;

use Geekbrains\PhpAdvanced\Blog\User;
use Geekbrains\PhpAdvanced\Http\Request;

interface IdentificationInterface
{
// Контракт описывает единственный метод,
// получающий пользователя из запроса
    public function user(Request $request): User;
}