<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Profiler;

/**
 * Storage for profiler using files.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class FileProfilerStorage implements ProfilerStorageInterface
{
    /**
     * Folder where profiler data are stored.
     */
    private string $folder;

    /**
     * Constructs the file storage using a "dsn-like" path.
     *
     * Example : "file:/path/to/the/storage/folder"
     *
     * @throws \RuntimeException
     */
    public function __construct(string $dsn)
    {
        if (!str_starts_with($dsn, 'file:')) {
            throw new \RuntimeException(\sprintf('Please check your configuration. You are trying to use FileStorage with an invalid dsn "%s". The expected format is "file:/path/to/the/storage/folder".', $dsn));
        }
        $this->folder = substr($dsn, 5);

        if (!is_dir($this->folder) && false === @mkdir($this->folder, 0o777, true) && !is_dir($this->folder)) {
            throw new \RuntimeException(\sprintf('Unable to create the storage directory (%s).', $this->folder));
        }
    }

    public function find(?string $ip, ?string $url, ?int $limit, ?string $method, ?int $start = null, ?int $end = null, ?string $statusCode = null, ?\Closure $filter = null): array
    {
        $file = $this->getIndexFilename();

        if (!file_exists($file)) {
            return [];
        }

        $file = fopen($file, 'r');
        fseek($file, 0, \SEEK_END);

        $result = [];
        while (\count($result) < $limit && $line = $this->readLineFromFile($file)) {
            $values = str_getcsv($line, ',', '"', '\\');

            if (7 > \count($values)) {
                // skip invalid lines
                continue;
            }

            [$csvToken, $csvIp, $csvMethod, $csvUrl, $csvTime, $csvParent, $csvStatusCode, $csvVirtualType] = $values + [7 => null];
            $csvTime = (int) $csvTime;

            $urlFilter = false;
            if ($url) {
                $urlFilter = str_starts_with($url, '!') ? str_contains($csvUrl, substr($url, 1)) : !str_contains($csvUrl, $url);
            }

            if ($ip && !str_contains($csvIp, $ip) || $urlFilter || $method && !str_contains($csvMethod, $method) || $statusCode && !str_contains($csvStatusCode, $statusCode)) {
                continue;
            }

            if ($start && $csvTime < $start) {
                continue;
            }

            if ($end && $csvTime > $end) {
                continue;
            }

            $profile = [
                'token' => $csvToken,
                'ip' => $csvIp,
                'method' => $csvMethod,
                'url' => $csvUrl,
                'time' => $csvTime,
                'parent' => $csvParent,
                'status_code' => $csvStatusCode,
                'virtual_type' => $csvVirtualType ?: 'request',
            ];

            if ($filter && !$filter($profile)) {
                continue;
            }

            $result[$csvToken] = $profile;
        }

        fclose($file);

        return array_values($result);
    }

    public function purge(): void
    {
        $flags = \FilesystemIterator::SKIP_DOTS;
        $iterator = new \RecursiveDirectoryIterator($this->folder, $flags);
        $iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $file) {
            if (is_file($file)) {
                unlink($file);
            } else {
                rmdir($file);
            }
        }
    }

    public function read(string $token): ?Profile
    {
        return $this->doRead($token);
    }

    /**
     * @throws \RuntimeException
     */
    public function write(Profile $profile): bool
    {
        $file = $this->getFilename($profile->getToken());

        $profileIndexed = is_file($file);
        if (!$profileIndexed) {
            // Create directory
            $dir = \dirname($file);
            if (!is_dir($dir) && false === @mkdir($dir, 0o777, true) && !is_dir($dir)) {
                throw new \RuntimeException(\sprintf('Unable to create the storage directory (%s).', $dir));
            }
        }

        $profileToken = $profile->getToken();
        // when there are errors in sub-requests, the parent and/or children tokens
        // may equal the profile token, resulting in infinite loops
        $parentToken = $profile->getParentToken() !== $profileToken ? $profile->getParentToken() : null;
        $childrenToken = array_filter(array_map(static fn (Profile $p) => $profileToken !== $p->getToken() ? $p->getToken() : null, $profile->getChildren()));

        // Store profile
        $data = [
            'token' => $profileToken,
            'parent' => $parentToken,
            'children' => $childrenToken,
            'data' => $profile->getCollectors(),
            'ip' => $profile->getIp(),
            'method' => $profile->getMethod(),
            'url' => $profile->getUrl(),
            'time' => $profile->getTime(),
            'status_code' => $profile->getStatusCode(),
            'virtual_type' => $profile->getVirtualType() ?? 'request',
        ];

        $data = serialize($data);

        if (\function_exists('gzencode')) {
            $data = gzencode($data, 3);
        }

        if (false === file_put_contents($file, $data, \LOCK_EX)) {
            return false;
        }

        if (!$profileIndexed) {
            // Add to index
            if (false === $file = fopen($this->getIndexFilename(), 'a')) {
                return false;
            }

            fputcsv($file, [
                $profile->getToken(),
                $profile->getIp(),
                $profile->getMethod(),
                $profile->getUrl(),
                $profile->getTime() ?: time(),
                $profile->getParentToken(),
                $profile->getStatusCode(),
                $profile->getVirtualType() ?? 'request',
            ], ',', '"', '\\');
            fclose($file);

            if (1 === random_int(1, 10)) {
                $this->removeExpiredProfiles();
            }
        }

        return true;
    }

    /**
     * Gets filename to store data, associated to the token.
     */
    protected function getFilename(string $token): string
    {
        // Uses 4 last characters, because first are mostly the same.
        $folderA = substr($token, -2, 2);
        $folderB = substr($token, -4, 2);

        return $this->folder.'/'.$folderA.'/'.$folderB.'/'.$token;
    }

    /**
     * Gets the index filename.
     */
    protected function getIndexFilename(): string
    {
        return $this->folder.'/index.csv';
    }

    /**
     * Reads a line in the file, backward.
     *
     * This function automatically skips the empty lines and do not include the line return in result value.
     *
     * @param resource $file The file resource, with the pointer placed at the end of the line to read
     */
    protected function readLineFromFile($file): mixed
    {
        $line = '';
        $position = ftell($file);

        if (0 === $position) {
            return null;
        }

        while (true) {
            $chunkSize = min($position, 1024);
            $position -= $chunkSize;
            fseek($file, $position);

            if (0 === $chunkSize) {
                // bof reached
                break;
            }

            $buffer = fread($file, $chunkSize);

            if (false === ($upTo = strrpos($buffer, "\n"))) {
                $line = $buffer.$line;
                continue;
            }

            $position += $upTo;
            $line = substr($buffer, $upTo + 1).$line;
            fseek($file, max(0, $position), \SEEK_SET);

            if ('' !== $line) {
                break;
            }
        }

        return '' === $line ? null : $line;
    }

    protected function createProfileFromData(string $token, array $data, ?Profile $parent = null): Profile
    {
        $profile = new Profile($token);
        $profile->setIp($data['ip']);
        $profile->setMethod($data['method']);
        $profile->setUrl($data['url']);
        $profile->setTime($data['time']);
        $profile->setStatusCode($data['status_code']);
        $profile->setVirtualType($data['virtual_type'] ?: 'request');
        $profile->setCollectors($data['data']);

        if (!$parent && $data['parent']) {
            $parent = $this->read($data['parent']);
        }

        if ($parent) {
            $profile->setParent($parent);
        }

        foreach ($data['children'] as $token) {
            if (null !== $childProfile = $this->doRead($token, $profile)) {
                $profile->addChild($childProfile);
            }
        }

        return $profile;
    }

    private function doRead($token, ?Profile $profile = null): ?Profile
    {
        if (!$token || !file_exists($file = $this->getFilename($token))) {
            return null;
        }

        $h = fopen($file, 'r');
        flock($h, \LOCK_SH);
        $data = stream_get_contents($h);
        flock($h, \LOCK_UN);
        fclose($h);

        if (\function_exists('gzdecode')) {
            $data = @gzdecode($data) ?: $data;
        }

        if (!$data = unserialize($data, ['allowed_classes' => true])) {
            return null;
        }

        return $this->createProfileFromData($token, $data, $profile);
    }

    private function removeExpiredProfiles(): void
    {
        $minimalProfileTimestamp = time() - 2 * 86400;
        $file = $this->getIndexFilename();
        $handle = fopen($file, 'r');

        if ($offset = is_file($file.'.offset') ? (int) file_get_contents($file.'.offset') : 0) {
            fseek($handle, $offset);
        }

        while ($line = fgets($handle)) {
            $values = str_getcsv($line, ',', '"', '\\');

            if (7 > \count($values)) {
                // skip invalid lines
                $offset += \strlen($line);
                continue;
            }

            [$csvToken, , , , $csvTime] = $values;

            if ($csvTime >= $minimalProfileTimestamp) {
                break;
            }

            @unlink($this->getFilename($csvToken));
            $offset += \strlen($line);
        }
        fclose($handle);

        file_put_contents($file.'.offset', $offset);
    }
}
