<?php

namespace Igdr\Bundle\TokenAuthBundle\Tests;

/**
 * Class AbstractEntityTest.
 */
abstract class AbstractEntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \stdObject
     */
    protected $entity;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->entity = $this->createEntity();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->entity);
    }

    /**
     * @return \stdObject
     */
    abstract protected function createEntity();

    /**
     * @test
     */
    public function testGetId()
    {
        $entity = $this->createEntity();
        $idProperty = new \ReflectionProperty($entity, 'id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($entity, 100);
        $this->assertEquals($entity->getId(), 100);
    }

    /**
     * Helper for testing setters and getters.
     *
     * @param mixed $method
     * @param mixed $value
     * @param mixed $defaultValue
     *
     * @throws \Exception
     *
     * @return object
     */
    protected function checkField($method, $value, $defaultValue = null)
    {
        if ($this->entity === null) {
            throw new \Exception('Entity is not set');
        }

        if (is_string($method) && preg_match('/^test/', $method)) {
            $setter = 'set'.str_replace('test', '', $method);
            $getter = 'get'.str_replace('test', '', $method);
            if (!method_exists($this->entity, $getter)) {
                $getter = 'is'.substr($getter, 3);
            }
        } elseif (is_array($method)) {
            list($setter, $getter) = $method;
        } else {
            throw new \Exception('Setters and Getters are not properly defined');
        }
        if (is_bool($defaultValue)) {
            $assert = ($defaultValue) ? 'assertTrue' : 'assertFalse';
            $this->$assert($this->entity->$getter());
        } elseif (is_string($defaultValue) && preg_match('/instanceof/', $defaultValue)) {
            $defaultValue = str_replace('instanceof', '', $defaultValue);
            $this->assertInstanceOf(trim($defaultValue), $this->entity->$getter());
        } elseif (null !== $defaultValue) {
            $this->assertEquals($defaultValue, $this->entity->$getter());
        }
        $this->entity->$setter($value);
        $this->assertEquals($value, $this->entity->$getter());
        if (is_object($value)) {
            $this->assertInstanceOf(get_class($value), $this->entity->$getter());
        }

        return $this->entity;
    }
}
