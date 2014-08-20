<?php

namespace D9magai\Tests;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessage [ERROR] Command not valid outside EC2 instance.
     * Please run this command within a running EC2 instance.
     */
    public function isRunningOnEc2ThrowingRuntimeExceptionTest()
    {

        (new \D9magai\Mock\VirtualEc2MetadataGetter(\D9magai\Mock\DummyMetadata::$dummyMetadata, 'not_found'))->isRunningOnEc2();
    }

    /**
     * @test
     * @expectedException LogicException
     * @expectedExceptionMessage Only get operations allowed.
     */
    public function magicMethodThrowingLogicExceptionTest()
    {

        (new \D9magai\Mock\VirtualEc2MetadataGetter(\D9magai\Mock\DummyMetadata::$dummyMetadata))->getHogeFuga();
    }

}
