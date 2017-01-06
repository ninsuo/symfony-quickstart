<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
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
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
     */
    private $users;

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
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
