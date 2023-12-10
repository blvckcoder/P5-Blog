<?php

//declare(strict_types=1);

namespace App\Entity;


class Post
{
    private int $id;
    private string $title;
    private string $excerpt;
    private string $content;
    private string $imgCard;
    private string $imgCover;
    private int $userId;
    private object $author;
    private string $createdDate;
    private ?string $updateDate = null;
    private string $postStatus = "draft";

    // category, slug, comment, tag

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


    //TITLE
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): object
    {
        $this->title = $title;
        return $this;
    }

    //EXCERPT
    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    public function setExcerpt(string $excerpt): object
    {
        $this->excerpt = $excerpt;
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

    //IMG CARD
    public function getImgCard(): string
    {
        return $this->imgCard;
    }

    public function setImgCard(string $imgCard): object
    {
        $this->imgCard = $imgCard;
        return $this;
    }

    //IMG COVER
    public function getImgCover(): string
    {
        return $this->imgCover;
    }

    public function setImgCover(string $imgCover): object
    {
        $this->imgCover = $imgCover;
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

    //UPDATE DATE
    public function getUpdateDate(): string
    {
        return $this->updateDate;
    }

    public function setUpdateDate(string $updateDate): object
    {
        $this->updateDate = $updateDate;
        return $this;
    }

    //POSTSTATUS
    public function getPostStatus(): string
    {
        return $this->postStatus;
    }

    public function setPostStatus(string $postStatus): object
    {
        $this->postStatus = $postStatus;
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
}
