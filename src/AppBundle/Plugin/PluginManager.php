<?php

namespace AppBundle\Plugin;

class PluginManager
{
    /**
     * @var PluginInterface[]
     */
    private $plugins;

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
}
