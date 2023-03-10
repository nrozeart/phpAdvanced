<?php

namespace Geekbrains\PhpAdvanced\Blog\Repositories;

class Comment
{

    public function __construct(
        private int $id,
        private User $user,
        private Post $post,
        private string $commentText
        )
    {

    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post): void
    {
        $this->post = $post;
    }

    /**
     * @return string
     */
    public function getCommentText(): string
    {
        return $this->commentText;
    }

    /**
     * @param string $commentText
     */
    public function setCommentText(string $commentText): void
    {
        $this->commentText = $commentText;
    }


    public function __toString()
    {
        return $this->user . ' опубликовал комментарий: "' . $this->commentText . '"'.PHP_EOL;
    }
}