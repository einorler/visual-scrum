<?php

namespace AppBundle\Plugin;

use FOS\UserBundle\Model\UserInterface;

interface PluginInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getMainRoute(): string;

    /**
     * @return mixed
     */
    public function getForm();

    /**
     * @param UserInterface $user
     * @param array $data
     */
    public function saveProjectData(UserInterface $user, array $data);
}
