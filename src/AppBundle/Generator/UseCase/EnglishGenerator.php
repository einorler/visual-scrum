<?php

namespace AppBundle\Generator\UseCase;

use AppBundle\Entity\UserStory;

class EnglishGenerator extends AbstractUseCaseGenerator
{
    /**
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        return 'en';
    }

    /**
     * @param UserStory $story
     *
     * @return array
     */
    protected function getUseCase(UserStory $story): array
    {
        $matches = [];
        $useCase = [];
        preg_match('/^As (a|the|an)? ([\w ]*?) I can (.*)(, so that .*)?$/U', $story->getTitle(), $matches);

        $useCase['actor'] = $matches[2];
        $useCase['use_case'] = $matches[3];

        return $useCase;
    }

    /**
     * @param UserStory $story
     *
     * @return bool
     */
    public function isStoryValid(UserStory $story): bool
    {
        if (null === $story->isValid()) {
            $this->validateStory($story);
        }

        return $story->isValid();
    }

    /**
     * @param UserStory $story
     */
    public function validateStory(UserStory $story): void
    {
        if (!preg_match('/^As [\w ]*? I can .*$/', $story->getTitle())) {
            $story->setValid(false);
            $this->em->persist($story);

            return;
        }

        $story->setValid(true);
        $this->em->persist($story);
    }
}
