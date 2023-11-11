<?php

declare(strict_types = 1);

namespace PSX\Framework\Model;


class Welcome implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $message = null;
    protected ?string $url = null;
    public function setMessage(?string $message) : void
    {
        $this->message = $message;
    }
    public function getMessage() : ?string
    {
        return $this->message;
    }
    public function setUrl(?string $url) : void
    {
        $this->url = $url;
    }
    public function getUrl() : ?string
    {
        return $this->url;
    }
    public function toRecord() : \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('message', $this->message);
        $record->put('url', $this->url);
        return $record;
    }
    public function jsonSerialize() : object
    {
        return (object) $this->toRecord()->getAll();
    }
}

