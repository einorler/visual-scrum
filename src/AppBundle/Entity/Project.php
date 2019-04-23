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
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $distId;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $version = 1;

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

    /**
     * A collection of all the nouns in the project
     *
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    private $dictionary = [];

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
     * @return string
     */
    public function getDistId()
    {
        return $this->distId;
    }

    /**
     * @param string $distId
     */
    public function setDistId($distId)
    {
        $this->distId = $distId;
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
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion(int $version)
    {
        $this->version = $version;
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
     * @return UserStory[]|ArrayCollection
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
     * If there is at least a single changed story
     * we consider the project itself as changed
     *
     * @return bool
     */
    public function isChanged(): bool
    {
        foreach ($this->userStories as $story) {
            if ($story->isChanged()) {
                return true;
            }
        }

        return false;
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

    /**
     * @param string $title
     *
     * @return UserStory
     */
    public function getUserStoryByTitle(string $title): ?UserStory
    {
        foreach ($this->userStories as $story) {
            if ($story->getTitle() == $title) {
                return $story;
            }
        }

        return null;
    }

    /**
     * @param string $identifier
     *
     * @return UserStory
     */
    public function getUserStoryByIdentifier(string $identifier): ?UserStory
    {
        foreach ($this->userStories as $story) {
            if ($story->getDistId() == $identifier) {
                return $story;
            }
        }

        return null;
    }

    /**
     * Returns [ noun_1 => [ story_id_1, story_id_2, ... ], noun_2 => [ ... ], ... ]
     *
     * @return array
     */
    public function getDictionary(): array
    {
        return $this->dictionary;
    }

    /**
     * Returns just [ noun_1, noun_2, ... ]
     *
     * @return array
     */
    public function getDictionaryNouns(): array
    {
        return array_keys($this->dictionary);
    }

    /**
     * Returns nouns for a specific story
     *
     * @param UserStory $story
     *
     * @return array
     */
    public function getDictionaryForStory(UserStory $story): array
    {
        $nouns = [];

        foreach ($this->dictionary as $noun => $storyIds) {
            if (in_array($story->getId(), $storyIds)) {
                $nouns[] = $noun;
            }
        }

        return $nouns;
    }

    /**
     * @param string $noun
     */
    public function removeDictionaryNoun(string $noun): void
    {
        unset($this->dictionary[$noun]);
    }

    /**
     * @param array $dictionary
     */
    public function setDictionary(array $dictionary)
    {
        $this->dictionary = $dictionary;
    }

    public function addToDictionary(int $stroyId, string $noun): void
    {
        $this->dictionary[$noun][] = $stroyId;
    }
}
