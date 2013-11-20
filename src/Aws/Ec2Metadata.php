<?php
namespace Aws;

class Ec2Metadata
{
	private $provider;
	
	public function __construct(Ec2MetadataProvider $provider)
	{
		$this->provider = $provider;
	}
	
	public function __call($fn, $args)
	{
		if (strpos($fn, 'get') !== 0) {
			throw new \LogicException("Only get operations allowed.");
		}
		
		// Remove 'get'
		$req = substr($fn, 3);
		
		if ($this->provider->exists($req))
		{
			return $this->provider->get($req, $args);
		}
	}
	
	public function getBlockDeviceMapping()
	{
		$maps = $this->provider->get('block-device-mapping');
		
		$output = array();
		
		foreach (explode(PHP_EOL, $maps) as $map)
		{
			$output[$map] = $this->provider->get('block-device-mapping', array($map));
		}
		
		return $output;
	}
	
	public function getPublicKeys()
	{
		$rawKeys = $this->provider->get('public-keys');

		$keys = array();
		foreach (explode(PHP_EOL, $rawKeys) as $rawKey)
		{
			$parts = explode('=', $rawKey);
			$index = $parts[0];
			$keyname = $parts[1];
			
			$format = $this->provider->get('public-keys', array($index));
			
			$key = array(
				'keyname' => $keyname,
				'index' => $index,
				'format' => $format,
				'key' => $this->provider->get('public-keys', array($index, $format))
			);
			
			$keys[] = $key;
		}
		
		return $keys;
	}
	
	
	public function getAll()
	{
		$output = array();
		
		foreach ($this->provider->getCommands() as $req => $apiArg)
		{
			switch ($req)
			{
				case 'PublicKeys':
					$output[$req] = $this->getPublicKeys();
					break;
				case 'BlockDeviceMapping':
					$output[$req] = $this->getBlockDeviceMapping();
					break;
				default:
					$output[$fn] = $this->provider->get($req);
			}
		}
		
		return $output;
	}
}