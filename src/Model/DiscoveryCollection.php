<?php

declare(strict_types = 1);

namespace PSX\Framework\Model;


class DiscoveryCollection implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    /**
     * @var array<DiscoveryLink>|null
     */
    protected ?array $links = null;
    /**
     * @param array<DiscoveryLink>|null $links
     */
    public function setLinks(?array $links) : void
    {
        $this->links = $links;
    }
    /**
     * @return array<DiscoveryLink>|null
     */
    public function getLinks() : ?array
    {
        return $this->links;
    }
    public function toRecord() : \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('links', $this->links);
        return $record;
    }
    public function jsonSerialize() : object
    {
        return (object) $this->toRecord()->getAll();
    }
}

