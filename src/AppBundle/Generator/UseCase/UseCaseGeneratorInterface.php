<?php

namespace AppBundle\Generator\UseCase;

use AppBundle\Entity\UserStory;
use Doctrine\Common\Collections\Collection;

interface UseCaseGeneratorInterface
{
    /**
     * @param UserStory[]|Collection $userStories
     *
     * @return array
     */
    public function generateUseCases($userStories): array;

    /**
     * @return string
     */
    public function getLocale(): string;

    /**
     * @param UserStory $story
     *
     * @return bool
     */
    public function isStoryValid(UserStory $story): bool;

    /**
     * @param UserStory $story
     */
    public function validateStory(UserStory $story): void;
}
