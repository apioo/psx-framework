<?php

declare(strict_types = 1);

namespace PSX\Framework\App\Model;


class Population implements \JsonSerializable
{
    protected ?int $id = null;
    protected ?int $place = null;
    protected ?string $region = null;
    protected ?int $population = null;
    protected ?int $users = null;
    protected ?float $worldUsers = null;
    protected ?\PSX\DateTime\LocalDateTime $insertDate = null;
    public function setId(?int $id) : void
    {
        $this->id = $id;
    }
    public function getId() : ?int
    {
        return $this->id;
    }
    public function setPlace(?int $place) : void
    {
        $this->place = $place;
    }
    public function getPlace() : ?int
    {
        return $this->place;
    }
    public function setRegion(?string $region) : void
    {
        $this->region = $region;
    }
    public function getRegion() : ?string
    {
        return $this->region;
    }
    public function setPopulation(?int $population) : void
    {
        $this->population = $population;
    }
    public function getPopulation() : ?int
    {
        return $this->population;
    }
    public function setUsers(?int $users) : void
    {
        $this->users = $users;
    }
    public function getUsers() : ?int
    {
        return $this->users;
    }
    public function setWorldUsers(?float $worldUsers) : void
    {
        $this->worldUsers = $worldUsers;
    }
    public function getWorldUsers() : ?float
    {
        return $this->worldUsers;
    }
    public function setInsertDate(?\PSX\DateTime\LocalDateTime $insertDate) : void
    {
        $this->insertDate = $insertDate;
    }
    public function getInsertDate() : ?\PSX\DateTime\LocalDateTime
    {
        return $this->insertDate;
    }
    public function jsonSerialize() : object
    {
        return (object) array_filter(array('id' => $this->id, 'place' => $this->place, 'region' => $this->region, 'population' => $this->population, 'users' => $this->users, 'worldUsers' => $this->worldUsers, 'insertDate' => $this->insertDate), static function ($value) : bool {
            return $value !== null;
        });
    }
}

