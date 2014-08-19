<?php

namespace Aws\Mock;

class VirtualEc2Metadata extends \Aws\Ec2Metadata
{

    protected $protocol = 'vfs';

    private $vfsRoot;

    public function __construct(array $metadata)
    {

        $this->vfsRoot = \org\bovigo\vfs\vfsStream::setup($this->hostname);
        $this->vfsRoot->addChild(\org\bovigo\vfs\vfsStream::newDirectory($this->path));

        if (array_key_exists('block-device-mapping', $metadata)) {

            $this->writeBlockDeviceMappingToVfs($metadata['block-device-mapping']);
            unset($metadata['block-device-mapping']);
        }

        if (array_key_exists('public-keys', $metadata)) {

            $this->writePublicKeysToVfs($metadata['public-keys']);
            unset($metadata['public-keys']);
        }

        if (array_key_exists('network/interfaces/macs', $metadata)) {

            $this->writeNetworkToVfs($metadata['network/interfaces/macs']);
            unset($metadata['network/interfaces/macs']);
        }

        $this->writeArrayToVfs($metadata, $this->path);
    }

    private function writeArrayToVfs(array $metadata, $path)
    {

        foreach ($metadata as $key => $val) {
            $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/%s", $path, $key));
            $file->write($val);
            $this->vfsRoot->addChild($file);
        }
    }

    private function writeBlockDeviceMappingToVfs(array $blockDeviceMapping)
    {

        $blockDeviceMappingPath = sprintf("%s/block-device-mapping", $this->path);
        $file = \org\bovigo\vfs\vfsStream::newFile($blockDeviceMappingPath);
        $file->write(implode(PHP_EOL, array_keys($blockDeviceMapping)));
        $this->vfsRoot->addChild($file);

        $this->writeArrayToVfs($blockDeviceMapping, $blockDeviceMappingPath);
    }

    private function writePublicKeysToVfs(array $publicKeys)
    {

        $publicKeysFile = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/public-keys", $this->path));
        $publicKeysList = [];
        foreach ($publicKeys as $publicKey) {
            $publicKeysList[] = sprintf("%s=%s", $publicKey['index'], $publicKey['keyname']);

            $indexFile = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/public-keys/%s", $this->path, $publicKey['index']));
            $indexFile->write($publicKey['format']);
            $this->vfsRoot->addChild($indexFile);

            $formatFile = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/public-keys/%s/%s", $this->path, $publicKey['index'], $publicKey['format']));
            $formatFile->write($publicKey['key']);
            $this->vfsRoot->addChild($formatFile);
        }
        $publicKeysFile->write(implode(PHP_EOL, $publicKeysList));
        $this->vfsRoot->addChild($publicKeysFile);
    }

    private function writeNetworkToVfs(array $network)
    {

        $macsFile = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/network/interfaces/macs", $this->path));
        $macsList = [];
        foreach ($network as $mac => $elements) {
            $macsList[] = $mac;
            $macAddressPath = sprintf("%s/network/interfaces/macs/%s", $this->path, $mac);
            $file = \org\bovigo\vfs\vfsStream::newFile($macAddressPath);
            $file->write(implode(PHP_EOL, array_keys($elements)));
            $this->vfsRoot->addChild($file);

            foreach ($elements as $key => $val) {
                $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/%s", $macAddressPath, $key));
                $file->write($val);
                $this->vfsRoot->addChild($file);
            }
        }
        $macsFile->write(implode(PHP_EOL, $macsList));
        $this->vfsRoot->addChild($macsFile);
    }

}
