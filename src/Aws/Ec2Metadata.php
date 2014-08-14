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
            'InstanceId' => 'instance-id',
            'InstanceType' => 'instance-type',
            'LocalHostname' => 'local-hostname',
            'LocalIpv4' => 'local-ipv4',
            'KernelId' => 'kernel-id',
            'AvailabilityZone' => 'availability-zone',
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

        $maps = $this->get('block-device-mapping');

        $output = [];

        foreach (explode(PHP_EOL, $maps) as $map) {
            $output[$map] = $this->get('block-device-mapping', [
                    $map
            ]);
        }

        return $output;
    }

    public function getPublicKeys()
    {

        $rawKeys = $this->get('public-keys');

        $keys = [];
        foreach (explode(PHP_EOL, $rawKeys) as $rawKey) {
            $parts = explode('=', $rawKey);
            $index = $parts[0];
            $keyname = $parts[1];

            $format = $this->get('public-keys', [
                    $index
            ]);

            $key = [
                    'keyname' => $keyname,
                    'index' => $index,
                    'format' => $format,
                    'key' => $this->get('public-keys', [
                            $index,
                            $format
                    ])
            ];

            $keys[] = $key;
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

        if (! @get_headers($this->url)) {
            throw new \RuntimeException("[ERROR] Command not valid outside EC2 instance. Please run this command within a running EC2 instance.");
        }

        return true;
    }

    public function get($req, $args)
    {

        $command = $this->commands[$req];
        $args = implode('/', $args);

        if ($args) {
            $args = "/$args";
        }

        return file_get_contents($this->getFullPath() . $command . $args);
    }

    /**
     * UserData is not inside '/meta-data', so we need to declare it explicitly
     *
     * @return string
     */
    public function getUserData()
    {

        return file_get_contents($this->url . '/latest/' . $this->commands['UserData']);
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

        return sprintf("%s://%s/%s/", $this->protocol, $this->hostname, $this->path);
    }

    public function __call($fn, $args)
    {

        if (strpos($fn, 'get') !== 0) {
            throw new \LogicException("Only get operations allowed.");
        }

        // Remove 'get'
        $req = substr($fn, 3);

        if ($this->exists($req)) {
            return $this->get($req, $args);
        }
    }
}