<?php

namespace AppBundle\Generator;

class XmlGenerator
{
    private $initial = '<?xml version="1.0" encoding="UTF-8"?><uml:Model xmi:version="2.0" xmlns:xmi="http://www.omg.org/XMI" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:uml="http://www.eclipse.org/uml2/5.0.0/UML" xmi:id="_diagram" name="model"></uml:Model>';

    /**
     * @param array $data The necessary data for UML diagram creation
     *
     * @return string The generated filename
     *
     * @throws \Exception
     */
    public function generateUseCaseXml(array $data): string
    {
        $xml = new \SimpleXMLElement($this->initial);

        foreach ($data as $actor => $useCases) {
            $this->addActorNode($actor, $xml);

            foreach ($useCases as $useCase) {
                $this->addUseCaseNode($useCase, $xml);
            }
        }

        $string = $xml->asXML();
        echo $string;
    }

    /**
     * @param array $data The necessary data for UML diagram creation
     *
     * @return string The generated filename
     *
     * @throws \Exception
     */
    public function generateActivityXml(array $data): string
    {

    }

    /**
     * @param string $actor
     * @param \SimpleXMLElement $xml
     */
    private function addActorNode(string $actor, \SimpleXMLElement $xml): void
    {
        $element = $xml->addChild('packagedElement');
        $element->addAttribute('xsi:type', 'uml:Actor');
        $element->addAttribute('xmi:id', $this->getIdisifiedString($actor));
        $element->addAttribute('name', $actor);

        $annotations = $element->addChild('eAnnotations');
        $annotations->addAttribute('xmi:id', $this->getIdisifiedString($actor) . '_diagram');
        $annotations->addAttribute('source', 'visual-scrum');

        $details = $annotations->addChild('details');
        $details->addAttribute('xmi:id', $this->getIdisifiedString($actor) . '_details');
        $details->addAttribute('key', 'uuid');
        $details->addAttribute('value', uniqid('_'));
    }

    /**
     * @param string $useCase
     * @param \SimpleXMLElement $xml
     */
    private function addUseCaseNode(string $useCase, \SimpleXMLElement $xml): void
    {
        // xsi:type="uml:UseCase" xmi:id="_use_case_1_diagram" name="UseCase"
        $element = $xml->addChild('packagedElement');
        $element->addAttribute('xsi:type', 'uml:UseCase');
        $element->addAttribute('xmi:id', $this->getIdisifiedString($useCase));
        $element->addAttribute('name', $useCase);

        $annotations = $element->addChild('eAnnotations');
        $annotations->addAttribute('xmi:id', $this->getIdisifiedString($useCase) . '_diagram');
        $annotations->addAttribute('source', 'visual-scrum');

        $details = $annotations->addChild('details');
        $details->addAttribute('xmi:id', $this->getIdisifiedString($useCase) . '_details');
        $details->addAttribute('key', 'uuid');
        $details->addAttribute('value', uniqid('_'));
    }

    private function getIdisifiedString(string $string): string
    {
        return str_replace(' ', '_', strtolower($string));
    }
}
