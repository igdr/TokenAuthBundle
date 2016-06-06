<?php

namespace Igdr\Bundle\TokenAuthBundle\Security\Authentication\Provider;

use Igdr\Bundle\TokenAuthBundle\IgdrTokenAuth;
use Igdr\Bundle\TokenAuthBundle\Event\TokenLoginEvent;
use Igdr\Bundle\TokenAuthBundle\Security\Authentication\AuthToken;
use Igdr\Bundle\TokenAuthBundle\Service\TokenService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Class AuthProvider.
 */
class AuthProvider implements AuthenticationProviderInterface
{
    /**
     * @var \Symfony\Component\Security\Core\User\UserProviderInterface
     */
    private $userProvider;

    /**
     * @var TokenService
     */
    protected $tokenService;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param UserProviderInterface   $userProvider
     * @param EncoderFactoryInterface $encoderFactory
     * @param TokenService            $tokenService
     */
    public function __construct(UserProviderInterface $userProvider, EncoderFactoryInterface $encoderFactory, TokenService $tokenService)
    {
        $this->userProvider = $userProvider;
        $this->encoderFactory = $encoderFactory;
        $this->tokenService = $tokenService;
    }

    /**
     * @param TokenInterface $token
     *
     * @return AuthToken
     *
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function authenticate(TokenInterface $token)
    {
        /* @var $token AuthToken */
        if ($tokenUser = $this->tokenService->verifyToken($token->getHash())) {
            $user = $this->userProvider->loadUserByUsername($tokenUser->getUsername());
            if (!$user instanceof UserInterface) {
                throw new AuthenticationServiceException('The user provider must return a UserInterface object.');
            }

            $authenticatedToken = new AuthToken($user->getRoles());
            $authenticatedToken->setUser($user);
            $authenticatedToken->setHash($token->getHash());

            //fire event
            $this->eventDispatcher->dispatch(IgdrTokenAuth::ON_TOKEN_LOGIN, new TokenLoginEvent($authenticatedToken));

            return $authenticatedToken;
        }

        throw new AuthenticationException('The token authentication failed.');
    }

    /**
     * @param TokenInterface $token
     *
     * @return bool
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof AuthToken;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher($eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
