<?php

namespace AppBundle\Generator;

class XmlGenerator
{
    private $initial = '<?xml version="1.0" encoding="UTF-8"?><uml:Model xmi:version="2.0" xmlns:xmi="http://www.omg.org/XMI" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:uml="http://www.eclipse.org/uml2/5.0.0/UML" xmi:id="_diagram" name="model">';

    /**
     * @param array $data The necessary data for UML diagram creation
     *
     * @return string The generated filename
     *
     * @throws \Exception
     */
    public function generateUseCaseXml(array $data): string
    {
        $xml = $this->initial;

        foreach ($data as $actor => $useCases) {
            $this->addActorNode($actor, $xml);

            foreach ($useCases as $useCase) {
                $this->addUseCaseNode($useCase, $xml);
                $this->addAssociationNode($useCase, $actor, $xml);
            }
        }

        $xml .= '</uml:Model>';
        echo $xml;
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
     * @param string $xml
     */
    private function addActorNode(string $actor, string &$xml): void
    {
        $xml .= sprintf(
            '<packagedElement xsi:type="uml:Actor" xmi:id="%s" name="%s">' . PHP_EOL,
            $this->getIdisifiedString($actor),
            $actor
            );
        $xml .= sprintf(
            '<eAnnotations xmi:id="%s" source="visual-scrum">' . PHP_EOL,
            $this->getIdisifiedString($actor) . '_annotation'
        );
        $xml .= sprintf(
            '<details xmi:id="%s" key="uuid" value="%s"/>' . PHP_EOL,
            $this->getIdisifiedString($actor) . '_details',
            uniqid('_')
        );
        $xml .= "</eAnnotations>\n</packagedElement>\n";
    }

    /**
     * @param string $useCase
     * @param string $xml
     */
    private function addUseCaseNode(string $useCase, string &$xml): void
    {
        $xml .= sprintf(
            '<packagedElement xsi:type="uml:UseCase" xmi:id="%s" name="%s">' . PHP_EOL,
            $this->getIdisifiedString($useCase),
            $useCase
        );
        $xml .= sprintf(
            '<eAnnotations xmi:id="%s" source="visual-scrum">' . PHP_EOL,
            $this->getIdisifiedString($useCase) . '_annotation'
        );
        $xml .= sprintf(
            '<details xmi:id="%s" key="uuid" value="%s"/>' . PHP_EOL,
            $this->getIdisifiedString($useCase) . '_details',
            uniqid('_')
        );
        $xml .= "</eAnnotations>\n</packagedElement>\n";
    }

    /**
     * @param string $useCase
     * @param string $actor
     * @param string $xml
     */
    private function addAssociationNode(string $useCase, string $actor, string &$xml): void
    {
        $xml .= sprintf(
            '<packagedElement xsi:type="uml:UseCase" xmi:id="%s" name="%s">' . PHP_EOL,
            $this->getIdisifiedString($useCase),
            $useCase
        );
        $xml .= sprintf(
            '<eAnnotations xmi:id="%s" source="visual-scrum">' . PHP_EOL,
            $this->getIdisifiedString($useCase) . '_annotation'
        );
        $xml .= sprintf(
            '<details xmi:id="%s" key="uuid" value="%s"/>' . PHP_EOL,
            $this->getIdisifiedString($useCase) . '_details',
            uniqid('_')
        );
        $xml .= "</eAnnotations>\n</packagedElement>\n";
    }

    private function getIdisifiedString(string $string): string
    {
        return str_replace(' ', '_', strtolower($string));
    }
}
