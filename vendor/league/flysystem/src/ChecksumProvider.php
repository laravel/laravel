<?php

namespace League\Flysystem;

interface ChecksumProvider
{
    /**
     * @return string MD5 hash of the file contents
     *
     * @throws UnableToProvideChecksum
     * @throws ChecksumAlgoIsNotSupported
     */
    public function checksum(string $path, Config $config): string;
}
