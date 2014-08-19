<?php

namespace Aws;

/**
 * This class uses file_get_contents to query the EC2 instance Metadata from within a running EC2 instance.
 *
 * see:http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/AESDG-chapter-instancedata.html
 */
class Ec2Metadata
{

    protected $protocol = 'http';

    protected $hostname = '169.254.169.254';

    protected $path = 'latest/meta-data';

    private $commands = [
            'AmiId' => 'ami-id',
            'AmiLaunchIndex' => 'ami-launch-index',
            'AmiManifestPath' => 'ami-manifest-path',
            'AncestorAmiIds' => 'ancestor-ami-ids',
            'BlockDeviceMapping' => 'block-device-mapping',
            'Hostname' => 'hostname',
            'InstanceAction' => 'instance-action',
            'InstanceId' => 'instance-id',
            'InstanceType' => 'instance-type',
            'KernelId' => 'kernel-id',
            'LocalHostname' => 'local-hostname',
            'LocalIpv4' => 'local-ipv4',
            'Mac' => 'mac',
            'Metrics' => 'metrics/vhostmd',
            'Network' => 'network/interfaces/macs',
            'Placement' => 'placement/availability-zone',
            'ProductCodes' => 'product-codes',
            'Profile' => 'profile',
            'PublicHostname' => 'public-hostname',
            'PublicIpv4' => 'public-ipv4',
            'PublicKeys' => 'public-keys',
            'RamdiskId' => 'ramdisk-id',
            'ReservationId' => 'reservation-id',
            'SecurityGroups' => 'security-groups',
            'Services' => 'services/domain',
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

    public function getNetwork()
    {

        $macList = explode(PHP_EOL, $this->get('Network'));
        foreach ($macList as $mac) {
            $interfaces = [];
            foreach (explode(PHP_EOL, $this->get('Network', $mac)) as $key) {
                $interfaces[$key] = $this->get('Network', sprintf("%s/%s", $mac, $key));
            }
            $network[$mac] = $interfaces;
        }

        return $network;
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
