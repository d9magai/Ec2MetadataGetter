<?php

namespace D9magai\Mock;

class DummyMetadata
{

    public static $dummyMetadata = [
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
}