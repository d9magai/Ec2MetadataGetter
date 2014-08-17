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

        foreach ($metadata as $key => $val) {
            $file = \org\bovigo\vfs\vfsStream::newFile(sprintf("%s/%s", $this->path, $key));
            $file->write($val);
            $vfsRoot->addChild($file);
        }
    }
}