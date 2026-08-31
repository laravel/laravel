<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

use Monolog\JsonSerializableDateTimeImmutable;
use Monolog\Utils;
use Throwable;
use Monolog\LogRecord;

/**
 * Normalizes incoming records to remove objects/resources so it's easier to dump to various targets
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class NormalizerFormatter implements FormatterInterface
{
    public const SIMPLE_DATE = "Y-m-d\TH:i:sP";

    protected string $dateFormat;
    protected int $maxNormalizeDepth = 9;
    protected int $maxNormalizeItemCount = 1000;

    private int $jsonEncodeOptions = Utils::DEFAULT_JSON_FLAGS;

    protected string $basePath = '';

    /**
     * @param string|null $dateFormat The format of the timestamp: one supported by DateTime::format
     */
    public function __construct(?string $dateFormat = null)
    {
        $this->dateFormat = null === $dateFormat ? static::SIMPLE_DATE : $dateFormat;
    }

    /**
     * @inheritDoc
     */
    public function format(LogRecord $record)
    {
        return $this->normalizeRecord($record);
    }

    /**
     * Normalize an arbitrary value to a scalar|array|null
     *
     * @return null|scalar|array<mixed[]|scalar|null>
     */
    public function normalizeValue(mixed $data): mixed
    {
        return $this->normalize($data);
    }

    /**
     * @inheritDoc
     */
    public function formatBatch(array $records)
    {
        foreach ($records as $key => $record) {
            $records[$key] = $this->format($record);
        }

        return $records;
    }

    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    /**
     * @return $this
     */
    public function setDateFormat(string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    /**
     * The maximum number of normalization levels to go through
     */
    public function getMaxNormalizeDepth(): int
    {
        return $this->maxNormalizeDepth;
    }

    /**
     * @return $this
     */
    public function setMaxNormalizeDepth(int $maxNormalizeDepth): self
    {
        $this->maxNormalizeDepth = $maxNormalizeDepth;

        return $this;
    }

    /**
     * The maximum number of items to normalize per level
     */
    public function getMaxNormalizeItemCount(): int
    {
        return $this->maxNormalizeItemCount;
    }

    /**
     * @return $this
     */
    public function setMaxNormalizeItemCount(int $maxNormalizeItemCount): self
    {
        $this->maxNormalizeItemCount = $maxNormalizeItemCount;

        return $this;
    }

    /**
     * Enables `json_encode` pretty print.
     *
     * @return $this
     */
    public function setJsonPrettyPrint(bool $enable): self
    {
        if ($enable) {
            $this->jsonEncodeOptions |= JSON_PRETTY_PRINT;
        } else {
            $this->jsonEncodeOptions &= ~JSON_PRETTY_PRINT;
        }

        return $this;
    }

    /**
     * Setting a base path will hide the base path from exception and stack trace file names to shorten them
     * @return $this
     */
    public function setBasePath(string $path = ''): self
    {
        if ($path !== '') {
            $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        $this->basePath = $path;

        return $this;
    }

    /**
     * Provided as extension point
     *
     * Because normalize is called with sub-values of context data etc, normalizeRecord can be
     * extended when data needs to be appended on the record array but not to other normalized data.
     *
     * @return array<mixed[]|scalar|null>
     */
    protected function normalizeRecord(LogRecord $record): array
    {
        /** @var array<mixed[]|scalar|null> $normalized */
        $normalized = $this->normalize($record->toArray());

        return $normalized;
    }

    /**
     * @return null|scalar|array<mixed[]|scalar|null>
     */
    protected function normalize(mixed $data, int $depth = 0): mixed
    {
        if ($depth > $this->maxNormalizeDepth) {
            return 'Over ' . $this->maxNormalizeDepth . ' levels deep, aborting normalization';
        }

        if (null === $data || \is_scalar($data)) {
            if (\is_float($data)) {
                if (is_infinite($data)) {
                    return ($data > 0 ? '' : '-') . 'INF';
                }
                if (is_nan($data)) {
                    return 'NaN';
                }
            }

            return $data;
        }

        if (\is_array($data)) {
            $normalized = [];

            $count = 1;
            foreach ($data as $key => $value) {
                if ($count++ > $this->maxNormalizeItemCount) {
                    $normalized['...'] = 'Over ' . $this->maxNormalizeItemCount . ' items ('.\count($data).' total), aborting normalization';
                    break;
                }

                $normalized[$key] = $this->normalize($value, $depth + 1);
            }

            return $normalized;
        }

        if ($data instanceof \DateTimeInterface) {
            return $this->formatDate($data);
        }

        if (\is_object($data)) {
            if ($data instanceof Throwable) {
                return $this->normalizeException($data, $depth);
            }

            if ($data instanceof \JsonSerializable) {
                /** @var null|scalar|array<mixed[]|scalar|null> $value */
                $value = $data->jsonSerialize();
            } elseif (\get_class($data) === '__PHP_Incomplete_Class') {
                $accessor = new \ArrayObject($data);
                $value = (string) $accessor['__PHP_Incomplete_Class_Name'];
            } elseif (method_exists($data, '__toString')) {
                try {
                    /** @var string $value */
                    $value = $data->__toString();
                } catch (\Throwable) {
                    // if the toString method is failing, use the default behavior
                    /** @var null|scalar|array<mixed[]|scalar|null> $value */
                    $value = json_decode($this->toJson($data, true), true);
                }
            } else {
                // the rest is normalized by json encoding and decoding it
                /** @var null|scalar|array<mixed[]|scalar|null> $value */
                $value = json_decode($this->toJson($data, true), true);
            }

            return [Utils::getClass($data) => $value];
        }

        if (\is_resource($data)) {
            return sprintf('[resource(%s)]', get_resource_type($data));
        }

        return '[unknown('.\gettype($data).')]';
    }

    /**
     * @return array<array-key, string|int|array<string|int|array<string>>>
     */
    protected function normalizeException(Throwable $e, int $depth = 0)
    {
        if ($depth > $this->maxNormalizeDepth) {
            return ['Over ' . $this->maxNormalizeDepth . ' levels deep, aborting normalization'];
        }

        if ($e instanceof \JsonSerializable) {
            return (array) $e->jsonSerialize();
        }

        $file = $e->getFile();
        if ($this->basePath !== '') {
            $file = preg_replace('{^'.preg_quote($this->basePath).'}', '', $file);
        }

        $data = [
            'class' => Utils::getClass($e),
            'message' => $e->getMessage(),
            'code' => (int) $e->getCode(),
            'file' => $file.':'.$e->getLine(),
        ];

        if ($e instanceof \SoapFault) {
            if (isset($e->faultcode)) {
                $data['faultcode'] = $e->faultcode;
            }

            if (isset($e->faultactor)) {
                $data['faultactor'] = $e->faultactor;
            }

            if (isset($e->detail)) {
                if (\is_string($e->detail)) {
                    $data['detail'] = $e->detail;
                } elseif (\is_object($e->detail) || \is_array($e->detail)) {
                    $data['detail'] = $this->toJson($e->detail, true);
                }
            }
        }

        $trace = $e->getTrace();
        foreach ($trace as $frame) {
            if (isset($frame['file'], $frame['line'])) {
                $file = $frame['file'];
                if ($this->basePath !== '') {
                    $file = preg_replace('{^'.preg_quote($this->basePath).'}', '', $file);
                }
                $data['trace'][] = $file.':'.$frame['line'];
            }
        }

        if (($previous = $e->getPrevious()) instanceof \Throwable) {
            $data['previous'] = $this->normalizeException($previous, $depth + 1);
        }

        return $data;
    }

    /**
     * Return the JSON representation of a value
     *
     * @param  mixed             $data
     * @throws \RuntimeException if encoding fails and errors are not ignored
     * @return string            if encoding fails and ignoreErrors is true 'null' is returned
     */
    protected function toJson($data, bool $ignoreErrors = false): string
    {
        return Utils::jsonEncode($data, $this->jsonEncodeOptions, $ignoreErrors);
    }

    protected function formatDate(\DateTimeInterface $date): string
    {
        // in case the date format isn't custom then we defer to the custom JsonSerializableDateTimeImmutable
        // formatting logic, which will pick the right format based on whether useMicroseconds is on
        if ($this->dateFormat === self::SIMPLE_DATE && $date instanceof JsonSerializableDateTimeImmutable) {
            return (string) $date;
        }

        return $date->format($this->dateFormat);
    }

    /**
     * @return $this
     */
    public function addJsonEncodeOption(int $option): self
    {
        $this->jsonEncodeOptions |= $option;

        return $this;
    }

    /**
     * @return $this
     */
    public function removeJsonEncodeOption(int $option): self
    {
        $this->jsonEncodeOptions &= ~$option;

        return $this;
    }
}
