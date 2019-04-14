<?php

namespace AppBundle\Generator\UseCase;

use AppBundle\Entity\UserStory;

class LithuanianGenerator extends AbstractUseCaseGenerator
{
    /**
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        return 'lt';
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
        preg_match('/^Kaip (.*) a(s|š) turiu gal(e|ė)ti (.*)$/U', $story->getTitle(), $matches);

        // TODO: remove the call to `removeNonEnglishLetters` as soon as an issue with guzzle is resolved
        $useCase['actor'] = $this->removeNonEnglishLetters($matches[1]);
        $useCase['use_case'] = $this->removeNonEnglishLetters($matches[4]);

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
        if (!preg_match('/^Kaip .* as|š turiu gale|ėti .*$/', $story->getTitle())) {
            $story->setValid(false);
            $this->em->persist($story);

            return;
        }

        $story->setValid(true);
        $this->em->persist($story);
    }

    /**
     * This functionality is temporary and only here because of a potential bug in guzzle
     * which does not encode the uri normally when lithuanian letters are present. As such
     * the request to yUml fails.
     *
     * @param string $phrase
     *
     * @return string
     */
    protected function removeNonEnglishLetters(string $phrase)
    {
        $search = ['Ą', 'ą', 'Č', 'č', 'Ę', 'ę', 'Ė', 'ė', 'Į', 'į', 'Š', 'š', 'Ų', 'ų', 'Ū', 'ū', 'Ž', 'ž'];
        $replace = ['A', 'a', 'C', 'c', 'E', 'e', 'E', 'e', 'I', 'i', 'S', 's', 'U', 'u', 'U', 'u', 'Z', 'z'];

        return str_replace($search, $replace, $phrase);
    }
}
