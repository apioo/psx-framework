<?php

namespace PSX\Framework\Tests\Table;

class SessionHandlerSqlTestTable extends \PSX\Sql\TableAbstract
{
    public const NAME = 'psx_session_handler_sql_test';
    public const COLUMN_ID = 'id';
    public const COLUMN_CONTENT = 'content';
    public const COLUMN_DATE = 'date';
    public function getName() : string
    {
        return self::NAME;
    }
    public function getColumns() : array
    {
        return array(self::COLUMN_ID => 0x10a00020, self::COLUMN_CONTENT => 0xc00000, self::COLUMN_DATE => 0x800000);
    }
    /**
     * @return \PSX\Framework\Tests\Table\SessionHandlerSqlTestRow[]
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findAll(?\PSX\Sql\Condition $condition = null, ?int $startIndex = null, ?int $count = null, ?string $sortBy = null, ?int $sortOrder = null, ?\PSX\Sql\Fields $fields = null) : iterable
    {
        return $this->doFindAll($condition, $startIndex, $count, $sortBy, $sortOrder, $fields);
    }
    /**
     * @return \PSX\Framework\Tests\Table\SessionHandlerSqlTestRow[]
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findBy(\PSX\Sql\Condition $condition, ?int $startIndex = null, ?int $count = null, ?string $sortBy = null, ?int $sortOrder = null, ?\PSX\Sql\Fields $fields = null) : iterable
    {
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder, $fields);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneBy(\PSX\Sql\Condition $condition, ?\PSX\Sql\Fields $fields = null) : ?\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow
    {
        return $this->doFindOneBy($condition, $fields);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function find(string $id) : ?\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow
    {
        $condition = new \PSX\Sql\Condition();
        $condition->like('id', $id);
        return $this->doFindOneBy($condition);
    }
    /**
     * @return \PSX\Framework\Tests\Table\SessionHandlerSqlTestRow[]
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findById(string $value, ?int $startIndex = null, ?int $count = null, ?string $sortBy = null, ?int $sortOrder = null) : iterable
    {
        $condition = new \PSX\Sql\Condition();
        $condition->like('id', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneById(string $value) : ?\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow
    {
        $condition = new \PSX\Sql\Condition();
        $condition->like('id', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @return \PSX\Framework\Tests\Table\SessionHandlerSqlTestRow[]
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByContent(mixed $value, ?int $startIndex = null, ?int $count = null, ?string $sortBy = null, ?int $sortOrder = null) : iterable
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('content', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByContent(mixed $value) : ?\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('content', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @return \PSX\Framework\Tests\Table\SessionHandlerSqlTestRow[]
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByDate(\DateTime $value, ?int $startIndex = null, ?int $count = null, ?string $sortBy = null, ?int $sortOrder = null) : iterable
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('date', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByDate(\DateTime $value) : ?\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow
    {
        $condition = new \PSX\Sql\Condition();
        $condition->equals('date', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function create(\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow $record) : int
    {
        return $this->doCreate($record);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function update(\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow $record) : int
    {
        return $this->doUpdate($record);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function delete(\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow $record) : int
    {
        return $this->doDelete($record);
    }
    protected function newRecord(array $row) : \PSX\Framework\Tests\Table\SessionHandlerSqlTestRow
    {
        return new \PSX\Framework\Tests\Table\SessionHandlerSqlTestRow($row);
    }
}