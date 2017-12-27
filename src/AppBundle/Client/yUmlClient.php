<?php

namespace AppBundle\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class yUmlClient
{
    const HOST = 'http://yuml.me/diagram/scruffy/';
    const PATH = __DIR__ . '/../../../web/img/diagrams/';

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
     * @throws \Exception
     */
    public function fetchUseCaseDiagram(array $useCases): void
    {
        $query = $this->queryfyUseCaseArray($useCases);
        $uri = self::HOST . 'usecase/' . $query;
        $filename = self::PATH . 'file.png';
//        $response = $this->client->request('GET', self::HOST . 'usecase/' . $query);

        $dirname = dirname($filename);
        if (!is_dir($dirname))
        {
            mkdir($dirname, 0755, true);
        }
        $myFile = fopen($filename, 'w') or die('Problems');
        $request = $this->client->get($uri, ['save_to' => $myFile]);

//        file_put_contents(self::PATH . rand(0, 10000) . '.png', file_get_contents($uri));

        $a = 1;
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
