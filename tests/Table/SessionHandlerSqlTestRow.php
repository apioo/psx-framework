<?php

namespace PSX\Framework\Tests\Table;

class SessionHandlerSqlTestRow implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    private ?string $id = null;
    private mixed $content = null;
    private ?\PSX\DateTime\LocalDateTime $date = null;
    public function setId(string $id): void
    {
        $this->id = $id;
    }
    public function getId(): string
    {
        return $this->id ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "id" was provided');
    }
    public function setContent(mixed $content): void
    {
        $this->content = $content;
    }
    public function getContent(): mixed
    {
        return $this->content ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "content" was provided');
    }
    public function setDate(\PSX\DateTime\LocalDateTime $date): void
    {
        $this->date = $date;
    }
    public function getDate(): \PSX\DateTime\LocalDateTime
    {
        return $this->date ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "date" was provided');
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('id', $this->id);
        $record->put('content', $this->content);
        $record->put('date', $this->date);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
    public static function from(array|\ArrayAccess $data): self
    {
        $row = new self();
        $row->id = isset($data['id']) && is_string($data['id']) ? $data['id'] : null;
        $row->content = isset($data['content']) ? $data['content'] : null;
        $row->date = isset($data['date']) && $data['date'] instanceof \DateTimeInterface ? \PSX\DateTime\LocalDateTime::from($data['date']) : null;
        return $row;
    }
}