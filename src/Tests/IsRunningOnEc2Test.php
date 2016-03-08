<?php

namespace Razorpay\EC2Metadata\Tests;

class IsRunningOnEc2Test extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function isRunningOnEc2Test()
    {

        $this->assertEquals((new \Razorpay\EC2Metadata\Mock\VirtualEc2MetadataGetter(\Razorpay\EC2Metadata\Mock\DummyMetadata::$dummyMetadata))->isRunningOnEc2(), true);
    }

}
