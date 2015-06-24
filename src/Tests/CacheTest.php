<?php
namespace Razorpay\EC2Metadata\Tests;

use Razorpay\EC2Metadata\Ec2MetadataGetter;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $ec2 = new Ec2MetadataGetter("/tmp");
        $this->assertInstanceOf('Razorpay\EC2Metadata\Ec2MetadataGetter', $ec2);
    }

    /**
     * @expectedException     Exception
     * @expectedExceptionMessage Cache directory not writable
     */
    public function testCacheWriteableError()
    {
        new Ec2MetadataGetter("/sys");
    }
}
