<?php

declare(strict_types = 1);

namespace PSX\Framework\Model;


class RoutingCollection implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    /**
     * @var array<RoutingRoute>|null
     */
    protected ?array $routings = null;
    /**
     * @param array<RoutingRoute>|null $routings
     */
    public function setRoutings(?array $routings) : void
    {
        $this->routings = $routings;
    }
    /**
     * @return array<RoutingRoute>|null
     */
    public function getRoutings() : ?array
    {
        return $this->routings;
    }
    public function toRecord() : \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('routings', $this->routings);
        return $record;
    }
    public function jsonSerialize() : object
    {
        return (object) $this->toRecord()->getAll();
    }
}

