<?php

namespace Igdr\Bundle\TokenAuthBundle\Event;

use Igdr\Bundle\TokenAuthBundle\Security\Authentication\AuthToken;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class TokenLoginEvent.
 */
class TokenLoginEvent extends Event
{
    /**
     * @var AuthToken
     */
    private $token;

    /**
     * TokenLoginEvent constructor.
     *
     * @param AuthToken $token
     */
    public function __construct(AuthToken $token)
    {
        $this->token = $token;
    }

    /**
     * @return AuthToken
     */
    public function getToken(): AuthToken
    {
        return $this->token;
    }
}
