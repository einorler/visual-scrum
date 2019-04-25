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

        return $this->fetchFile(self::HOST . 'usecase/' . $query);
    }

    /**
     * @param array $dictionary
     *
     * @return string
     */
    public function fetchClassDiagram(array $dictionary): string
    {
        $query = $this->queryfyDictionary($dictionary);

        return $this->fetchFile(self::HOST . 'class/' . $query);
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

    /**
     * @param array $dictionary
     *
     * @return string
     */
    private function queryfyDictionary(array $dictionary): string
    {
        $query = '';

        foreach ($dictionary as $noun) {
            $query .= sprintf('[%s], ', $noun);
        }

        return substr($query, 0, -2);
    }

    /**
     * @param string $uri
     * @return string
     */
    private function fetchFile(string $uri): string
    {
        $filename = self::PATH . uniqid() . '.png';

        if (!is_dir(self::WEB_DIR . self::PATH)) {
            mkdir(self::WEB_DIR . self::PATH, 0755, true);
        }

        $myFile = fopen(self::WEB_DIR . $filename, 'w');
        $this->client->get($uri, ['save_to' => $myFile]);

        return $filename;
    }
}
