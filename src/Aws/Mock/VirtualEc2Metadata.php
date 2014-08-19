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

            $publicKeysPath = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/public-keys", $this->path));
            $publicKeysList = [];
            foreach ($metadata['public-keys'] as $publicKey) {
                $publicKeysList[] = sprintf("%s=%s", $publicKey['index'], $publicKey['keyname']);

                $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/public-keys/%s", $this->path, $publicKey['index']));
                $file->write($publicKey['format']);
                $this->vfsRoot->addChild($file);

                $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/public-keys/%s/%s", $this->path, $publicKey['index'], $publicKey['format']));
                $file->write($publicKey['key']);
                $this->vfsRoot->addChild($file);
            }
            $publicKeysPath->write(implode(PHP_EOL, $publicKeysList));
            $this->vfsRoot->addChild($publicKeysPath);

            unset($metadata['public-keys']);
        }

        if (array_key_exists('network/interfaces/macs', $metadata)) {

            $networkMacsPath = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/network/interfaces/macs", $this->path));
            $networkMacsList = [];
            foreach ($metadata['network/interfaces/macs'] as $mac => $elements) {
                $networkMacsList[] = $mac;
                $networkMacAddressPath = sprintf("%s/network/interfaces/macs/%s", $this->path, $mac);
                $file = \org\bovigo\vfs\vfsStream::newFile($networkMacAddressPath);
                $file->write(implode(PHP_EOL, array_keys($elements)));
                $this->vfsRoot->addChild($file);

                foreach ($elements as $key => $val) {
                    $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/%s", $networkMacAddressPath, $key));
                    $file->write($val);
                    $this->vfsRoot->addChild($file);
                }
            }
            $networkMacsPath->write(implode(PHP_EOL, $networkMacsList));
            $this->vfsRoot->addChild($networkMacsPath);

            unset($metadata['network/interfaces/macs']);
        }

        foreach ($metadata as $key => $val) {
            $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/%s", $this->path, $key));
            $file->write($val);
            $this->vfsRoot->addChild($file);
        }
    }

    private function writeBlockDeviceMappingToVfs(array $blockDeviceMapping)
    {

        $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/block-device-mapping", $this->path));
        $file->write(implode(PHP_EOL, array_keys($blockDeviceMapping)));
        $this->vfsRoot->addChild($file);

        foreach ($blockDeviceMapping as $key => $val) {
            $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/block-device-mapping/%s", $this->path, $key));
            $file->write($val);
            $this->vfsRoot->addChild($file);
        }
    }

}
