<?php

namespace PSX\Framework\Tests\Table;

class SessionHandlerSqlTestRow extends \PSX\Record\Record
{
    public function setId(?string $id) : void
    {
        $this->setProperty('id', $id);
    }
    public function getId() : ?string
    {
        return $this->getProperty('id');
    }
    public function setContent(mixed $content) : void
    {
        $this->setProperty('content', $content);
    }
    public function getContent() : mixed
    {
        return $this->getProperty('content');
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