<?php

namespace Geekbrains\PhpAdvanced\Blog;

use Geekbrains\PhpAdvanced\Person\Person;

class Post
{
    private int $postId;
    private Person $author;
    private string $title;
    private string $text;

    /**
     * @param Person $author
     * @param string $text
     * @param string $title
     * @param int $postId
     */
    public function __construct(int $postId, Person $author, string $title, string $text)
    {
        $this->postId = $postId;
        $this->text = $text;
        $this->title = $title;
        $this->author = $author;
    }

    public function __toString()
    {
        return $this->author . ' опубликовал статью с названием: "' . $this->title . '" и содержанием: "'. $this->text . '"' .PHP_EOL;
    }
}