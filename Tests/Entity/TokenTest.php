<?php

namespace Igdr\Bundle\TokenAuthBundle\Tests\Entity;

use Igdr\Bundle\TokenAuthBundle\Entity\Token;
use Igdr\Bundle\TokenAuthBundle\Tests\AbstractEntityTest;

/**
 * Class TokenTest.
 */
class TokenTest extends AbstractEntityTest
{
    /**
     * @return Token
     */
    protected function createEntity()
    {
        return new Token();
    }

    /**
     * @test
     */
    public function testUser()
    {
        $mock = $this->getMock('\Symfony\Component\Security\Core\User\UserInterface');
        $this->checkField(__FUNCTION__, $mock);
    }

    /**
     * @test
     */
    public function testToken()
    {
        $this->checkField(__FUNCTION__, md5(time()));
    }

    /**
     * @test
     */
    public function testCreated()
    {
        $this->assertTrue($this->createEntity()->getCreated() instanceof \DateTime);
    }

    /**
     * @test
     */
    public function testLastUse()
    {
        $this->checkField(__FUNCTION__, new \DateTime());
    }
}
