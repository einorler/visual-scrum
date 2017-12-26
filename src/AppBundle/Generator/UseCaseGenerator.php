<?php

namespace AppBundle\Generator;

use AppBundle\Entity\UserStory;
use Doctrine\ORM\EntityManagerInterface;

class UseCaseGenerator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UserStory[] $userStories
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


        }

        $this->em->flush();

        return $useCases;
    }

    private function isStoryValid(UserStory $story): bool
    {
        if (null === $story->isValid()) {
            $this->validateStory($story);
        }

        return $story->isValid();
    }

    /**
     * @param UserStory $story
     */
    private function validateStory(UserStory $story): void
    {
        if (!preg_match('/^Kaip \w* a. turiu gal.ti \w*$/?', $story->getTitle())) {
            $story->setValid(false);
            $this->em->persist($story);

            return;
        }

        $story->setValid(true);
        $this->em->persist($story);
    }
}
