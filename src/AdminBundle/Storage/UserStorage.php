<?php

namespace AdminBundle\Storage;

use BaseBundle\Base\BaseStorage;

class UserStorage extends BaseStorage
{
    public function toggleAdmin($id)
    {
        $this->connection->executeQuery("
            UPDATE user
            SET is_admin = 1 - is_admin
            WHERE id = :user_id
        ", [
            'user_id' => $id,
        ]);
    }
}