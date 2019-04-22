<?php

namespace TrelloBundle\Plugin;

use AppBundle\Entity\Configuration;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Entity\UserStory;
use AppBundle\Generator\DictionaryGenerator;
use AppBundle\Plugin\PluginInterface;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Routing\RouterInterface;
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
     * @var RouterInterface
     */
    private $router;

    /**
     * @var DictionaryGenerator
     */
    private $dictionaryGenerator;

    /**
     * @param EntityManagerInterface $em
     * @param Environment            $twig
     * @param RouterInterface        $router
     * @param DictionaryGenerator        $dictionaryGenerator
     */
    public function __construct(
        EntityManagerInterface $em,
        Environment $twig,
        RouterInterface $router,
        DictionaryGenerator $dictionaryGenerator
    ) {
        $this->em = $em;
        $this->twig = $twig;
        $this->router = $router;
        $this->dictionaryGenerator = $dictionaryGenerator;
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
        return $this->router->generate('trello');
    }

    /**
     * @param User  $user
     * @param array $data
     */
    public function saveProjectData(User $user, array $data)
    {
        foreach ($data as $board) {
            $project = $user->getProjectByTitle($board['name']) ?? new Project();
            $project->setDistId($board['id']);
            $project->setUser($user);
            $project->setTitle($board['name']);

            if ($project->getId()) {
                $project->setVersion($project->getVersion() + 1);
            }

            foreach ($board['cards'] as $card) {
                $story = $project->getUserStoryByIdentifier($card['id']) ?? new UserStory();

                if ($story->getId()) continue;

                $story->setTitle($card['name']);
                $story->setDistId($card['id']);
                $story->setChanged(false);
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
