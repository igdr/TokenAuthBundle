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
        $user = $token->getUser();
        $hash = $token->getHash();

        if (!$user instanceof UserInterface) {
            /* @var $token \Igdr\Bundle\TokenAuthBundle\Security\Authentication\AuthToken */
            if (strlen($hash) > 0) {
                $user = $this->tokenService->verifyToken($hash);
                if (!$user) {
                    throw new UsernameNotFoundException('User with token '.$hash.' not found.');
                }
            } else {
                $presentedPassword = $token->getCredentials();
                if (!$presentedPassword) {
                    throw new BadCredentialsException('The presented password cannot be empty.');
                }
                $user = $this->userProvider->loadUserByUsername($token->getUser());
                if (!$this->encoderFactory->getEncoder($user)->isPasswordValid($user->getPassword(), $presentedPassword, $user->getSalt())) {
                    throw new BadCredentialsException('The presented password is invalid.');
                }

                $hash = $this->tokenService->generateToken($user);
            }
        }

        $authenticatedToken = new AuthToken($user->getRoles());
        $authenticatedToken->setUser($user);
        $authenticatedToken->setHash($hash);
        $authenticatedToken->setAuthenticated(true);

        //fire event
        $this->eventDispatcher->dispatch(IgdrTokenAuth::ON_TOKEN_LOGIN, new TokenLoginEvent($authenticatedToken));

        return $authenticatedToken;
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
