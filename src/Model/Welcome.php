<?php

declare(strict_types = 1);

namespace PSX\Framework\Model;


class Welcome implements \JsonSerializable
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
    public function jsonSerialize() : object
    {
        return (object) array_filter(array('message' => $this->message, 'url' => $this->url), static function ($value) : bool {
            return $value !== null;
        });
    }
}

