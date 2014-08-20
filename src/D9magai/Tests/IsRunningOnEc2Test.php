<?php

namespace D9magai\Tests;

class IsRunningOnEc2Test extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function isRunningOnEc2ThrowingRuntimeExceptionTest()
    {

        $this->assertEquals((new \D9magai\Mock\VirtualEc2MetadataGetter(\D9magai\Mock\DummyMetadata::$dummyMetadata))->isRunningOnEc2(), true);
    }

}
