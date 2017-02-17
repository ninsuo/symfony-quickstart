<?php

namespace BaseBundle\Api;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface for services tagged base.incomplete_profile
 *
 * If users should fill some part of their profile (such as email;
 * as it is not gathered from Twitter), create a service with
 * the above tag implementing this interface.
 */
interface IncompleteProfileInterface
{
    /**
     * Returns true when user profile is considered complete.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isProfileComplete(UserInterface $user);

    /**
     * Returns profile completion route.
     *
     * @return string
     */
    public function getRoute();
}
