<?php

namespace BaseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User.
 *
 * @ORM\Table(
 *     name="users",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="resource_owner_idx", columns={"resource_owner", "resource_owner_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="BaseBundle\Repository\UserRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, EquatableInterface
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
     * @ORM\Column(name="resource_owner", type="string", length=16)
     */
    protected $resourceOwner;

    /**
     * @var string
     *
     * @ORM\Column(name="resource_owner_id", type="string", length=255)
     */
    protected $resourceOwnerId;

    /**
     * @var string
     *
     * @ORM\Column(name="nickname", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $nickname;

    /**
     * @var string
     *
     * @ORM\Column(name="contact", type="string", length=255, nullable=true)
     * @Assert\Email()
     */
    protected $contact;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    protected $picture;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_seen", type="datetime")
     */
    protected $lastSeen;

    /**
     * @var int
     *
     * @ORM\Column(name="signin_count", type="integer")
     */
    protected $signinCount = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_enabled", type="boolean")
     */
    protected $isEnabled = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_admin", type="boolean")
     */
    protected $isAdmin = false;

    /**
     * @var array
     */
    protected $roles = ['ROLE_USER'];

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Group", cascade={"persist", "remove"}, inversedBy="users")
     * @ORM\JoinTable(name="users_groups",
     *     joinColumns={
     *         @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     *     }
     * )
     */
    protected $groups;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    /**
     * Get resourceOwner.
     *
     * @return string
     */
    public function getResourceOwner()
    {
        return $this->resourceOwner;
    }

    /**
     * Set resourceOwner.
     *
     * @param string $resourceOwner
     *
     * @return User
     */
    public function setResourceOwner($resourceOwner)
    {
        $this->resourceOwner = $resourceOwner;

        return $this;
    }

    /**
     * Get resourceOwnerId.
     *
     * @return string
     */
    public function getResourceOwnerId()
    {
        return $this->resourceOwnerId;
    }

    /**
     * Set resourceOwnerId.
     *
     * @param string $resourceOwnerId
     *
     * @return User
     */
    public function setResourceOwnerId($resourceOwnerId)
    {
        $this->resourceOwnerId = $resourceOwnerId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return json_encode([$this->resourceOwner, $this->resourceOwnerId]);
    }

    /**
     * Get nickname.
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set nickname.
     *
     * @param string $nickname
     *
     * @return User
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get contact.
     *
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set contact.
     *
     * @param string $contact
     *
     * @return User
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get picture.
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set picture.
     *
     * @param string $picture
     *
     * @return User
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get lastSeen.
     *
     * @return \DateTime
     */
    public function getLastSeen()
    {
        return $this->lastSeen;
    }

    /**
     * Set lastSeen.
     *
     * @param \DateTime $lastSeen
     *
     * @return User
     */
    public function setLastSeen($lastSeen)
    {
        $this->lastSeen = $lastSeen;

        return $this;
    }

    /**
     * Get signinCount.
     *
     * @return int
     */
    public function getSigninCount()
    {
        return $this->signinCount;
    }

    /**
     * Set signinCount.
     *
     * @param int $signinCount
     *
     * @return User
     */
    public function setSigninCount($signinCount)
    {
        $this->signinCount = $signinCount;

        return $this;
    }

    /**
     * Get isEnabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set isEnabled.
     *
     * @param bool $isEnabled
     *
     * @return User
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get isAdmin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * Set isAdmin.
     *
     * @param bool $isAdmin
     *
     * @return User
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * Get groups.
     *
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
     * @return User
     */
    public function addGroup(Group $group)
    {
        if ($this->groups->contains($group)) {
            return;
        }

        $this->groups->add($group);
        $group->addUser($this);

        return $this;
    }

    /**
     * Remove group.
     *
     * @param Group $group
     *
     * @return User
     */
    public function removeGroup(Group $group)
    {
        if (!$this->groups->contains($group)) {
            return;
        }

        $this->groups->removeElement($group);
        $group->removeUser($this);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param string $role
     *
     * @return User
     */
    public function addRole($role)
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param string $role
     *
     * @return User
     */
    public function removeRole($role)
    {
        if (in_array($role, $this->roles)) {
            unset($this->roles[array_search($role, $this->roles)]);
        }

        return $this;
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setLastSeen(new \DateTime());
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setLastSeen(new \DateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(UserInterface $user)
    {
        if ((int)$this->getId() === $user->getId()) {
            return true;
        }

        return false;
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
}
