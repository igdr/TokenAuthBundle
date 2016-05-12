<?php

namespace Igdr\Bundle\TokenAuthBundle\Service;

use Igdr\Bundle\TokenAuthBundle\Entity\Token;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TokenService.
 */
class TokenService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var int
     */
    private $expire;

    /**
     * @param EntityManager $em
     * @param int           $expire
     */
    public function __construct(EntityManager $em, $expire)
    {
        $this->em = $em;
        $this->expire = $expire;
    }

    /**
     * @param UserInterface $user
     *
     * @return string
     */
    public function generateToken(UserInterface $user)
    {
        $token = md5(uniqid(rand(), 1));
        $apiToken = new Token();
        $apiToken->setUser($user);
        $apiToken->setToken($token);

        $this->em->persist($apiToken);
        $this->em->flush();

        return $token;
    }

    /**
     * @param string $token
     *
     * @return UserInterface
     */
    public function verifyToken($token)
    {
        $apiToken = $this->em->getRepository('IgdrTokenAuthBundle:Token')->findOneBy(array('token' => $token));
        if (!$apiToken) {
            return;
        }

        $apiToken->setLastUse(new \DateTime());

        $this->em->persist($apiToken);
        $this->em->flush();

        return $apiToken->getUser();
    }

    /**
     * @param string $token
     */
    public function removeToken($token)
    {
        $apiToken = $this->em->getRepository('IgdrTokenAuthBundle:Token')->findOneBy(array('token' => $token));
        if ($apiToken) {
            $this->em->remove($apiToken);
            $this->em->flush();
        }
    }

    /**
     * @param UserInterface $user
     */
    public function removeTokenByUser(UserInterface $user)
    {
        $tokens = $this->em->getRepository('IgdrTokenAuthBundle:Token')->findBy(['user' => $user]);
        foreach ($tokens as $token) {
            $this->em->remove($token);
        }

        $this->em->flush();
    }

    /**
     *  Remove expired tokens.
     */
    public function cleanupToken()
    {
        $day = $this->expire;
        $expired = new \DateTime("now - $day days");
        $tokens = $this->em->getRepository('IgdrTokenAuthBundle:Token')->cleanup($expired->format('Y-m-d'));

        foreach ($tokens as $token) {
            $this->em->remove($token);
        }

        $this->em->flush();
    }
}
