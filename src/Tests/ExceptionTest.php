<?php

namespace Razorpay\EC2Metadata\Tests;

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

        (new \Razorpay\EC2Metadata\Mock\VirtualEc2MetadataGetter(\Razorpay\EC2Metadata\Mock\DummyMetadata::$dummyMetadata, 'not_found'))->isRunningOnEc2();
    }

    /**
     * @test
     * @expectedException LogicException
     * @expectedExceptionMessage Only get operations allowed.
     */
    public function magicMethodThrowingLogicExceptionTest()
    {

        (new \Razorpay\EC2Metadata\Mock\VirtualEc2MetadataGetter(\Razorpay\EC2Metadata\Mock\DummyMetadata::$dummyMetadata))->getHogeFuga();
    }

}
