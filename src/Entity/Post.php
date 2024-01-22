<?php

declare(strict_types=1);

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
    private User $author;
    private string $createdDate;
    private ?string $updateDate = null;
    private string $postStatus = "draft";
    private array $comment;

    // category, slug, comment, tag

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


    //TITLE
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    //EXCERPT
    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    public function setExcerpt(string $excerpt): self
    {
        $this->excerpt = $excerpt;
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

    //IMG CARD
    public function getImgCard(): string
    {
        return $this->imgCard;
    }

    public function setImgCard(string $imgCard): self
    {
        $this->imgCard = $imgCard;
        return $this;
    }

    //IMG COVER
    public function getImgCover(): string
    {
        return $this->imgCover;
    }

    public function setImgCover(string $imgCover): self
    {
        $this->imgCover = $imgCover;
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

    //UPDATE DATE
    public function getUpdateDate(): ?string
    {
        return $this->updateDate;
    }

    public function setUpdateDate(string $updateDate): self
    {
        $this->updateDate = $updateDate;
        return $this;
    }

    //POSTSTATUS
    public function getPostStatus(): string
    {
        return $this->postStatus;
    }

    public function setPostStatus(string $postStatus): self
    {
        $this->postStatus = $postStatus;
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

    //USER
    public function getComment(): array
    {
        return $this->comment;
    }

    public function setComment(array $comment): self
    {
        $this->comment = $comment;
        return $this;
    }
}
