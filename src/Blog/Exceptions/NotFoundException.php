<?php

namespace Geekbrains\PhpAdvanced\Blog\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends AppException implements NotFoundExceptionInterface
{

}