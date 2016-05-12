<?php

namespace Igdr\Bundle\TokenAuthBundle\Security\Authentication;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * Class AuthToken.
 */
class AuthToken extends AbstractToken
{
    /**
     * @var string
     */
    private $credentials;

    /**
     * @var string
     */
    public $hash;

    /**
     * @param array $roles
     */
    public function __construct(array $roles = array())
    {
        parent::__construct($roles);
        $this->setAuthenticated(count($roles) > 0);
    }

    /**
     * @param string $credentials
     */
    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @return mixed|string
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
}
