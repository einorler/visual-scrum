<?php

namespace Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ApplicationTest extends WebTestCase
{
    protected $container;

    /**
     * Returns service container.
     *
     * @param bool  $reinitialize  Force kernel reinitialization.
     * @param array $kernelOptions Options used passed to kernel if it needs to be initialized.
     *
     * @return ContainerInterface
     */
    protected function getContainer($reinitialize = false, $kernelOptions = [])
    {
        if ($this->container === null || $reinitialize) {
            static::bootKernel($kernelOptions);
            $this->container = static::$kernel->getContainer();
        }
        return $this->container;
    }
}
