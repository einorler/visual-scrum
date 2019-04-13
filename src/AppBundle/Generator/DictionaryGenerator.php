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

    public function getNounsForStory(UserStory $story)
    {
        return $this->client->getNounsFromText($story->getTitle());
    }
}
