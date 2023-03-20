<?php

declare(strict_types = 1);

namespace PSX\Framework\Model;


class RoutingRoute implements \JsonSerializable
{
    protected ?string $method = null;
    protected ?string $path = null;
    protected ?string $operationId = null;
    public function setMethod(?string $method) : void
    {
        $this->method = $method;
    }
    public function getMethod() : ?string
    {
        return $this->method;
    }
    public function setPath(?string $path) : void
    {
        $this->path = $path;
    }
    public function getPath() : ?string
    {
        return $this->path;
    }
    public function setOperationId(?string $operationId) : void
    {
        $this->operationId = $operationId;
    }
    public function getOperationId() : ?string
    {
        return $this->operationId;
    }
    public function jsonSerialize() : object
    {
        return (object) array_filter(array('method' => $this->method, 'path' => $this->path, 'operationId' => $this->operationId), static function ($value) : bool {
            return $value !== null;
        });
    }
}

