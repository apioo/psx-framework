<?php

namespace PSX\Framework\Tests\Table;

enum HandlerCommentColumn : string implements \PSX\Sql\ColumnInterface
{
    case ID = \PSX\Framework\Tests\Table\HandlerCommentTable::COLUMN_ID;
    case USERID = \PSX\Framework\Tests\Table\HandlerCommentTable::COLUMN_USERID;
    case TITLE = \PSX\Framework\Tests\Table\HandlerCommentTable::COLUMN_TITLE;
    case DATE = \PSX\Framework\Tests\Table\HandlerCommentTable::COLUMN_DATE;
}