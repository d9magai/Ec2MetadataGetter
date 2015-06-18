<?php

namespace Razorpay\EC2Metadata\Tests;

class GetAllTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function getAllTest()
    {

        $allMetadata = (new \Razorpay\EC2Metadata\Mock\VirtualEc2MetadataGetter(\Razorpay\EC2Metadata\Mock\DummyMetadata::$dummyMetadata))->getAll();

        $this->assertEquals($allMetadata['AmiId'], 'ami-12345678');
        $this->assertEquals($allMetadata['AmiLaunchIndex'], '0');
        $this->assertEquals($allMetadata['AmiManifestPath'], '(unknown)');
        $this->assertEquals($allMetadata['AncestorAmiIds'], false);

        $this->assertEquals($allMetadata['BlockDeviceMapping']['ebs0'], 'sda');
        $this->assertEquals($allMetadata['BlockDeviceMapping']['ephemeral0'], 'sdb');
        $this->assertEquals($allMetadata['BlockDeviceMapping']['root'], '/dev/sda1');

        $this->assertEquals($allMetadata['Hostname'], 'ip-10-123-123-123.ap-northeast-1.compute.internal');
        $this->assertEquals($allMetadata['InstanceAction'], 'none');
        $this->assertEquals($allMetadata['InstanceId'], 'i-87654321');
        $this->assertEquals($allMetadata['InstanceType'], 't1.micro');
        $this->assertEquals($allMetadata['KernelId'], 'aki-12345678');
        $this->assertEquals($allMetadata['LocalHostname'], 'ip-10-123-123-123.ap-northeast-1.compute.internal');
        $this->assertEquals($allMetadata['LocalIpv4'], '10.123.123.123');
        $this->assertEquals($allMetadata['Mac'], '11:22:33:44:55:66');
        $this->assertEquals($allMetadata['Metrics'], '<?xml version="1.0" encoding="UTF-8"?>');

        $this->assertEquals($allMetadata['Network']['11:22:33:44:55:66']['device-number'], '0');
        $this->assertEquals($allMetadata['Network']['11:22:33:44:55:66']['local-hostname'], 'ip-10-123-123-123.ap-northeast-1.compute.internal');
        $this->assertEquals($allMetadata['Network']['11:22:33:44:55:66']['local-ipv4s'], '10.123.123.123');
        $this->assertEquals($allMetadata['Network']['11:22:33:44:55:66']['mac'], '11:22:33:44:55:66');
        $this->assertEquals($allMetadata['Network']['11:22:33:44:55:66']['owner-id'], '123456789012');
        $this->assertEquals($allMetadata['Network']['11:22:33:44:55:66']['public-hostname'], 'ec2-12-34-56-78.ap-northeast-1.compute.amazonaws.com');
        $this->assertEquals($allMetadata['Network']['11:22:33:44:55:66']['public-ipv4s'], '12.34.56.78');

        $this->assertEquals($allMetadata['Placement'], 'ap-northeast-1c');
        $this->assertEquals($allMetadata['ProductCodes'], 'abcdefghijklmnopqrstuvwxy');
        $this->assertEquals($allMetadata['Profile'], 'default-paravirtual');
        $this->assertEquals($allMetadata['PublicIpv4'], '12.34.56.78');

        $this->assertEquals($allMetadata['PublicKeys'][0]['keyname'], 'my-public-key');
        $this->assertEquals($allMetadata['PublicKeys'][0]['index'], '0');
        $this->assertEquals($allMetadata['PublicKeys'][0]['format'], 'openssh-key');
        $this->assertEquals($allMetadata['PublicKeys'][0]['key'], 'ssh-rsa hogefuga my-public-key');
        $this->assertEquals($allMetadata['PublicKeys'][1]['keyname'], 'hoge-key');
        $this->assertEquals($allMetadata['PublicKeys'][1]['index'], '1');
        $this->assertEquals($allMetadata['PublicKeys'][1]['format'], 'openssh-key');
        $this->assertEquals($allMetadata['PublicKeys'][1]['key'], 'ssh-rsa hogefugahogefuga hoge-key');

        $this->assertEquals($allMetadata['RamdiskId'], 'ari-abcdefgh');
        $this->assertEquals($allMetadata['ReservationId'], 'r-1234abcd');
        $this->assertEquals($allMetadata['SecurityGroups'], 'securitygroups');
        $this->assertEquals($allMetadata['Services'], 'amazonaws.com');

        $this->assertEquals($allMetadata['UserData'], 'this is userdata');
    }

}
