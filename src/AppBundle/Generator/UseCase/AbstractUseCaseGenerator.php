<?php

namespace AppBundle\Generator\UseCase;

use AppBundle\Entity\UserStory;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractUseCaseGenerator implements UseCaseGeneratorInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UserStory[]|Collection $userStories
     *
     * @return array
     */
    public function generateUseCases($userStories): array
    {
        $useCases = [];

        foreach ($userStories as $story) {
            if (!$this->isStoryValid($story)) {
                continue;
            }

            $useCase = $this->getUseCase($story);
            $useCases[$useCase['actor']][] = $useCase['use_case'];
        }

        $this->em->flush();

        return $useCases;
    }

    /**
     * @param UserStory $story
     *
     * @return array
     */
    abstract protected function getUseCase(UserStory $story): array;

    /**
     * @param UserStory $story
     *
     * @return bool
     */
    abstract protected function isStoryValid(UserStory $story): bool;
}
