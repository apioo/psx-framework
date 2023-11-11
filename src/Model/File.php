<?php

declare(strict_types = 1);

namespace PSX\Framework\Model;

use PSX\Schema\Attribute\Description;
use PSX\Schema\Attribute\Key;

#[Description('File upload provided through a multipart/form-data post')]
class File implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $name = null;
    protected ?string $type = null;
    protected ?int $size = null;
    #[Key('tmp_name')]
    protected ?string $tmpName = null;
    protected ?int $error = null;
    public function setName(?string $name) : void
    {
        $this->name = $name;
    }
    public function getName() : ?string
    {
        return $this->name;
    }
    public function setType(?string $type) : void
    {
        $this->type = $type;
    }
    public function getType() : ?string
    {
        return $this->type;
    }
    public function setSize(?int $size) : void
    {
        $this->size = $size;
    }
    public function getSize() : ?int
    {
        return $this->size;
    }
    public function setTmpName(?string $tmpName) : void
    {
        $this->tmpName = $tmpName;
    }
    public function getTmpName() : ?string
    {
        return $this->tmpName;
    }
    public function setError(?int $error) : void
    {
        $this->error = $error;
    }
    public function getError() : ?int
    {
        return $this->error;
    }
    public function toRecord() : \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('name', $this->name);
        $record->put('type', $this->type);
        $record->put('size', $this->size);
        $record->put('tmp_name', $this->tmpName);
        $record->put('error', $this->error);
        return $record;
    }
    public function jsonSerialize() : object
    {
        return (object) $this->toRecord()->getAll();
    }
}

