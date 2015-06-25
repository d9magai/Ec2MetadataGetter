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

    public function testCacheWritten()
    {
        $ec2 = new Ec2MetadataGetter("/tmp");
        $ec2->allowDummy();
        $response = $ec2->getMultiple(['AmiId']);

        $this->assertEquals('ami-12345678', $response['AmiId']);

        $cacheContent = file_get_contents('/tmp/fd2f4b29d4b9a6c68c2669d66aacd03dffa6164b.json');
        $this->assertEquals('{"AmiId":"ami-12345678"}', $cacheContent);
    }
}
