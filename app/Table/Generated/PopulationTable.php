<?php

namespace PSX\Framework\App\Table\Generated;

class PopulationTable extends \PSX\Sql\TableAbstract
{
    public const NAME = 'population';
    public const COLUMN_ID = 'id';
    public const COLUMN_PLACE = 'place';
    public const COLUMN_REGION = 'region';
    public const COLUMN_POPULATION = 'population';
    public const COLUMN_USERS = 'users';
    public const COLUMN_WORLD_USERS = 'world_users';
    public const COLUMN_INSERT_DATE = 'insert_date';
    public function getName() : string
    {
        return self::NAME;
    }
    public function getColumns() : array
    {
        return array(self::COLUMN_ID => 0x3020000a, self::COLUMN_PLACE => 0x20000a, self::COLUMN_REGION => 0xa000ff, self::COLUMN_POPULATION => 0x20000a, self::COLUMN_USERS => 0x20000a, self::COLUMN_WORLD_USERS => 0x60000a, self::COLUMN_INSERT_DATE => 0x800000);
    }
    /**
     * @return \PSX\Framework\App\Table\Generated\PopulationRow[]
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findAll(?\PSX\Sql\Condition $condition = null, ?int $startIndex = null, ?int $count = null, ?string $sortBy = null, ?int $sortOrder = null, ?\PSX\Sql\Fields $fields = null) : iterable
    {
        return $this->doFindAll($condition, $startIndex, $count, $sortBy, $sortOrder, $fields);
    }
    /**
     * @return \PSX\Framework\App\Table\Generated\PopulationRow[]
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findBy(\PSX\Sql\Condition $condition, ?int $startIndex = null, ?int $count = null, ?string $sortBy = null, ?int $sortOrder = null, ?\PSX\Sql\Fields $fields = null) : iterable
    {
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder, $fields);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneBy(\PSX\Sql\Condition $condition, ?\PSX\Sql\Fields $fields = null) : ?\PSX\Framework\App\Table\Generated\PopulationRow
    {
        return $this->doFindOneBy($condition, $fields);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function find(int $id) : ?\PSX\Framework\App\Table\Generated\PopulationRow
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('id', $id);
        return $this->doFindOneBy($condition);
    }
    /**
     * @return \PSX\Framework\App\Table\Generated\PopulationRow[]
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findById(int $value, ?int $startIndex = null, ?int $count = null, ?string $sortBy = null, ?int $sortOrder = null) : iterable
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('id', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneById(int $value) : ?\PSX\Framework\App\Table\Generated\PopulationRow
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('id', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @return \PSX\Framework\App\Table\Generated\PopulationRow[]
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByPlace(int $value, ?int $startIndex = null, ?int $count = null, ?string $sortBy = null, ?int $sortOrder = null) : iterable
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('place', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByPlace(int $value) : ?\PSX\Framework\App\Table\Generated\PopulationRow
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('place', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @return \PSX\Framework\App\Table\Generated\PopulationRow[]
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByRegion(string $value, ?int $startIndex = null, ?int $count = null, ?string $sortBy = null, ?int $sortOrder = null) : iterable
    {
        $condition = new \PSX\Sql\Condition();
        $condition->like('region', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByRegion(string $value) : ?\PSX\Framework\App\Table\Generated\PopulationRow
    {
        $condition = new \PSX\Sql\Condition();
        $condition->like('region', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @return \PSX\Framework\App\Table\Generated\PopulationRow[]
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByPopulation(int $value, ?int $startIndex = null, ?int $count = null, ?string $sortBy = null, ?int $sortOrder = null) : iterable
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('population', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByPopulation(int $value) : ?\PSX\Framework\App\Table\Generated\PopulationRow
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('population', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @return \PSX\Framework\App\Table\Generated\PopulationRow[]
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByUsers(int $value, ?int $startIndex = null, ?int $count = null, ?string $sortBy = null, ?int $sortOrder = null) : iterable
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('users', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByUsers(int $value) : ?\PSX\Framework\App\Table\Generated\PopulationRow
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('users', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @return \PSX\Framework\App\Table\Generated\PopulationRow[]
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByWorldUsers(float $value, ?int $startIndex = null, ?int $count = null, ?string $sortBy = null, ?int $sortOrder = null) : iterable
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('world_users', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByWorldUsers(float $value) : ?\PSX\Framework\App\Table\Generated\PopulationRow
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('world_users', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @return \PSX\Framework\App\Table\Generated\PopulationRow[]
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByInsertDate(\DateTime $value, ?int $startIndex = null, ?int $count = null, ?string $sortBy = null, ?int $sortOrder = null) : iterable
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('insert_date', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByInsertDate(\DateTime $value) : ?\PSX\Framework\App\Table\Generated\PopulationRow
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('insert_date', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function create(\PSX\Framework\App\Table\Generated\PopulationRow $record) : int
    {
        return $this->doCreate($record);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function update(\PSX\Framework\App\Table\Generated\PopulationRow $record) : int
    {
        return $this->doUpdate($record);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function delete(\PSX\Framework\App\Table\Generated\PopulationRow $record) : int
    {
        return $this->doDelete($record);
    }
    protected function newRecord(array $row) : \PSX\Framework\App\Table\Generated\PopulationRow
    {
        return new \PSX\Framework\App\Table\Generated\PopulationRow($row);
    }
}