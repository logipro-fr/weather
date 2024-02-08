<?php

namespace Weather\Application\ImportLegacy;

use Weather\Application\Presenter\RequestInterface;

class ImportLegacySQLRequest implements RequestInterface
{
    public function __construct(
        private readonly string $db,
        private readonly string $table,
        private readonly ?string $user = null, 
        private readonly ?string $pwd = null, 
    ) {
    }

    public function getDB(): string
    {
        return $this->db;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPwd(): string
    {
        return $this->pwd;
    }
}
