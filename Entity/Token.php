<?php

namespace Igdr\Bundle\TokenAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class Token.
 *
 * @ORM\Entity(repositoryClass="Igdr\Bundle\TokenAuthBundle\Entity\Repository\TokenRepository")
 * @ORM\Table(name="user_token", indexes={ @ORM\Index(name="token_idx", columns={"token"})})
 * @ORM\HasLifecycleCallbacks()
 */
class Token
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastUse;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $token;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $lastUse
     *
     * @return $this
     */
    public function setLastUse(\DateTime $lastUse)
    {
        $this->lastUse = $lastUse;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastUse()
    {
        return $this->lastUse;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $token
     *
     * @return Token
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param UserInterface $user
     *
     * @return $this
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
