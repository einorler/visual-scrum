<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_story")
 */
class UserStory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="userStories")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;
}
