<?php

namespace Geekbrains\PhpAdvanced\Blog;

use Geekbrains\PhpAdvanced\Person\Person;

class Post
{
    private int $id;
    private Person $author;
    private string $text;

    /**
     * @param Person $author
     * @param string $text
     */
    public function __construct(int $id, Person $author, string $text)
    {
        $this->id = $id;
        $this->text = $text;
        $this->author = $author;
    }

    public function __toString()
    {
        return $this->author . ' пишет: ' . $this->text . PHP_EOL;
    }
}