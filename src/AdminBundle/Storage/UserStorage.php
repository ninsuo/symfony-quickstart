<?php

namespace AdminBundle\Storage;

use BaseBundle\Base\BaseStorage;

class UserStorage extends BaseStorage
{
    public function toggleAdmin($id)
    {
        $this->connection->executeQuery('
            UPDATE users
            SET is_admin = 1 - is_admin
            WHERE id = :user_id
        ', [
            'user_id' => $id,
        ]);
    }

    public function toggleFrozen($id)
    {
        $this->connection->executeQuery('
            UPDATE users
            SET is_frozen = 1 - is_frozen
            WHERE id = :user_id
        ', [
            'user_id' => $id,
        ]);
    }
}
