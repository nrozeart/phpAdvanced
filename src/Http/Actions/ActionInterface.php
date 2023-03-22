<?php

namespace Geekbrains\PhpAdvanced\Http\Actions;

use Geekbrains\PhpAdvanced\Http\Request;
use Geekbrains\PhpAdvanced\Http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}