<?php

namespace AdminBundle\Storage;

use BaseBundle\Base\BaseStorage;

class UserStorage extends BaseStorage
{
    public function getUsers()
    {
        return $this->connection->fetchAll("
            SELECT id, nickname, contact, is_admin
            FROM user
            ORDER BY contact ASC
        ");
    }

    public function toggleAdmin($userId)
    {
        $this->connection->executeQuery("
            UPDATE user
            SET is_admin = 1 - is_admin
            WHERE id = :user_id
        ", [
            'user_id' => $userId,
        ]);
    }
}