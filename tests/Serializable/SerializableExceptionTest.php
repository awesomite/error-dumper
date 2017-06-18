<?php

namespace Awesomite\ErrorDumper\Serializable;

use Awesomite\ErrorDumper\TestBase;

/**
 * @internal
 */
class SerializableExceptionTest extends TestBase
{
    /**
     * @dataProvider providerAll
     *
     * @param \Exception|\Throwable $exception
     * @param SerializableException $clonedException
     * @param bool $hasPrevious
     */
    public function testAll($exception, SerializableException $clonedException, $hasPrevious)
    {
        $this->assertSame($exception->getCode(), $clonedException->getCode());
        $this->assertSame($exception->getFile(), $clonedException->getFile());
        $this->assertSame($exception->getLine(), $clonedException->getLine());
        $this->assertSame($exception->getMessage(), $clonedException->getMessage());
        $this->assertInstanceOf($clonedException->getOriginalClass(), $exception);
        $this->assertInstanceOf('Awesomite\StackTrace\StackTraceInterface', $clonedException->getStackTrace());

        $this->assertSame($hasPrevious, !is_null($exception->getPrevious()));
        $this->assertSame($hasPrevious, $clonedException->hasPrevious());
        if ($hasPrevious) {
            $this->testAll(
                $exception->getPrevious(),
                $clonedException->getPrevious(),
                !is_null($exception->getPrevious()->getPrevious())
            );
        }
    }

    public function providerAll()
    {
        $exception = new \Exception('Without previous');

        $withPrevious = null;
        try {
            $this->throwExceptionWithPrevious();
        } catch (\Exception $withPrevious) {}

        return array(
            array($exception, new SerializableException($exception), false),
            array($withPrevious, new SerializableException($withPrevious), true),
        );
    }

    public function testSerialize()
    {
        $message = 'Test message ' . mt_rand(1, 10000);
        $clonedException = new SerializableException(new \Exception($message), 5);
        /** @var SerializableException $unserialized */
        $unserialized = unserialize(serialize($clonedException));
        $this->assertSame($message, $unserialized->getMessage());
    }

    /**
     * @dataProvider providerConstructorWithPrevious
     *
     * @param bool $clonePrevious
     */
    public function testConstructorWithPrevious($clonePrevious)
    {
        $exception = null;
        try {
            $this->throwExceptionWithPrevious();
        } catch (\Exception $exception) {};
        $cloned = new SerializableException($exception, 0, false, $clonePrevious);
        $this->assertSame($clonePrevious, $cloned->hasPrevious());
        if ($clonePrevious) {
            $interface = 'Awesomite\ErrorDumper\Serializable\SerializableExceptionInterface';
            $this->assertInstanceOf($interface, $cloned->getPrevious());
        }
    }

    public function providerConstructorWithPrevious()
    {
        return array(
            array(true),
            array(false),
        );
    }

    /**
     * @expectedException \LogicException
     */
    public function testConstructorWithoutPrevious()
    {
        $cloned = new SerializableException(new \Exception());
        $this->assertFalse($cloned->hasPrevious());
        $cloned->getPrevious();
    }

    private function throwExceptionWithPrevious()
    {
        try {
            throw new \Exception('First');
        } catch (\Exception $exception) {
            throw new \Exception('Second', 0, $exception);
        }
    }
}
