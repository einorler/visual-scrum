<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('app:test');

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $similarNouns = [];
        $nouns = ['car', 'vampire', 'emperor', 'tree', 'soap', 'soad', 'stargazer', 'glastenbury', 'starlazer', 'vamrire', 'vamkirer'];

        foreach ($nouns as $key => $a) {
            for ($i = $key + 1; $i < count($nouns); $i++) {
                $similarity = 0;
                $b = $nouns[$i];
                similar_text($a, $b, $similarity);

                if ($similarity >= 80) {
                    $similarNouns[] = [$a, $b];
                }
            }
        }

        echo var_dump($similarNouns);
    }
}
