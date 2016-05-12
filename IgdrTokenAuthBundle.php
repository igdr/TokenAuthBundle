<?php

namespace Igdr\Bundle\TokenAuthBundle;

use Igdr\Bundle\TokenAuthBundle\DependencyInjection\Security\Factory\AuthFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class IgdrTokenAuthBundle.
 */
class IgdrTokenAuthBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new AuthFactory());
    }
}
