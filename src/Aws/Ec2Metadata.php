<?php

namespace Aws;

class Ec2Metadata
{

    protected $protocol = 'http';

    protected $hostname = '169.254.169.254';

    protected $path = 'latest/meta-data';

    protected $commands = [
            'AmiId' => 'ami-id',
            'AmiLaunchIndex' => 'ami-launch-index',
            'AmiManifestPath' => 'ami-manifest-path',
            'AncestorAmiIds' => 'ancestor-ami-ids',
            'BlockDeviceMapping' => 'block-device-mapping',
            'Hostname' => 'hostname',
            'InstanceId' => 'instance-id',
            'InstanceType' => 'instance-type',
            'LocalHostname' => 'local-hostname',
            'LocalIpv4' => 'local-ipv4',
            'KernelId' => 'kernel-id',
            'Placement' => 'placement/availability-zone',
            'ProductCodes' => 'product-codes',
            'PublicHostname' => 'public-hostname',
            'PublicIpv4' => 'public-ipv4',
            'PublicKeys' => 'public-keys',
            'RamdiskId' => 'ramdisk-id',
            'ReservationId' => 'reservation-id',
            'SecurityGroups' => 'security-groups',
            'UserData' => 'user-data'
    ];

    public function getBlockDeviceMapping()
    {

        $output = [];
        foreach (explode(PHP_EOL, $this->get('BlockDeviceMapping')) as $map) {
            $output[$map] = $this->get('BlockDeviceMapping', $map);
        }
        return $output;
    }

    public function getPublicKeys()
    {

        $keys = [];
        foreach (explode(PHP_EOL, $this->get('PublicKeys')) as $publicKey) {
            list($index, $keyname) = explode('=', $publicKey, 2);
            $format = $this->get('PublicKeys', $index);

            $keys[] = [
                    'keyname' => $keyname,
                    'index' => $index,
                    'format' => $format,
                    'key' => $this->get('PublicKeys', sprintf("%s/%s", $index, $format))
            ];
        }

        return $keys;
    }

    public function getAll()
    {

        $output = [];
        foreach ($this->getCommands() as $req => $apiArg) {
            $output[$req] = $this->{"get$req"}();
        }

        return $output;
    }

    public function chkConfig()
    {

        if (!@get_headers($this->url)) {
            throw new \RuntimeException("[ERROR] Command not valid outside EC2 instance. Please run this command within a running EC2 instance.");
        }

        return true;
    }

    public function get($req, $args = '')
    {

        $response = @file_get_contents(sprintf("%s/%s/%s", $this->getFullPath(), $this->commands[$req], $args));
        return $response === false ? 'not available' : $response;
    }

    public function exists($req)
    {

        return array_key_exists($req, $this->commands);
    }

    public function getCommands()
    {

        return $this->commands;
    }

    private function getFullPath()
    {

        return sprintf("%s://%s/%s", $this->protocol, $this->hostname, $this->path);
    }

    public function __call($functionName, $args)
    {

        $command = preg_replace('/^get/', '', $functionName);
        if (!$this->exists($command)) {
            throw new \LogicException("Only get operations allowed.");
        }
        return $this->get($command, array_pop($args));
    }
}