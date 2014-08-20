<?php

namespace D9magai\Tests;

class IsRunningOnEc2Test extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function isRunningOnEc2ThrowingRuntimeExceptionTest()
    {

        $ec2metadataGetter = new \D9magai\Mock\VirtualEc2MetadataGetter(\D9magai\Mock\DummyMetadata::$dummyMetadata);
        $this->assertEquals($ec2metadataGetter->isRunningOnEc2(), true);
    }

}
