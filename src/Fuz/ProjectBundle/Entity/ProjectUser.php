<?php

namespace Fuz\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectUser
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ProjectUser
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
     * @var \DateTime
     *
     * @ORM\Column(name="creationDatetime", type="datetime")
     */
    private $creationDatetime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updateDatetime", type="datetime")
     */
    private $updateDatetime;

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
     * @return ProjectUser
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
     * @return ProjectUser
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
     * Set creationDatetime
     *
     * @param \DateTime $creationDatetime
     * @return ProjectUser
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
     * Set updateDatetime
     *
     * @param \DateTime $updateDatetime
     * @return ProjectUser
     */
    public function setUpdateDatetime($updateDatetime)
    {
        $this->updateDatetime = $updateDatetime;

        return $this;
    }

    /**
     * Get updateDatetime
     *
     * @return \DateTime
     */
    public function getUpdateDatetime()
    {
        return $this->updateDatetime;
    }

}
