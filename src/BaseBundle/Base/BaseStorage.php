<?php

namespace BaseBundle\Base;

use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class BaseStorage extends BaseService
{
    use ContainerAwareTrait;

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
