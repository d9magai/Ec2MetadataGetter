<?php
namespace Razorpay\EC2Metadata\Tests;

use Razorpay\EC2Metadata\Ec2MetadataGetter;

class DummyTest extends \PHPUnit_Framework_TestCase
{
    public function testDummyData()
    {
        $ec2 = new Ec2MetadataGetter("/tmp");
        $ec2->allowDummy();
        $network = $ec2->getNetwork();

        $interfaces = $network['11:22:33:44:55:66'];
        $this->assertEquals($interfaces['device-number'], '0');
        $this->assertEquals($interfaces['local-hostname'], 'ip-10-123-123-123.ap-northeast-1.compute.internal');
        $this->assertEquals($interfaces['local-ipv4s'], '10.123.123.123');
        $this->assertEquals($interfaces['mac'], '11:22:33:44:55:66');
        $this->assertEquals($interfaces['owner-id'], '123456789012');
        $this->assertEquals($interfaces['public-hostname'], 'ec2-12-34-56-78.ap-northeast-1.compute.amazonaws.com');
        $this->assertEquals($interfaces['public-ipv4s'], '12.34.56.78');
    }

    public function testDummyMultiple()
    {
        $ec2 = new Ec2MetadataGetter("/tmp");
        $ec2->allowDummy();
        $data = $ec2->getMultiple(['Network', 'AmiId']);

        $interface = $data['Network']['11:22:33:44:55:66'];
        $this->assertEquals($interface['public-ipv4s'], '12.34.56.78');

        $this->assertEquals('ami-12345678', $data['AmiId']);
    }
}
