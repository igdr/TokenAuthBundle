<?php

namespace Igdr\Bundle\TokenAuthBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class AuthFactory.
 */
class AuthFactory implements SecurityFactoryInterface
{
    /**
     * @param ContainerBuilder      $container
     * @param string                $id
     * @param array                 $config
     * @param UserProviderInterface $userProvider
     * @param string                $defaultEntryPoint
     *
     * @return array
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.api.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('igdr_token_auth_security_authentication_provider'))
            ->replaceArgument(0, new Reference($userProvider))
            ->addArgument($config['lifetime']);

        $listenerId = 'security.authentication.listener.api.'.$id;
        $container->setDefinition($listenerId, new DefinitionDecorator('igdr_token_auth_security_authentication_listener'));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return 'api_token';
    }

    /**
     * @param NodeDefinition $node
     */
    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
            ->scalarNode('lifetime')->defaultValue(300)
            ->end();
    }
}
