<?php

namespace PSX\Framework\Tests\Table;

enum SessionHandlerSqlTestColumn : string implements \PSX\Sql\ColumnInterface
{
    case ID = \PSX\Framework\Tests\Table\SessionHandlerSqlTestTable::COLUMN_ID;
    case CONTENT = \PSX\Framework\Tests\Table\SessionHandlerSqlTestTable::COLUMN_CONTENT;
    case DATE = \PSX\Framework\Tests\Table\SessionHandlerSqlTestTable::COLUMN_DATE;
}