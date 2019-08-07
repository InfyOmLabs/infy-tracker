<?php

namespace App\Filesystem\Adapter;

class Ftp extends \League\Flysystem\Adapter\Ftp
{
    public function getUrl($path)
    {
        return config('filesystems.disks.ftp.image_domain') . $path;
    }
}