<?php

namespace Igdr\Bundle\TokenAuthBundle\Tests\DependencyInjection;

use Igdr\Bundle\TokenAuthBundle\DependencyInjection\IgdrTokenAuthExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class IgdrApiExtensionTest.
 */
class IgdrTokenAuthExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testLoad()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new IgdrTokenAuthExtension();
        $loader->load(array(), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }
}
