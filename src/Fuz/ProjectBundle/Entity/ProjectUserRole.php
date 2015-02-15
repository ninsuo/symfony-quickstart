<?php

namespace Fuz\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectUserRole
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ProjectUserRole
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="project", type="integer")
     */
    private $project;

    /**
     * @var integer
     *
     * @ORM\Column(name="user", type="integer")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="role", type="integer")
     */
    private $role;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDatetime", type="datetime")
     */
    private $creationDatetime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updateDatetiem", type="datetime")
     */
    private $updateDatetiem;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set project
     *
     * @param integer $project
     * @return ProjectUserRole
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return integer 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set user
     *
     * @param integer $user
     * @return ProjectUserRole
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return integer 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set role
     *
     * @param integer $role
     * @return ProjectUserRole
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return integer 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set creationDatetime
     *
     * @param \DateTime $creationDatetime
     * @return ProjectUserRole
     */
    public function setCreationDatetime($creationDatetime)
    {
        $this->creationDatetime = $creationDatetime;

        return $this;
    }

    /**
     * Get creationDatetime
     *
     * @return \DateTime 
     */
    public function getCreationDatetime()
    {
        return $this->creationDatetime;
    }

    /**
     * Set updateDatetiem
     *
     * @param \DateTime $updateDatetiem
     * @return ProjectUserRole
     */
    public function setUpdateDatetiem($updateDatetiem)
    {
        $this->updateDatetiem = $updateDatetiem;

        return $this;
    }

    /**
     * Get updateDatetiem
     *
     * @return \DateTime 
     */
    public function getUpdateDatetiem()
    {
        return $this->updateDatetiem;
    }
}
