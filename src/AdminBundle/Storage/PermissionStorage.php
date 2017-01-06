<?php

namespace AdminBundle\Storage;

use BaseBundle\Base\BaseStorage;

class PermissionStorage extends BaseStorage
{
    public function deletePermission($id)
    {
        $this->connection->delete('permission', [
            'id' => $id,
        ]);
    }
}
