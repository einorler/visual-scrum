<?php

namespace TrelloBundle\Plugin;

use AppBundle\Entity\Configuration;
use AppBundle\Entity\Project;
use AppBundle\Entity\UserStory;
use AppBundle\Plugin\PluginInterface;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Twig\Environment;

class TrelloPlugin implements PluginInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @param EntityManagerInterface $em
     * @param Environment $twig
     */
    public function __construct(EntityManagerInterface $em, Environment $twig)
    {
        $this->em = $em;
        $this->twig = $twig;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'trello';
    }

    /**
     * @return string
     */
    public function getSynchronizationUrl(): string
    {
        return 'api/trello';
    }

    /**
     * @return string
     */
    public function getForm()
    {
        return '';
    }

    /**
     * @param UserInterface $user
     * @param array $data
     */
    public function saveProjectData(UserInterface $user, array $data)
    {
        foreach ($data as $board) {
            $project = new Project();
            $project->setUser($user);
            $project->setTitle($board['name']);

            foreach ($board['cards'] as $card) {
                $story = new UserStory();
                $story->setTitle($card);
                $project->addUserStory($story);
            }

            $this->em->persist($project);
        }

        $this->em->flush();
    }

    /**
     * @param Configuration $configuration
     *
     * @return string
     */
    public function getConfigurationSubForm(Configuration $configuration = null)
    {
        return $this->twig->render(':plugin/trello:_configuration_subform.html.twig', [
            'configuration' => $configuration,
        ]);
    }

    /**
     * @param array $data
     * @param Configuration $configuration
     */
    public function handleConfigurationFormSubmition(array $data, Configuration $configuration)
    {
        if (!isset($data['list']) || empty($data['list'])) {
            throw new InvalidArgumentException('The name of the list holding your user stories must be provided');
        }

        $configuration->setMetas(['list' => $data['list']]);
    }
}
