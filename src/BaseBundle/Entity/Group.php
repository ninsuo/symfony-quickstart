<?php

namespace BaseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Group.
 *
 * @ORM\Table(
 *     name="groups",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="group_name_idx", columns={"name"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="BaseBundle\Repository\GroupRepository")
 * @UniqueEntity("name")
 */
class Group
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    protected $notes;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
     */
    protected $users;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Permission", cascade={"persist", "remove"}, inversedBy="groups")
     * @ORM\JoinTable(name="groups_permissions",
     *     joinColumns={
     *         @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="permission_id", referencedColumnName="id", onDelete="CASCADE")
     *     }
     * )
     */
    protected $permissions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->permissions = new ArrayCollection();
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
     * @return Group
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
     * Set notes.
     *
     * @param string $notes
     *
     * @return Group
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes.
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Get users.
     *
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
     * @return Group
     */
    public function addUser(User $user)
    {
        if ($this->users->contains($user)) {
            return ;
        }

        $this->users->add($user);
        $user->addGroup($this);

        return $this;
    }

    /**
     * Remove user.
     *
     * @param User $user
     * @return Group
     */
    public function removeUser(User $user)
    {
        if (!$this->users->contains($user)) {
            return ;
        }

        $this->users->removeElement($user);
        $user->removeGroup($this);

        return $this;
    }

    /**
     * Get permissions.
     *
     * @return ArrayCollection
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Add permission.
     *
     * @param Permission $permission
     * @return Group
     */
    public function addPermission(Permission $permission)
    {
        if ($this->permissions->contains($permission)) {
            return ;
        }

        $this->permissions->add($permission);
        $permission->addGroup($this);

        return $this;
    }

    /**
     * Remove permission.
     *
     * @param Permission $permission
     * @return Group
     */
    public function removePermission(Permission $permission)
    {
        if (!$this->permissions->contains($permission)) {
            return ;
        }

        $this->permissions->removeElement($permission);
        $permission->removeGroup($this);

        return $this;
    }
}
