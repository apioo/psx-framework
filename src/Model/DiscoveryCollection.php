<?php

declare(strict_types = 1);

namespace PSX\Framework\Model;


class DiscoveryCollection implements \JsonSerializable
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
    public function getLinks() : ?array
    {
        return $this->links;
    }
    public function jsonSerialize() : object
    {
        return (object) array_filter(array('links' => $this->links), static function ($value) : bool {
            return $value !== null;
        });
    }
}

