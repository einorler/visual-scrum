<?php

namespace AppBundle\Plugin;

use AppBundle\Entity\Configuration;

class PluginManager
{
    /**
     * @var PluginInterface[]
     */
    private $plugins = [];

    /**
     * @param PluginInterface $plugin
     */
    public function addPlugin(PluginInterface $plugin)
    {
        $this->plugins[$plugin->getName()] = $plugin;
    }

    /**
     * @return array
     */
    public function getAvailablePlugins(): array
    {
        return array_keys($this->plugins);
    }

    /**
     * @param string $plugin
     * @param Configuration $configuration
     *
     * @return string
     */
    public function getConfigurationSubForm(string $plugin, Configuration $configuration = null)
    {
        if (!isset($this->plugins[$plugin])) {
            throw new \InvalidArgumentException('There is no plugin called `' . $plugin . '` configured!');
        }

        return $this->plugins[$plugin]->getConfigurationSubForm($configuration);
    }

    /**
     * @param array $data
     * @param string $plugin
     * @param Configuration $configuration
     */
    public function submitConfiguration(array $data, string $plugin, Configuration $configuration)
    {
        $configuration->setType($plugin);
        $this->plugins[$plugin]->handleConfigurationFormSubmition($data, $configuration);
    }
}
