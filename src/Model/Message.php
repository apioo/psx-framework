<?php

declare(strict_types = 1);

namespace PSX\Framework\Model;


class Message implements \JsonSerializable
{
    protected ?bool $success = null;
    protected ?string $message = null;
    public function setSuccess(?bool $success) : void
    {
        $this->success = $success;
    }
    public function getSuccess() : ?bool
    {
        return $this->success;
    }
    public function setMessage(?string $message) : void
    {
        $this->message = $message;
    }
    public function getMessage() : ?string
    {
        return $this->message;
    }
    public function jsonSerialize() : object
    {
        return (object) array_filter(array('success' => $this->success, 'message' => $this->message), static function ($value) : bool {
            return $value !== null;
        });
    }
}

