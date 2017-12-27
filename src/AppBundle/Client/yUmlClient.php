<?php

namespace AppBundle\Client;

use GuzzleHttp\Client;

class yUmlClient
{
    const HOST = 'http://yuml.me/diagram/scruffy/';
    const WEB_DIR = __DIR__ . '/../../../web';
    const PATH = '/img/diagrams/';

    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param array $useCases
     *
     * @return string
     *
     * @throws \Exception
     */
    public function fetchUseCaseDiagram(array $useCases): string
    {
        $query = $this->queryfyUseCaseArray($useCases);

        $uri = self::HOST . 'usecase/' . $query;
        $filename = self::PATH . uniqid() . '.png';

        if (!is_dir(self::WEB_DIR . self::PATH)) {
            mkdir(self::WEB_DIR . self::PATH, 0755, true);
        }

        $myFile = fopen(self::WEB_DIR . $filename, 'w');
        $this->client->get($uri, ['save_to' => $myFile]);

        return $filename;
    }

    /**
     * @param array $useCases
     *
     * @return string
     */
    private function queryfyUseCaseArray(array $useCases): string
    {
        $query = '';

        foreach ($useCases as $actor => $cases) {
            foreach ($cases as $useCase) {
                $query .= sprintf('[%s]-(%s), ', $actor, $useCase);
            }
        }

        return substr($query, 0, -2);
    }
}
