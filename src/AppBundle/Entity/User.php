<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ArrayCollection|Project[]
     *
     * @ORM\OneToMany(targetEntity="Project", mappedBy="user")
     */
    private $projects;

    /**
     * @var Configuration
     *
     * @ORM\OneToOne(targetEntity="Configuration")
     * @ORM\JoinColumn(name="config_id", referencedColumnName="id")
     */
    private $configuration;

    public function __construct()
    {
        parent::__construct();

        $this->projects = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Project[]|Collection
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    /**
     * @param Project $project
     */
    public function addProject(Project $project)
    {
        $this->projects->add($project);
        $project->setUser($this);
    }

    /**
     * @param Project $project
     */
    public function removeProject(Project $project)
    {
        if (!$this->projects->contains($project)) {
            $this->projects->remove($project);
            $project->setUser(null);
        }
    }

    /**
     * @return Configuration
     */
    public function getConfiguration(): ?Configuration
    {
        return $this->configuration;
    }

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }
}
