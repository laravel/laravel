<?php

namespace Illuminate\Queue\Connectors;

use Aws\Credentials\CredentialProvider;
use Aws\Sqs\SqsClient;
use Illuminate\Queue\SqsQueue;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class SqsConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        $config = $this->getDefaultConfiguration($config);

        if ($credentials = $this->resolveCredentialProvider($config)) {
            $config['credentials'] = $credentials;
        } elseif (! empty($config['key']) && ! empty($config['secret'])) {
            $config['credentials'] = Arr::only($config, ['key', 'secret']);

            if (! empty($config['token'])) {
                $config['credentials']['token'] = $config['token'];
            }
        }

        return new SqsQueue(
            new SqsClient(
                Arr::except($config, ['token'])
            ),
            $config['queue'],
            $config['prefix'] ?? '',
            $config['suffix'] ?? '',
            $config['after_commit'] ?? null
        );
    }

    /**
     * Resolve a credential provider from the given config.
     *
     * @param  array  $config
     * @return callable|null
     *
     * @throws \InvalidArgumentException
     */
    protected function resolveCredentialProvider(array $config)
    {
        $credentials = $config['credentials'] ?? null;

        $provider = is_array($credentials) ? ($credentials['provider'] ?? null) : $credentials;

        if (! is_string($provider)) {
            return $provider;
        }

        $options = is_array($credentials) ? Arr::except($credentials, ['provider']) : [];

        $resolved = match ($provider) {
            'ecs' => CredentialProvider::ecsCredentials($options),
            'instance' => CredentialProvider::instanceProfile($options),
            default => throw new InvalidArgumentException(
                "Invalid credential provider [{$provider}]."
            ),
        };

        return CredentialProvider::memoize($resolved);
    }

    /**
     * Get the default configuration for SQS.
     *
     * @param  array  $config
     * @return array
     */
    protected function getDefaultConfiguration(array $config)
    {
        return array_merge([
            'version' => 'latest',
            'http' => [
                'timeout' => 60,
                'connect_timeout' => 60,
            ],
        ], $config);
    }
}
