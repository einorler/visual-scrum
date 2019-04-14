<?php

namespace AppBundle\Generator;

use AppBundle\Client\PartsOfSpeechClient;
use AppBundle\Entity\Project;
use AppBundle\Entity\UserStory;

class DictionaryGenerator
{
    const PERCENT_FOR_SIMILARITY_CHECK = 80;

    /**
     * @var PartsOfSpeechClient
     */
    private $client;

    /**
     * @param PartsOfSpeechClient $client
     */
    public function __construct(PartsOfSpeechClient $client)
    {
        $this->client = $client;
    }

    public function generateDictionary(Project $project): void
    {
        $project->setDictionary([]);

        foreach ($project->getUserStories() as $userStory) {
            foreach ($this->getNounsForStory($userStory) as $noun) {
                $project->addToDictionary($userStory->getId(), $noun);
            }
        }
    }

    /**
     * Retrieves nouns whose similarity is >= 80%
     * Response: [ 'books' => 'boks', 'stargazer' => 'stargafer', ... ]
     *
     * @param Project $project
     *
     * @return array
     */
    public function getSimilarNouns(Project $project): array
    {
        $similarNouns = [];
        $nouns = $project->getDictionaryNouns();

        foreach ($nouns as $key => $a) {
            for ($i = $key + 1; $i < count($nouns); $i++) {
                $similarity = 0;
                $b = $nouns[$i];
                similar_text($a, $b, $similarity);

                if ($similarity >= self::PERCENT_FOR_SIMILARITY_CHECK) {
                    $similarNouns[] = [$a, $b];
                }
            }
        }

        return $similarNouns;
    }

    /**
     * @param Project $project
     * @param string $nounToKeep
     * @param string $replaceable
     *
     * @throws \Exception
     */
    public function mergeNouns(Project $project, string $nounToKeep, string $replaceable): void
    {
        $dictionary = $project->getDictionary();

        if (!isset($dictionary[$nounToKeep]) || !isset($dictionary[$replaceable])) {
            throw new \Exception('Nouns need to exist in project');
        }

        $nounToKeepStories = $dictionary[$nounToKeep];
        $replaceableStories = $dictionary[$replaceable];

        foreach ($project->getUserStories() as $story) {
            if (in_array($story->getId(), $replaceableStories)) {
                $story->setTitle(str_replace($replaceable, $nounToKeep, $story->getTitle()));
            }
        }

        $dictionary[$nounToKeep] = array_unique(array_merge($nounToKeepStories, $replaceableStories));
        unset($dictionary[$replaceable]);
        $project->setDictionary($dictionary);
    }

    public function getNounsForStory(UserStory $story)
    {
        return $this->client->getNounsFromText($story->getTitle());
    }
}
