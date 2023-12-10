<?php

//declare(strict_types=1);

namespace App\Entity;


class Comment
{
    private int $id;
    private int $userId;
    private object $author;
    private string $content;
    private string $createdDate;
    private string $commentStatus = "blocked";
    private int $postId;

    //ID
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): object
    {
        $this->id = $id;
        return $this;
    }

    //USER
    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): object
    {
        $this->userId = $userId;
        return $this;
    }

    //AUTHOR
    public function getAuthor(): object
    {
        return $this->author;
    }

    public function setAuthor(object $author): object
    {
        $this->author = $author;
        return $this;
    }

    //CONTENT
    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): object
    {
        $this->content = $content;
        return $this;
    }

    //CREATION DATE
    public function getCreatedDate(): string
    {
        return $this->createdDate;
    }

    public function setCreatedDate(string $createdDate): object
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    //COMMENTSTATUS
    public function getCommentStatus(): string
    {
        return $this->commentStatus;
    }

    public function setcommentStatus(string $commentStatus): object
    {
        $this->commentStatus = $commentStatus;
        return $this;
    }

    //POST
    public function getPostId(): int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): object
    {
        $this->postId = $postId;
        return $this;
    }
}
