<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Processor;

use ArrayAccess;
use Monolog\LogRecord;

/**
 * Injects url/method and remote IP of the current web request in all records
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class WebProcessor implements ProcessorInterface
{
    /**
     * @var array<string, mixed>|ArrayAccess<string, mixed>
     */
    protected array|ArrayAccess $serverData;

    /**
     * Default fields
     *
     * Array is structured as [key in record.extra => key in $serverData]
     *
     * @var array<string, string>
     */
    protected array $extraFields = [
        'url'         => 'REQUEST_URI',
        'ip'          => 'REMOTE_ADDR',
        'http_method' => 'REQUEST_METHOD',
        'server'      => 'SERVER_NAME',
        'referrer'    => 'HTTP_REFERER',
        'user_agent'  => 'HTTP_USER_AGENT',
    ];

    /**
     * @param array<string, mixed>|ArrayAccess<string, mixed>|null $serverData  Array or object w/ ArrayAccess that provides access to the $_SERVER data
     * @param array<string, string>|array<string>|null             $extraFields Field names and the related key inside $serverData to be added (or just a list of field names to use the default configured $serverData mapping). If not provided it defaults to: [url, ip, http_method, server, referrer] + unique_id if present in server data
     */
    public function __construct(array|ArrayAccess|null $serverData = null, array|null $extraFields = null)
    {
        if (null === $serverData) {
            $this->serverData = &$_SERVER;
        } else {
            $this->serverData = $serverData;
        }

        $defaultEnabled = ['url', 'ip', 'http_method', 'server', 'referrer'];
        if (isset($this->serverData['UNIQUE_ID'])) {
            $this->extraFields['unique_id'] = 'UNIQUE_ID';
            $defaultEnabled[] = 'unique_id';
        }

        if (null === $extraFields) {
            $extraFields = $defaultEnabled;
        }
        if (isset($extraFields[0])) {
            foreach (array_keys($this->extraFields) as $fieldName) {
                if (!\in_array($fieldName, $extraFields, true)) {
                    unset($this->extraFields[$fieldName]);
                }
            }
        } else {
            $this->extraFields = $extraFields;
        }
    }

    /**
     * @inheritDoc
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        // skip processing if for some reason request data
        // is not present (CLI or wonky SAPIs)
        if (!isset($this->serverData['REQUEST_URI'])) {
            return $record;
        }

        $record->extra = $this->appendExtraFields($record->extra);

        return $record;
    }

    /**
     * @return $this
     */
    public function addExtraField(string $extraName, string $serverName): self
    {
        $this->extraFields[$extraName] = $serverName;

        return $this;
    }

    /**
     * @param  mixed[] $extra
     * @return mixed[]
     */
    private function appendExtraFields(array $extra): array
    {
        foreach ($this->extraFields as $extraName => $serverName) {
            $extra[$extraName] = $this->serverData[$serverName] ?? null;
        }

        return $extra;
    }
}
