<?php

namespace BaseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Permission.
 *
 * @ORM\Table(name="permission")
 * @ORM\Entity(repositoryClass="BaseBundle\Repository\PermissionRepository")
 * @UniqueEntity("name")
 */
class Permission
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, unique=true)
     * @Assert\NotBlank
     * @Assert\Regex("/^USER$/i", match=false)
     * @Assert\Regex("/^ADMIN$/i", match=false)
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="permissions")
     */
    protected $grantedUsers;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="deniedPermissions")
     */
    protected $deniedUsers;

    /**
     * @ORM\ManyToMany(targetEntity="Group", mappedBy="permissions")
     */
    protected $grantedGroups;

    /**
     * @ORM\ManyToMany(targetEntity="Group", mappedBy="deniedPermissions")
     */
    protected $deniedGroups;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->grantedUsers  = new ArrayCollection();
        $this->deniedUsers   = new ArrayCollection();
        $this->grantedGroups = new ArrayCollection();
        $this->deniedGroups  = new Arraycollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Permission
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection
     */
    public function getGrantedUsers()
    {
        return $this->grantedUsers;
    }

    /**
     * Add grantedUser.
     *
     * @param User $grantedUser
     *
     * @return Permission
     */
    public function addGrantedUser(User $grantedUser)
    {
        if ($this->grantedUsers->contains($grantedUser)) {
            return;
        }

        $this->grantedUsers->add($grantedUser);
        $grantedUser->addPermission($this);

        return $this;
    }

    /**
     * Remove grantedUser.
     *
     * @param User $grantedUser
     *
     * @return Permission
     */
    public function removeGrantedUser(User $grantedUser)
    {
        if (!$this->grantedUsers->contains($grantedUser)) {
            return;
        }

        $this->grantedUsers->removeElement($grantedUser);
        $grantedUser->removePermission($this);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDeniedUsers()
    {
        return $this->deniedUsers;
    }

    /**
     * Add deniedUser.
     *
     * @param User $deniedUser
     *
     * @return Permission
     */
    public function addDeniedUser(User $deniedUser)
    {
        if ($this->deniedUsers->contains($deniedUser)) {
            return;
        }

        $this->deniedUsers->add($deniedUser);
        $deniedUser->addDeniedPermission($this);

        return $this;
    }

    /**
     * Remove deniedUser.
     *
     * @param User $deniedUser
     *
     * @return Permission
     */
    public function removeDeniedUser(User $deniedUser)
    {
        if (!$this->deniedUsers->contains($deniedUser)) {
            return;
        }

        $this->deniedUsers->removeElement($deniedUser);
        $deniedUser->removeDeniedPermission($this);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGrantedGroups()
    {
        return $this->grantedGroups;
    }

    /**
     * Add grantedGroup.
     *
     * @param Group $grantedGroup
     *
     * @return Permission
     */
    public function addGrantedGroup(Group $grantedGroup)
    {
        if ($this->grantedGroups->contains($grantedGroup)) {
            return;
        }

        $this->grantedGroups->add($grantedGroup);
        $grantedGroup->addPermission($this);

        return $this;
    }

    /**
     * Remove grantedGroup.
     *
     * @param Group $grantedGroup
     *
     * @return Permission
     */
    public function removeGrantedGroup(Group $grantedGroup)
    {
        if (!$this->grantedGroups->contains($grantedGroup)) {
            return;
        }

        $this->grantedGroups->removeElement($grantedGroup);
        $grantedGroup->removePermission($this);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDeniedGroups()
    {
        return $this->deniedGroups;
    }

    /**
     * Add deniedGroup.
     *
     * @param Group $deniedGroup
     *
     * @return Permission
     */
    public function addDeniedGroup(Group $deniedGroup)
    {
        if ($this->deniedGroups->contains($deniedGroup)) {
            return;
        }

        $this->deniedGroups->add($deniedGroup);
        $deniedGroup->addDeniedPermission($this);

        return $this;
    }

    /**
     * Remove deniedGroup.
     *
     * @param Group $deniedGroup
     *
     * @return Permission
     */
    public function removeDeniedGroup(Group $deniedGroup)
    {
        if (!$this->deniedGroups->contains($deniedGroup)) {
            return;
        }

        $this->deniedGroups->removeElement($deniedGroup);
        $deniedGroup->removeDeniedPermission($this);

        return $this;
    }
}
