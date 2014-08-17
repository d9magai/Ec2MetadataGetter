<?php

namespace Aws\Tests;

class Ec2MetadataTest extends \PHPUnit_Framework_TestCase
{

    private $metadata = [
            'ami-id' => 'ami-12345678',
            'ami-launch-index' => '0',
            'ami-manifest-path' => '(unknown)',
            'ancestor-ami-ids' => 'not available',
            'instance-id' => 'i-87654321',
            'instance-type' => 't1.micro',
            'local-hostname' => 'ip-10-123-123-123.ap-northeast-1.compute.internal',
            'local-ipv4' => '10.123.123.123',
            'kernel-id' => 'aki-12345678',
            'placement' => 'ap-northeast-1c',
            'product-codes' => 'abcdefghijklmnopqrstuvwxy',
            'public-hostname' => 'ec2-12-34-56-78.ap-northeast-1.compute.amazonaws.com',
            'public-ipv4' => '12.34.56.78',
            'amdisk-id' => 'not available',
            'reservation-id' => 'r-1234abcd',
            'ecurity-groups' => 'securitygroups'
    ];

    private $ec2metadata;

    public function setUp()
    {

        $this->ec2metadata = new \Aws\Mock\VirtualEc2Metadata($this->metadata);
    }

    /**
     * @test
     */
    public function getAmiIdTest()
    {

        $this->assertEquals($this->ec2metadata->getAmiId(), 'ami-12345678');
    }
}
