<?php

namespace AppBundle\Generator;

class XmlGenerator
{
    /**
     * @var string
     */
    private $initial = '<?xml version="1.0" encoding="UTF-8"?><uml:Model xmi:version="2.0" xmlns:xmi="http://www.omg.org/XMI" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:uml="http://www.eclipse.org/uml2/5.0.0/UML" xmi:id="_diagram" name="model">';

    /**
     * @var int
     */
    private $associationCount = 0;

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

        return $xml;
    }

    /**
     * @param array $data The necessary data for UML diagram creation
     *
     * @return string The generated filename
     *
     * @throws \Exception
     */
    public function generateClassXml(array $data): string
    {
        $xml = $this->initial;

        $xml .= '
          <eAnnotations xmi:id="_anotation" source="genmymodel">
            <details xmi:id="_anotation_value" key="uuid" value="' . md5(rand(0, 100000000)) . '"/>
            <details xmi:id="_anotation_author" key="author" value="Pakistan42"/>
          </eAnnotations>
        ';

        foreach ($data as $noun) {
            if (!is_string($noun)) {
                throw new \Exception('Failed generating class diagram due to invalid dictionary');
            }

            $codifiedNoun = str_replace(' ', '_', $noun);

            $xml .= '
              <packagedElement xsi:type="uml:Class" xmi:id="_noun_' . $codifiedNoun . '" name="' . $noun . '">
                <eAnnotations xmi:id="_noun_' . $codifiedNoun . '_source" source="genmymodel">
                  <details xmi:id="_noun_' . $codifiedNoun . '_uuid" key="uuid" value="' . md5($codifiedNoun) . '"/>
                </eAnnotations>
              </packagedElement>';
        }

        $xml .= '</uml:Model>';

        return $xml;
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

        $this->addAnnotations($this->getIdisifiedString($actor), $xml);
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

        $this->addAnnotations($this->getIdisifiedString($useCase), $xml);
        $xml .= "</eAnnotations>\n</packagedElement>\n";
    }

    /**
     * @param string $useCase
     * @param string $actor
     * @param string $xml
     */
    private function addAssociationNode(string $useCase, string $actor, string &$xml): void
    {
        $end1 = 'assoc_' . $this->associationCount . '_end_1';
        $end2 = 'assoc_' . $this->associationCount . '_end_2';

        $xml .= sprintf(
            '<packagedElement xsi:type="uml:Association" xmi:id="%s" name="%s" memberEnd="%s" navigableOwnedEnd="%s">' . PHP_EOL,
            'association_' . ++$this->associationCount,
            'Association ' . $this->associationCount,
            "$end1 $end2",
            "$end2 $end1"
        );

        $this->addAnnotations('association_' . $this->associationCount, $xml);
        $xml .= "</eAnnotations>\n";

        $xml .= sprintf(
            '<ownedEnd xmi:id="%s" name="useCase" type="%s" association="%s">',
            $end1,
            $this->getIdisifiedString($useCase),
            'association_' . $this->associationCount
        );

        $this->addAnnotations($end1, $xml);
        $xml .= "</eAnnotations>\n";

        // <upperValue xsi:type="uml:LiteralInteger" xmi:id="_yIR09vyVEeeyruBoe7-QtQ" value="1">
        $xml .= sprintf(
            '<lowerValue xmi:id="%s" xsi:type="uml:LiteralInteger">',
            $end1 . '_lower'
        );

        $this->addAnnotations($end1 . '_lower', $xml);
        $xml .= "</eAnnotations>\n";
        $xml .= "</lowerValue>\n";

        $xml .= sprintf(
            '<upperValue xmi:id="%s" xsi:type="uml:LiteralInteger" value="1">',
            $end1 . 'upper'
        );

        $this->addAnnotations($end1 . '_upper', $xml);
        $xml .= "</eAnnotations>\n";
        $xml .= "</upperValue>\n";
        $xml .= "</ownedEnd>\n";

        $xml .= sprintf(
            '<ownedEnd xmi:id="%s" name="useCase" type="%s" association="%s">',
            $end2,
            $this->getIdisifiedString($actor),
            'association_' . $this->associationCount
        );

        $this->addAnnotations($end1, $xml);
        $xml .= "</eAnnotations>\n";

        $xml .= sprintf(
            '<lowerValue xmi:id="%s" xsi:type="uml:LiteralInteger">',
            $end2 . '_lower'
        );

        $this->addAnnotations($end2 . '_lower', $xml);
        $xml .= "</eAnnotations>\n";
        $xml .= "</lowerValue>\n";

        $xml .= sprintf(
            '<upperValue xmi:id="%s" xsi:type="uml:LiteralInteger" value="1">',
            $end1 . 'upper'
        );

        $this->addAnnotations($end1 . '_upper', $xml);
        $xml .= "</eAnnotations>\n";
        $xml .= "</upperValue>\n";
        $xml .= "</ownedEnd>\n";
        $xml .= "</packagedElement>\n";
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function getIdisifiedString(string $string): string
    {
        return str_replace(' ', '_', strtolower($string));
    }

    /**
     * @param string $element
     * @param string $xml
     */
    private function addAnnotations(string $element, string &$xml)
    {
        $xml .= sprintf(
            '<eAnnotations xmi:id="%s" source="visual-scrum">' . PHP_EOL,
            $element . '_annotation'
        );
        $xml .= sprintf(
            '<details xmi:id="%s" key="uuid" value="%s"/>' . PHP_EOL,
            $element . '_details',
            uniqid('_')
        );
    }
}
