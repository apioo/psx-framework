<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
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
 * @Title("entity")
 * @Description("Represents an internet population entity")
 * @Required({"place", "region", "population", "users", "worldUsers"})
 */
class Entity
{
    /**
     * @Type("integer")
     * @Description("Unique id for each entry")
     */
    protected $id;

    /**
     * @Type("integer")
     * @Minimum(1)
     * @Maximum(64)
     * @Description("Position in the top list")
     */
    protected $place;

    /**
     * @Type("string")
     * @MinLength(3)
     * @MaxLength(64)
     * @Pattern("[A-z]+")
     * @Description("Name of the region")
     */
    protected $region;

    /**
     * @Type("integer")
     * @Description("Complete number of population")
     */
    protected $population;

    /**
     * @Type("integer")
     * @Description("Number of internet users")
     */
    protected $users;

    /**
     * @Type("number")
     * @Description("Percentage users of the world")
     */
    protected $worldUsers;

    /**
     * @Type("string")
     * @Format("date-time")
     * @Description("Date when the entity was created")
     */
    protected $datetime;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getPlace()
    {
        return $this->place;
    }

    public function setPlace($place)
    {
        $this->place = $place;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function setRegion($region)
    {
        $this->region = $region;
    }

    public function getPopulation()
    {
        return $this->population;
    }

    public function setPopulation($population)
    {
        $this->population = $population;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers($users)
    {
        $this->users = $users;
    }

    public function getWorldUsers()
    {
        return $this->worldUsers;
    }

    public function setWorldUsers($worldUsers)
    {
        $this->worldUsers = $worldUsers;
    }

    public function getDatetime()
    {
        return $this->datetime;
    }

    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }
}
