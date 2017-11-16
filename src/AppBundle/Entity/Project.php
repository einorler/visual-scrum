<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="project")
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="projects")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var ArrayCollection|Project[]
     *
     * @ORM\OneToMany(targetEntity="UserStory", mappedBy="project", cascade={"persist", "remove"})
     */
    private $userStories;

    /**
     * @var ArrayCollection|UseCaseDiagram[]
     *
     * @ORM\OneToMany(targetEntity="UseCaseDiagram", mappedBy="project")
     */
    private $useCaseDiagrams;

    /**
     * @var ArrayCollection|ActivityDiagram[]
     *
     * @ORM\OneToMany(targetEntity="ActivityDiagram", mappedBy="project")
     */
    private $activityDiagrams;

    public function __construct()
    {
        $this->userStories = new ArrayCollection();
        $this->useCaseDiagrams = new ArrayCollection();
        $this->activityDiagrams = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(?User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Project[]|ArrayCollection
     */
    public function getUserStories()
    {
        return $this->userStories;
    }

    /**
     * @param UserStory $userStory
     */
    public function addUserStory(UserStory $userStory)
    {
        $this->userStories->add($userStory);

        $userStory->setProject($this);
    }

    /**
     * @param UserStory $userStory
     */
    public function removeUserStory(UserStory $userStory)
    {
        if ($this->userStories->contains($userStory)) {
            $this->userStories->remove($userStory);
            $userStory->setProject(null);
        }
    }

    /**
     * @return UseCaseDiagram[]|ArrayCollection
     */
    public function getUseCaseDiagrams()
    {
        return $this->useCaseDiagrams;
    }

    /**
     * @param UseCaseDiagram $useCaceDiagram
     */
    public function addUseCaseDiagram(UseCaseDiagram $useCaceDiagram)
    {
        $this->userStories->add($useCaceDiagram);
    }

    /**
     * @param UseCaseDiagram $useCaceDiagram
     */
    public function removeUseCaseDiagram(UseCaseDiagram $useCaceDiagram)
    {
        if ($this->userStories->contains($useCaceDiagram)) {
            $this->userStories->remove($useCaceDiagram);
        }
    }

    /**
     * @return ActivityDiagram[]|ArrayCollection
     */
    public function getActivityDiagrams()
    {
        return $this->activityDiagrams;
    }

    /**
     * @param ActivityDiagram $useCaceDiagram
     */
    public function addActivityDiagram(ActivityDiagram $useCaceDiagram)
    {
        $this->userStories->add($useCaceDiagram);
    }

    /**
     * @param ActivityDiagram $useCaceDiagram
     */
    public function removeActivityDiagram(ActivityDiagram $useCaceDiagram)
    {
        if ($this->userStories->contains($useCaceDiagram)) {
            $this->userStories->remove($useCaceDiagram);
        }
    }
}
