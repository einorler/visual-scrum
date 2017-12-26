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
    private function getUseCase(UserStory $story): array
    {
        $matches = [];
        $useCase = [];
        preg_match('/^Kaip (.*) a(s|š) turiu gal(e|ė)ti (.*)$/U', $story->getTitle(), $matches);
        $useCase['actor'] = $matches[1];
        $useCase['use_case'] = $matches[4];

        return $useCase;
    }

    /**
     * @param UserStory $story
     *
     * @return bool
     */
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
        if (!preg_match('/^Kaip .* as|š turiu gale|ėti .*$/', $story->getTitle())) {
            $story->setValid(false);
            $this->em->persist($story);

            return;
        }

        $story->setValid(true);
        $this->em->persist($story);
    }
}
