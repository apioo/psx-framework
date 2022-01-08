<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Framework\App\Model;

/**
 * @Description("Represents an internet population entity")
 * @Required({"place", "region", "population", "users", "worldUsers"})
 */
class Entity
{
    /**
     * @var integer
     * @Description("Unique id for each entry")
     */
    protected $id;

    /**
     * @var integer
     * @Minimum(1)
     * @Maximum(64)
     * @Description("Position in the top list")
     */
    protected $place;

    /**
     * @var string
     * @MinLength(3)
     * @MaxLength(64)
     * @Pattern("[A-z]+")
     * @Description("Name of the region")
     */
    protected $region;

    /**
     * @var integer
     * @Description("Complete number of population")
     */
    protected $population;

    /**
     * @var integer
     * @Description("Number of internet users")
     */
    protected $users;

    /**
     * @var float
     * @Description("Percentage users of the world")
     */
    protected $worldUsers;

    /**
     * @var \DateTime
     * @Description("Date when the entity was created")
     */
    protected $datetime;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getPlace(): ?int
    {
        return $this->place;
    }

    /**
     * @param int $place
     */
    public function setPlace(int $place): void
    {
        $this->place = $place;
    }

    /**
     * @return string
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    /**
     * @return int
     */
    public function getPopulation(): ?int
    {
        return $this->population;
    }

    /**
     * @param int $population
     */
    public function setPopulation(int $population): void
    {
        $this->population = $population;
    }

    /**
     * @return int
     */
    public function getUsers(): ?int
    {
        return $this->users;
    }

    /**
     * @param int $users
     */
    public function setUsers(int $users): void
    {
        $this->users = $users;
    }

    /**
     * @return float
     */
    public function getWorldUsers(): ?float
    {
        return $this->worldUsers;
    }

    /**
     * @param float $worldUsers
     */
    public function setWorldUsers(float $worldUsers): void
    {
        $this->worldUsers = $worldUsers;
    }

    /**
     * @return \DateTime
     */
    public function getDatetime(): ?\DateTime
    {
        return $this->datetime;
    }

    /**
     * @param \DateTime $datetime
     */
    public function setDatetime(\DateTime $datetime): void
    {
        $this->datetime = $datetime;
    }
}
