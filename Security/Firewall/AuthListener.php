<?php

namespace Igdr\Bundle\TokenAuthBundle\Security\Firewall;

use Igdr\Bundle\TokenAuthBundle\Security\Authentication\AuthToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * Class AuthListener.
 */
class AuthListener implements ListenerInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStoreage;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface
     */
    protected $authManager;

    /**
     * @param TokenStorageInterface          $tokenStoreage
     * @param AuthenticationManagerInterface $authManager
     */
    public function __construct(TokenStorageInterface $tokenStoreage, AuthenticationManagerInterface $authManager)
    {
        $this->tokenStoreage = $tokenStoreage;
        $this->authManager = $authManager;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @return bool
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function handle(GetResponseEvent $event)
    {
        $tokenString = $this->getTokenString($event->getRequest());
        if ($tokenString) {
            $token = new AuthToken();
            $token->setHash($tokenString);
            try {
                $authToken = $this->authManager->authenticate($token);
                $this->tokenStoreage->setToken($authToken);
            } catch (AuthenticationException $e) {
                throw new AccessDeniedHttpException($e->getMessage());
            }
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    private function getTokenString(Request $request)
    {
        $tokenString = $request->headers->get('X-Auth-Token');
        if (empty($tokenString)) {
            $tokenString = $request->cookies->get('X-Auth-Token');
        }
        if (empty($tokenString)) {
            $tokenString = $request->query->get('x_auth_token');
        }

        return $tokenString;
    }
}
