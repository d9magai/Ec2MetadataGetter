<?php

namespace Aws\Mock;

class VirtualEc2Metadata extends \Aws\Ec2Metadata
{

    protected $protocol = 'vfs';

    public function __construct(array $metadata)
    {

        $vfsRoot = \org\bovigo\vfs\vfsStream::setup($this->hostname);
        $vfsRoot->addChild(\org\bovigo\vfs\vfsStream::newDirectory($this->path));

        if (array_key_exists('placement', $metadata)) {
            $metadata['placement/availability-zone'] = $metadata['placement'];
            unset($metadata['placement']);
        }

        if (array_key_exists('block-device-mapping', $metadata)) {

            $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/block-device-mapping", $this->path));
            $file->write(implode(PHP_EOL, array_keys($metadata['block-device-mapping'])));
            $vfsRoot->addChild($file);

            foreach ($metadata['block-device-mapping'] as $key => $val) {
                $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/block-device-mapping/%s", $this->path, $key));
                $file->write($val);
                $vfsRoot->addChild($file);
            }
            unset($metadata['block-device-mapping']);
        }

        if (array_key_exists('public-keys', $metadata)) {

            $publicKeysPath = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/public-keys", $this->path));
            $publicKeysList = [];
            foreach ($metadata['public-keys'] as $publicKey) {
                $publicKeysList[] = sprintf("%s=%s", $publicKey['index'], $publicKey['keyname']);

                $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/public-keys/%s", $this->path, $publicKey['index']));
                $file->write($publicKey['format']);
                $vfsRoot->addChild($file);

                $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/public-keys/%s/%s", $this->path, $publicKey['index'], $publicKey['format']));
                $file->write($publicKey['key']);
                $vfsRoot->addChild($file);
            }
            $publicKeysPath->write(implode(PHP_EOL, $publicKeysList));
            $vfsRoot->addChild($publicKeysPath);

            unset($metadata['public-keys']);
        }

        if (array_key_exists('network/interfaces/macs', $metadata)) {

            $networkMacsPath = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/network/interfaces/macs", $this->path));
            foreach ($metadata['network/interfaces/macs'] as $mac => $elements) {
                $networkMacsPath->write($mac);
                $vfsRoot->addChild($networkMacsPath);

                $networkMacAddressPath = sprintf("%s/network/interfaces/macs/%s", $this->path, $mac);
                $file = \org\bovigo\vfs\vfsStream::newFile($networkMacAddressPath);
                $file->write(implode(PHP_EOL, array_keys($elements)));
                $vfsRoot->addChild($file);

                foreach ($elements as $key => $val) {
                    $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/%s", $networkMacAddressPath, $key));
                    $file->write($val);
                    $vfsRoot->addChild($file);
                }
            }

            unset($metadata['network/interfaces/macs']);
        }

        foreach ($metadata as $key => $val) {
            $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/%s", $this->path, $key));
            $file->write($val);
            $vfsRoot->addChild($file);
        }
    }
}
