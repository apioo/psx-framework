<?php

namespace PSX\Framework\Tests\Table;

/**
 * @extends \PSX\Sql\TableAbstract<\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow>
 */
class SessionHandlerSqlTestTable extends \PSX\Sql\TableAbstract
{
    public const NAME = 'psx_session_handler_sql_test';
    public const COLUMN_ID = 'id';
    public const COLUMN_CONTENT = 'content';
    public const COLUMN_DATE = 'date';
    public function getName(): string
    {
        return self::NAME;
    }
    public function getColumns(): array
    {
        return [self::COLUMN_ID => 0x10a00020, self::COLUMN_CONTENT => 0xc00000, self::COLUMN_DATE => 0x800000];
    }
    /**
     * @return array<\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findAll(?\PSX\Sql\Condition $condition = null, ?int $startIndex = null, ?int $count = null, ?\PSX\Framework\Tests\Table\SessionHandlerSqlTestColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        return $this->doFindAll($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @return array<\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findBy(\PSX\Sql\Condition $condition, ?int $startIndex = null, ?int $count = null, ?\PSX\Framework\Tests\Table\SessionHandlerSqlTestColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneBy(\PSX\Sql\Condition $condition): ?\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow
    {
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function find(string $id): ?\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->like('id', $id);
        return $this->doFindOneBy($condition);
    }
    /**
     * @return array<\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findById(string $value, ?int $startIndex = null, ?int $count = null, ?\PSX\Framework\Tests\Table\SessionHandlerSqlTestColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->like('id', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneById(string $value): ?\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->like('id', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateById(string $value, \PSX\Framework\Tests\Table\SessionHandlerSqlTestRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->like('id', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteById(string $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->like('id', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @return array<\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByContent(mixed $value, ?int $startIndex = null, ?int $count = null, ?\PSX\Framework\Tests\Table\SessionHandlerSqlTestColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('content', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByContent(mixed $value): ?\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('content', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateByContent(mixed $value, \PSX\Framework\Tests\Table\SessionHandlerSqlTestRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('content', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteByContent(mixed $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('content', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @return array<\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByDate(\PSX\DateTime\LocalDateTime $value, ?int $startIndex = null, ?int $count = null, ?\PSX\Framework\Tests\Table\SessionHandlerSqlTestColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('date', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByDate(\PSX\DateTime\LocalDateTime $value): ?\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('date', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateByDate(\PSX\DateTime\LocalDateTime $value, \PSX\Framework\Tests\Table\SessionHandlerSqlTestRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('date', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteByDate(\PSX\DateTime\LocalDateTime $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('date', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function create(\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow $record): int
    {
        return $this->doCreate($record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function update(\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow $record): int
    {
        return $this->doUpdate($record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateBy(\PSX\Sql\Condition $condition, \PSX\Framework\Tests\Table\SessionHandlerSqlTestRow $record): int
    {
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function delete(\PSX\Framework\Tests\Table\SessionHandlerSqlTestRow $record): int
    {
        return $this->doDelete($record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteBy(\PSX\Sql\Condition $condition): int
    {
        return $this->doDeleteBy($condition);
    }
    /**
     * @param array<string, mixed> $row
     */
    protected function newRecord(array $row): \PSX\Framework\Tests\Table\SessionHandlerSqlTestRow
    {
        return \PSX\Framework\Tests\Table\SessionHandlerSqlTestRow::from($row);
    }
}