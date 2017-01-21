<?php

namespace AdminBundle\Storage;

use BaseBundle\Base\BaseStorage;

class GroupStorage extends BaseStorage
{
    public function listGroups()
    {
        $rows = $this->connection->fetchAll('
            SELECT id, name
            FROM Groups
        ');

        $groups = [];
        foreach ($rows as $row) {
            $groups[$row['id']] = $row['name'];
        }

        return $groups;
    }
}
