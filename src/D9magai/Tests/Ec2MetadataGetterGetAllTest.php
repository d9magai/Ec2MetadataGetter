<?php

namespace D9magai\Tests;

class Ec2MetadataGetterGetAllTest extends \PHPUnit_Framework_TestCase
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
            'placement/availability-zone' => 'ap-northeast-1c',
            'product-codes' => 'abcdefghijklmnopqrstuvwxy',
            'profile' => 'default-paravirtual',
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
            'services/domain' => 'amazonaws.com',
            'user-data' => 'this is userdata'
    ];

    private $ec2metadataGetter;

    public function setUp()
    {

        $this->ec2metadataGetter = new \D9magai\Mock\VirtualEc2MetadataGetter($this->dummyMetadata);
    }

    /**
     * @test
     */
    public function getAllTest()
    {

        $allMetadata = $this->ec2metadataGetter->getAll();

        $this->assertEquals($allMetadata['AmiId'], 'ami-12345678');
        $this->assertEquals($allMetadata['AmiLaunchIndex'], '0');
        $this->assertEquals($allMetadata['AmiManifestPath'], '(unknown)');
        $this->assertEquals($allMetadata['AncestorAmiIds'], 'not available');

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
