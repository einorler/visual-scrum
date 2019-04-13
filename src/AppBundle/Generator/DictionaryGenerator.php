<?php

namespace AppBundle\Generator;

use AppBundle\Client\PartsOfSpeechClient;
use AppBundle\Entity\Project;
use AppBundle\Entity\UserStory;

class DictionaryGenerator
{
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

    public function getNounsForStory(UserStory $story)
    {
        return $this->client->getNounsFromText($story->getTitle());
    }
}
