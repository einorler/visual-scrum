<?php

namespace Tests\AppBundle\Unit\Generator;

use AppBundle\Generator\XmlGenerator;
use PHPUnit\Framework\TestCase;

class XmlGeneratorTest extends TestCase
{
    public function testXmlGeneration()
    {
        $generator = new XmlGenerator();
        $useCases = ['A' => ['atlikti B']];
        $result = $generator->generateUseCaseXml($useCases);
    }
}
