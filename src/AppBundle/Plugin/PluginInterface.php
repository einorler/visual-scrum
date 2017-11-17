<?php

namespace AppBundle\Plugin;

use AppBundle\Entity\Configuration;
use AppBundle\Entity\User;
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
    public function getSynchronizationUrl(): string;

    /**
     * @param User  $user
     * @param array $data
     */
    public function saveProjectData(User $user, array $data);

    /**
     * @param Configuration $configuration
     *
     * @return string
     */
    public function getConfigurationSubForm(Configuration $configuration = null);

    /**
     * @param array $data
     * @param Configuration $configuration
     */
    public function handleConfigurationFormSubmition(array $data, Configuration $configuration);
}
