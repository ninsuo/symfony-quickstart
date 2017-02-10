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
    protected $users;

    /**
     * @ORM\ManyToMany(targetEntity="Group", mappedBy="permissions")
     */
    protected $groups;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->users  = new ArrayCollection();
        $this->groups = new ArrayCollection();
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
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add user.
     *
     * @param User $user
     *
     * @return Permission
     */
    public function addUser(User $user)
    {
        if ($this->users->contains($user)) {
            return;
        }

        $this->users->add($user);
        $user->addPermission($this);

        return $this;
    }

    /**
     * Remove user.
     *
     * @param User $user
     *
     * @return Permission
     */
    public function removeUser(User $user)
    {
        if (!$this->users->contains($user)) {
            return;
        }

        $this->users->removeElement($user);
        $user->removePermission($this);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add group.
     *
     * @param Group $group
     *
     * @return Permission
     */
    public function addGroup(Group $group)
    {
        if ($this->groups->contains($group)) {
            return;
        }

        $this->groups->add($group);
        $group->addPermission($this);

        return $this;
    }

    /**
     * Remove group.
     *
     * @param Group $group
     *
     * @return Permission
     */
    public function removeGroup(Group $group)
    {
        if (!$this->groups->contains($group)) {
            return;
        }

        $this->groups->removeElement($group);
        $group->removePermission($this);

        return $this;
    }
}
