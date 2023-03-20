<?php

declare(strict_types = 1);

namespace PSX\Framework\Model;


class RoutingCollection implements \JsonSerializable
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
    public function getRoutings() : ?array
    {
        return $this->routings;
    }
    public function jsonSerialize() : object
    {
        return (object) array_filter(array('routings' => $this->routings), static function ($value) : bool {
            return $value !== null;
        });
    }
}

