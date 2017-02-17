<?php

namespace BaseBundle\Provider;

use BaseBundle\Api\IncompleteProfileInterface;

class IncompleteProfileProvider
{
    protected $incompleteProfileServices = [];

    public function addIncompleteProfileService(IncompleteProfileInterface $incompleteProfileService)
    {
        $this->incompleteProfileServices[] = $incompleteProfileService;
    }

    public function getIncompleteProfileServices()
    {
        return $this->incompleteProfileServices;
    }
}
