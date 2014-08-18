<?php

namespace Aws\Tests;

class Ec2MetadataTest extends \PHPUnit_Framework_TestCase
{

    private $dummyMetadata = [
            'ami-id' => 'ami-12345678',
            'ami-launch-index' => '0',
            'ami-manifest-path' => '(unknown)',
            'block-device-mapping' => [
                    'ebs0' => 'sda',
                    'ephemeral0' => 'sdb',
                    'root' => '/dev/sda1'
            ],
            'hostname' => 'ip-10-123-123-123.ap-northeast-1.compute.internal',
            'instance-action' => 'none',
            'instance-id' => 'i-87654321',
            'instance-type' => 't1.micro',
            'kernel-id' => 'aki-12345678',
            'local-hostname' => 'ip-10-123-123-123.ap-northeast-1.compute.internal',
            'local-ipv4' => '10.123.123.123',
            'mac' => '11:22:33:44:55:66',
            'metrics/vhostmd' => '<?xml version="1.0" encoding="UTF-8"?>',
            'network/interfaces/macs' => [
                    '11:22:33:44:55:66' => [
                            'device-number' => '0',
                            'local-hostname' => 'ip-10-123-123-123.ap-northeast-1.compute.internal',
                            'local-ipv4s' => '10.123.123.123',
                            'mac' => '11:22:33:44:55:66',
                            'owner-id' => '123456789012',
                            'public-hostname' => 'ec2-12-34-56-78.ap-northeast-1.compute.amazonaws.com',
                            'public-ipv4s' => '12.34.56.78'
                    ]
            ],
            'placement' => 'ap-northeast-1c',
            'product-codes' => 'abcdefghijklmnopqrstuvwxy',
            'public-hostname' => 'ec2-12-34-56-78.ap-northeast-1.compute.amazonaws.com',
            'public-ipv4' => '12.34.56.78',
            'public-keys' => [
                    [
                            'keyname' => 'my-public-key',
                            'index' => '0',
                            'format' => 'openssh-key',
                            'key' => 'ssh-rsa hogefuga my-public-key'
                    ],
                    [
                            'keyname' => 'hoge-key',
                            'index' => '1',
                            'format' => 'openssh-key',
                            'key' => 'ssh-rsa hogefugahogefuga hoge-key'
                    ]
            ],
            'ramdisk-id' => 'ari-abcdefgh',
            'reservation-id' => 'r-1234abcd',
            'security-groups' => 'securitygroups',
            'user-data' => 'this is userdata'
    ];

    private $ec2metadata;

    public function setUp()
    {

        $this->ec2metadata = new \Aws\Mock\VirtualEc2Metadata($this->dummyMetadata);
    }

    /**
     * @test
     */
    public function getAmiIdTest()
    {

        $this->assertEquals($this->ec2metadata->getAmiId(), 'ami-12345678');
    }

    /**
     * @test
     */
    public function getAmiLaunchIndexTest()
    {

        $this->assertEquals($this->ec2metadata->getAmiLaunchIndex(), '0');
    }

    /**
     * @test
     */
    public function getAmiManifestPathTest()
    {

        $this->assertEquals($this->ec2metadata->getAmiManifestPath(), '(unknown)');
    }

    /**
     * @test
     */
    public function getAncestorAmiIdsTest()
    {

        $this->assertEquals($this->ec2metadata->getAncestorAmiIds(), 'not available');
    }

    /**
     * @test
     */
    public function getBlockDeviceMappingTest()
    {

        $blockDviceMapping = $this->ec2metadata->getBlockDeviceMapping();
        $this->assertEquals($blockDviceMapping['ebs0'], 'sda');
        $this->assertEquals($blockDviceMapping['ephemeral0'], 'sdb');
        $this->assertEquals($blockDviceMapping['root'], '/dev/sda1');
    }

    /**
     * @test
     */
    public function getHostnameTest()
    {

        $this->assertEquals($this->ec2metadata->getHostname(), 'ip-10-123-123-123.ap-northeast-1.compute.internal');
    }

    /**
     * @test
     */
    public function getInstanceActionTest()
    {

        $this->assertEquals($this->ec2metadata->getInstanceAction(), 'none');
    }

    /**
     * @test
     */
    public function getInstanceIdTest()
    {

        $this->assertEquals($this->ec2metadata->getInstanceId(), 'i-87654321');
    }

    /**
     * @test
     */
    public function getInstanceTypeTest()
    {

        $this->assertEquals($this->ec2metadata->getInstanceType(), 't1.micro');
    }

    /**
     * @test
     */
    public function getKernelIdTest()
    {

        $this->assertEquals($this->ec2metadata->getKernelId(), 'aki-12345678');
    }

    /**
     * @test
     */
    public function getLocalHostnameTest()
    {

        $this->assertEquals($this->ec2metadata->getLocalHostname(), 'ip-10-123-123-123.ap-northeast-1.compute.internal');
    }

    /**
     * @test
     */
    public function getLocalIpv4Test()
    {

        $this->assertEquals($this->ec2metadata->getLocalIpv4(), '10.123.123.123');
    }

    /**
     * @test
     */
    public function getMacTest()
    {

        $this->assertEquals($this->ec2metadata->getMac(), '11:22:33:44:55:66');
    }

    /**
     * @test
     */
    public function getMetricsTest()
    {

        $this->assertEquals($this->ec2metadata->getMetrics(), '<?xml version="1.0" encoding="UTF-8"?>');
    }

    /**
     * @test
     */
    public function getPlacementTest()
    {

        $this->assertEquals($this->ec2metadata->getPlacement(), 'ap-northeast-1c');
    }

    /**
     * @test
     */
    public function getProductCodesTest()
    {

        $this->assertEquals($this->ec2metadata->getProductCodes(), 'abcdefghijklmnopqrstuvwxy');
    }

    /**
     * @test
     */
    public function getPublicIpv4Test()
    {

        $this->assertEquals($this->ec2metadata->getPublicIpv4(), '12.34.56.78');
    }

    /**
     * @test
     */
    public function getPublicKeysTest()
    {

        $publicKeys = $this->ec2metadata->getPublicKeys();
        $publicKey = $publicKeys[0];
        $this->assertEquals($publicKey['keyname'], 'my-public-key');
        $this->assertEquals($publicKey['index'], '0');
        $this->assertEquals($publicKey['format'], 'openssh-key');
        $this->assertEquals($publicKey['key'], 'ssh-rsa hogefuga my-public-key');

        $publicKey = $publicKeys[1];
        $this->assertEquals($publicKey['keyname'], 'hoge-key');
        $this->assertEquals($publicKey['index'], '1');
        $this->assertEquals($publicKey['format'], 'openssh-key');
        $this->assertEquals($publicKey['key'], 'ssh-rsa hogefugahogefuga hoge-key');
    }

    /**
     * @test
     */
    public function getRamdiskIdTest()
    {

        $this->assertEquals($this->ec2metadata->getRamdiskId(), 'ari-abcdefgh');
    }

    /**
     * @test
     */
    public function getReservationIdTest()
    {

        $this->assertEquals($this->ec2metadata->getReservationId(), 'r-1234abcd');
    }

    /**
     * @test
     */
    public function getSecurityGroupsTest()
    {

        $this->assertEquals($this->ec2metadata->getSecurityGroups(), 'securitygroups');
    }

    /**
     * @test
     */
    public function getUserDataTest()
    {

        $this->assertEquals($this->ec2metadata->getUserData(), 'this is userdata');
    }

}
