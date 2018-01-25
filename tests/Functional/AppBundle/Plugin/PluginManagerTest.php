<?php

namespace Tests\Functional\AppBundle\Plugin;

use AppBundle\Entity\Configuration;
use Tests\Functional\ApplicationTest;

class PluginManagerTest extends ApplicationTest
{
    public function testGetAvailablePlugins()
    {
        $plugins = $this->getContainer()->get('app.manager.plugin')->getAvailablePlugins();

        $this->assertEquals(['trello'], $plugins);
    }

    public function testGetSynchronizationUrl()
    {
        $configuration = new Configuration();
        $configuration->setType('trello');
        $manager = $this->getContainer()->get('app.manager.plugin');

        $this->assertEquals('/api/trello', $manager->getSynchronizationUrl($configuration));
    }
}
