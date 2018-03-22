<?php

namespace AppBundle\Generator\UseCase;

class UseCaseGenerator
{
    /**
     * @var UseCaseGeneratorInterface[]
     */
    private $generators = [];

    /**
     * @param UseCaseGeneratorInterface $generator
     */
    public function addGenerator(UseCaseGeneratorInterface $generator): void
    {
        $this->generators[$generator->getLocale()] = $generator;
    }

    /**
     * @param string $locale
     *
     * @return null|UseCaseGeneratorInterface
     */
    public function getGenerator(string $locale): ?UseCaseGeneratorInterface
    {
        return $this->generators[$locale] ?? null;
    }
}
