<?php

namespace PSX\Framework\Tests\Table;

class HandlerCommentRow extends \PSX\Record\Record
{
    public function setId(?int $id) : void
    {
        $this->setProperty('id', $id);
    }
    public function getId() : ?int
    {
        return $this->getProperty('id');
    }
    public function setUserId(?int $userId) : void
    {
        $this->setProperty('userId', $userId);
    }
    public function getUserId() : ?int
    {
        return $this->getProperty('userId');
    }
    public function setTitle(?string $title) : void
    {
        $this->setProperty('title', $title);
    }
    public function getTitle() : ?string
    {
        return $this->getProperty('title');
    }
    public function setDate(?\DateTime $date) : void
    {
        $this->setProperty('date', $date);
    }
    public function getDate() : ?\DateTime
    {
        return $this->getProperty('date');
    }
}