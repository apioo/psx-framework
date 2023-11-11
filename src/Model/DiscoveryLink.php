<?php

declare(strict_types = 1);

namespace PSX\Framework\Model;


class DiscoveryLink implements \JsonSerializable, \PSX\Record\RecordableInterface
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
    public function toRecord() : \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('rel', $this->rel);
        $record->put('href', $this->href);
        $record->put('method', $this->method);
        return $record;
    }
    public function jsonSerialize() : object
    {
        return (object) $this->toRecord()->getAll();
    }
}

