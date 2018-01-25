<?php

namespace Tests\Functional\TrelloBundle\Plugin;

use Tests\Functional\ApplicationTest;

class PluginTest extends ApplicationTest
{
    public function testGetSynchronizationUrl()
    {
        $plugin = $this->getContainer()->get('trello.plugin');

        $this->assertEquals('/api/trello', $plugin->getSynchronizationUrl());
    }
}
