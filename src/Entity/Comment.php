<?php

declare(strict_types=1);

namespace App\Entity;


class Comment
{
    private int $id;
    private int $userId;
    private User $author;
    private string $content;
    private string $createdDate;
    private string $commentStatus = "blocked";
    private int $postId;
    private Post $post;

    //ID
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    //USER
    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    //AUTHOR
    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;
        return $this;
    }

    //CONTENT
    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    //CREATION DATE
    public function getCreatedDate(): string
    {
        return $this->createdDate;
    }

    public function setCreatedDate(string $createdDate): self
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    //COMMENTSTATUS
    public function getCommentStatus(): string
    {
        return $this->commentStatus;
    }

    public function setcommentStatus(string $commentStatus): self
    {
        $this->commentStatus = $commentStatus;
        return $this;
    }

    //POST
    public function getPostId(): int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): self
    {
        $this->postId = $postId;
        return $this;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;
        return $this;
    }
}
