<?php

namespace Geekbrains\PhpAdvanced\Blog\Repositories;

use Geekbrains\PhpAdvanced\Person\Person;

class Comments
{
    private int $commentId;
    private Person $author;
    private int $postId;
    private string $commentText;

    /**
     * @param int $commentId
     * @param Person $author
     * @param int $postId
     * @param string $commentText
     */
    public function __construct(int $commentId, Person $author, int $postId, string $commentText)
    {
        $this->commentId = $commentId;
        $this->author = $author;
        $this->postId = $postId;
        $this->commentText = $commentText;
    }

    public function __toString()
    {
        return $this->author . ' опубликовал комментарий "' . $this->commentText . '" к статье номер: "'. $this->postId . '"' .PHP_EOL;
    }
}