<?php

namespace AppBundle\Client;

use GuzzleHttp\Client;

class PartsOfSpeechClient
{
    const HOST = 'https://parts-of-speech.info/tagger/tagger';
    // NN - singular noun; NNS - plural noun
    const NOUN_TAGS = ['NN', 'NNS'];

    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getNounsFromText(string $text, string $language = 'en')
    {
        $response = $this->client->get(self::HOST . "?text=$text&language=$language");
        $contents = [];

        preg_match('/^callback\((.*)\);$/', $response->getBody()->getContents(), $contents);

        if (!empty($contents) && isset($contents[1])) {
            $contents = explode(' ', json_decode($contents[1], true)['taggedText']);
        } else {
            return [];
        }

        return $this->extractNounsFromResponse($contents);
    }


    protected function extractNounsFromResponse(array $contents): array
    {
        $nouns = [];
        // Used for when we want to add the adjective before the noun
        // (e.g. 'super admin' in stead of just 'admin'
        $previousWord = '';
        $previousTag = '';

        // filter response
        foreach ($contents as $key => $item) {
            try {
                list($word, $tag) = explode('_', $item);
            } catch (\Exception $e) {
                continue;
            }

            if (in_array($tag, self::NOUN_TAGS)) {
                $nouns[] = $previousTag == 'JJ' ? $previousWord . ' ' . $word : $word;
            }

            $previousTag = $tag;
            $previousWord = $word;
        }

        return $nouns;
    }
}
