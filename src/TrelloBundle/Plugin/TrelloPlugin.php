<?php

namespace TrelloBundle\Plugin;

use AppBundle\Entity\Project;
use AppBundle\Entity\UserStory;
use AppBundle\Plugin\PluginInterface;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserInterface;

class TrelloPlugin implements PluginInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
    public function getMainRoute(): string
    {
        return 'trello';
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
}
