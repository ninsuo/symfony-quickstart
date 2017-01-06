<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Group.
 *
 * @ORM\Table(
 *     name="group",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="group_name_idx", columns={"name"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="BaseBundle\Repository\GroupRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
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
     */
    protected $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Permission", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="group_permission",
     *     joinColumns={
     *         @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     *     }
     * )
     */
    protected $permissions;

    public function __construct()
    {
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
     * @return User
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
     * Get permissions.
     *
     * @return ArrayCollection
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
}
