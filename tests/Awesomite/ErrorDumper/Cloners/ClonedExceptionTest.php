<?php

namespace Awesomite\ErrorDumper\Cloners;

use Awesomite\ErrorDumper\TestBase;

/**
 * @internal
 */
class ClonedExceptionTest extends TestBase
{
    /**
     * @dataProvider providerAll
     *
     * @param \Exception|\Throwable $exception
     * @param ClonedException $clonedException
     * @param bool $hasPrevious
     */
    public function testAll($exception, ClonedException $clonedException, $hasPrevious)
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
            array($exception, new ClonedException($exception), false),
            array($withPrevious, new ClonedException($withPrevious), true),
        );
    }

    public function testSerialize()
    {
        $message = 'Test message ' . mt_rand(1, 10000);
        $clonedException = new ClonedException(new \Exception($message));
        /** @var ClonedException $unserialized */
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
        $cloned = new ClonedException($exception, 0, false, $clonePrevious);
        $this->assertSame($clonePrevious, $cloned->hasPrevious());
        if ($clonePrevious) {
            $interface = 'Awesomite\ErrorDumper\Cloners\ClonedExceptionInterface';
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
        $cloned = new ClonedException(new \Exception());
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