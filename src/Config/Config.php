<?php

namespace Krlove\EloquentModelGenerator\Config;

class Config
{
    private ?string $className = null;
    private ?string $tableName = null;
    private ?string $namespace = null;
    private ?string $baseClassName = null;
    private ?bool $noTimestamps = null;
    private ?string $dateFormat = null;
    private ?string $connection = null;

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function setClassName(?string $className): self
    {
        $this->className = $className;

        return $this;
    }

    public function getTableName(): ?string
    {
        return $this->tableName;
    }

    public function setTableName(?string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    public function setNamespace(?string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function getBaseClassName(): ?string
    {
        return $this->baseClassName;
    }

    public function setBaseClassName(?string $baseClassName): self
    {
        $this->baseClassName = $baseClassName;

        return $this;
    }

    public function getNoTimestamps(): ?bool
    {
        return $this->noTimestamps;
    }

    public function setNoTimestamps(?bool $noTimestamps): self
    {
        $this->noTimestamps = $noTimestamps;

        return $this;
    }

    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }

    public function setDateFormat(?string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    public function getConnection(): ?string
    {
        return $this->connection;
    }

    public function setConnection(?string $connection): self
    {
        $this->connection = $connection;

        return $this;
    }
}
