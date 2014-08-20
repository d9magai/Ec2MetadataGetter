<?php

namespace D9magai\Mock;

/**
 * VirtualEc2MetadataGetter queries the EC2 instance Metadata from Virtual File System.
 * Using for PHPUnit.
 *
 * @author d9magai
 */
class VirtualEc2MetadataGetter extends \D9magai\Ec2MetadataGetter
{

    /**
     * read from vfs protocol
     *
     * @var string
     */
    protected $protocol = 'vfs';

    /**
     * vfsRoot is $this->hostname
     *
     * @var string
     */
    private $vfsRoot;

    /**
     * write metadata to Virtual File System
     *
     * @param array $metadata
     * @param string $hostname
     */
    public function __construct(array $metadata, $hostname = '')
    {

        $this->vfsRoot = \org\bovigo\vfs\vfsStream::setup(empty($hostname) ? $this->hostname : $hostname);
        $file = \org\bovigo\vfs\vfsStream::newFile('latest');
        $file->write('dynamic' . PHP_EOL . 'meta-data' . PHP_EOL . 'user-data');
        $this->vfsRoot->addChild($file);

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

        if (array_key_exists('user-data', $metadata)) {

            $file = \org\bovigo\vfs\vfsStream::newFile('latest/user-data');
            $file->write($metadata['user-data']);
            $this->vfsRoot->addChild($file);
            unset($metadata['user-data']);
        }

        $this->writeArrayToVfs($metadata, $this->getMetadataPath());
    }

    /**
     * write array to Virtual File System
     *
     * e.g.
     * $metadata = ['id' => '12345'];
     * $path = vfs://169.254.169.254/latest/metadta;
     *
     * writing '12345' to vfs://169.254.169.254/latest/metadta/id
     *
     * @param array $metadata
     * @param string $path
     */
    private function writeArrayToVfs(array $metadata, $path)
    {

        foreach ($metadata as $key => $val) {
            $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/%s", $path, $key));
            $file->write($val);
            $this->vfsRoot->addChild($file);
        }
    }

    /**
     * wreite block-device-mapping to Virtual File System
     *
     * e.g.
     * $ curl http://169.254.169.254/latest/meta-data/block-device-mapping/
     * ebs0
     * root
     *
     * @param array $blockDeviceMapping
     */
    private function writeBlockDeviceMappingToVfs(array $blockDeviceMapping)
    {

        $blockDeviceMappingPath = sprintf("%s/block-device-mapping", $this->getMetadataPath());
        $file = \org\bovigo\vfs\vfsStream::newFile($blockDeviceMappingPath);
        $file->write(implode(PHP_EOL, array_keys($blockDeviceMapping)));
        $this->vfsRoot->addChild($file);

        $this->writeArrayToVfs($blockDeviceMapping, $blockDeviceMappingPath);
    }

    /**
     * write publick-keys to Virtual File System
     *
     * e.g.
     * $ curl http://169.254.169.254/latest/meta-data/public-keys/
     * 0=my-public-key
     * $ curl http://169.254.169.254/latest/meta-data/public-keys/0
     * openssh-key
     * $ curl http://169.254.169.254/latest/meta-data/public-keys/0/openssh-key
     * ssh-rsa hoge my-public-key
     *
     * @param array $publicKeys
     */
    private function writePublicKeysToVfs(array $publicKeys)
    {

        $publicKeysFile = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/public-keys", $this->getMetadataPath()));
        $publicKeysList = [];
        foreach ($publicKeys as $publicKey) {
            $publicKeysList[] = sprintf("%s=%s", $publicKey['index'], $publicKey['keyname']);

            $indexFile = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/public-keys/%s", $this->getMetadataPath(), $publicKey['index']));
            $indexFile->write($publicKey['format']);
            $this->vfsRoot->addChild($indexFile);

            $formatFile = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/public-keys/%s/%s", $this->getMetadataPath(), $publicKey['index'], $publicKey['format']));
            $formatFile->write($publicKey['key']);
            $this->vfsRoot->addChild($formatFile);
        }
        $publicKeysFile->write(implode(PHP_EOL, $publicKeysList));
        $this->vfsRoot->addChild($publicKeysFile);
    }

    /**
     * wriete network/interfaces/macs to Virtual File System
     *
     * e.g.
     * $ curl http://169.254.169.254/latest/meta-data/network/interfaces/macs/11:22:33:44:55:66/
     * device-number
     * local-hostname
     * local-ipv4s
     * mac
     * owner-id
     * public-hostname
     * public-ipv4s
     *
     * @param array $network
     */
    private function writeNetworkToVfs(array $network)
    {

        $macsFile = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/network/interfaces/macs", $this->getMetadataPath()));
        $macsList = [];
        foreach ($network as $mac => $elements) {
            $macsList[] = $mac;
            $macAddressPath = sprintf("%s/network/interfaces/macs/%s", $this->getMetadataPath(), $mac);
            $file = \org\bovigo\vfs\vfsStream::newFile($macAddressPath);
            $file->write(implode(PHP_EOL, array_keys($elements)));
            $this->vfsRoot->addChild($file);

            $this->writeArrayToVfs($elements, $macAddressPath);
        }
        $macsFile->write(implode(PHP_EOL, $macsList));
        $this->vfsRoot->addChild($macsFile);
    }

    /**
     * get latest meta-data path
     *
     * @return string
     */
    private function getMetadataPath()
    {

        return sprintf("latest/%s", self::METADATA);
    }

}
