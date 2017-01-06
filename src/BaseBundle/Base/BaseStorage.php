<?php

namespace BaseBundle\Base;

use BaseBundle\Base\BaseService;
use Doctrine\DBAL\Connection;

class BaseStorage extends BaseService
{
    protected $connection;

    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}