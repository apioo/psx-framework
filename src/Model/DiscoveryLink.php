<?php

declare(strict_types = 1);

namespace PSX\Framework\Model;


class DiscoveryLink implements \JsonSerializable
{
    protected ?string $rel = null;
    protected ?string $href = null;
    protected ?string $method = null;
    public function setRel(?string $rel) : void
    {
        $this->rel = $rel;
    }
    public function getRel() : ?string
    {
        return $this->rel;
    }
    public function setHref(?string $href) : void
    {
        $this->href = $href;
    }
    public function getHref() : ?string
    {
        return $this->href;
    }
    public function setMethod(?string $method) : void
    {
        $this->method = $method;
    }
    public function getMethod() : ?string
    {
        return $this->method;
    }
    public function jsonSerialize() : object
    {
        return (object) array_filter(array('rel' => $this->rel, 'href' => $this->href, 'method' => $this->method), static function ($value) : bool {
            return $value !== null;
        });
    }
}

