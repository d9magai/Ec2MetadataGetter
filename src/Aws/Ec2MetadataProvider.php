<?php
namespace Aws;

class Ec2MetadataProvider
{
	private static $instance;
	
	private $path = '/latest/meta-data/';
	private $url = 'http://169.254.169.254';
	
	private $commands = array(
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
	);
	
	public static function Factory()
	{
		if (!self::instance)
		{
			self::$instance = new self;
		}
		
		return self::$instance;
	}
	
	private function __construct() {}
	
	public function chkConfig()
	{
		if (!@get_headers($this->url)) {
			throw new \RuntimeException("[ERROR] Command not valid outside EC2 instance. Please run this command within a running EC2 instance.");
		}
	
		return true;
	}
	
	public function get($req, $args)
	{
		$command = $this->commands[$req];
		$args = implode('/', $args);
	
		if ($args)
		{
			$args = "/$args";
		}
	
		return file_get_contents($this->getFullPath() . $command . $args);
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
		return $this->url . $this->path;
	}
}