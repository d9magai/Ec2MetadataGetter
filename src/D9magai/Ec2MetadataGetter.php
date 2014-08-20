<?php

namespace D9magai;

/**
 * Ec2MetadataGetter uses file_get_contents to query the EC2 instance Metadata from within a running EC2 instance.
 *
 * see:http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/AESDG-chapter-instancedata.html
 */
class Ec2MetadataGetter
{

    /**
     * read from http scheme
     *
     * @var string
     */
    protected $scheme = 'http';

    /**
     * to view instance metadata from within a running instance, use this
     *
     * @var string
     */
    protected $hostname = '169.254.169.254';

    /**
     * lookup table of command and meta-data destination
     *
     * @var array
     */
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

    /**
     * http connections time out after 0.1 seconds
     *
     * @var float
     */
    const HTTP_TIMEOUT = 0.1;

    /**
     * be used when assembling metadata path
     *
     * @var string
     */
    const METADATA = 'meta-data';

    /**
     * when not available metadata, display this message.
     *
     * @var string
     */
    const NOT_AVAILABLE = 'not available';

    /**
     * e.g.
     * $blockDeviceMapping = [
     *          'ebs0' => 'sda',
     *          'ephemeral0' => 'sdb',
     *          'root' => '/dev/sda1'
     *  ];
     * @return array
     */
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
        $network = [];
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

        $result = [];
        foreach (array_keys($this->commands) as $commandName) {
            $result[$commandName] = $this->{"get$commandName"}();
        }

        return $result;
    }

    public function isRunningOnEc2()
    {

        if (!@file_get_contents($this->getLatestInstanceDataPath(), false, $this->getStreamContext(), 1, 1)) {
            throw new \RuntimeException("[ERROR] Command not valid outside EC2 instance. Please run this command within a running EC2 instance.");
        }

        return true;
    }

    public function get($commandName, $args = '')
    {

        $response = @file_get_contents($this->getFullPath($commandName, $args), false, $this->getStreamContext());
        return $response === false ? self::NOT_AVAILABLE : $response;
    }

    private function getFullPath($commandName, $args)
    {

        if ($commandName === 'UserData') {
            return sprintf("%s/%s", $this->getLatestInstanceDataPath(), $this->commands['UserData']);
        }
        return sprintf("%s/%s/%s/%s", $this->getLatestInstanceDataPath(), self::METADATA, $this->commands[$commandName], $args);
    }

    /**
     * get latest instance data path combined scheme
     *
     * @return string
     */
    private function getLatestInstanceDataPath()
    {

        return sprintf("%s://%s/latest", $this->scheme, $this->hostname);
    }

    /**
     * get stream_context with setting timeout of http connection
     *
     * @return resource
     */
    private function getStreamContext()
    {

        return stream_context_create([
                'http' => [
                        'timeout' => self::HTTP_TIMEOUT
                ]
        ]);
    }

    public function __call($functionName, $args)
    {

        $command = preg_replace('/^get/', '', $functionName);
        if (!array_key_exists($command, $this->commands)) {
            throw new \LogicException("Only get operations allowed.");
        }
        return $this->get($command, array_pop($args));
    }

}
