# Namespaces, interfaces and classes renamed in Predis v0.8 #
____________________________________________

Some namespaces, interfaces and classes in Predis v0.8 have been renamed to follow a common rule inspired
by the naming conventions adopted by the Symfony2 project. This is a list of all the changes:

### Renamed namespaces ###

  - `Predis\Network` => `Predis\Connection`
  - `Predis\Profiles` => `Predis\Profile`
  - `Predis\Iterators` => `Predis\Iterator`
  - `Predis\Options` => `Predis\Option`
  - `Predis\Commands` => `Predis\Command`
  - `Predis\Commands\Processors` => `Predis\Command\Processor`

### Renamed interfaces ###

  - `Predis\IReplyObject` => `Predis\ResponseObjectInterface`
  - `Predis\IRedisServerError` => `Predis\ResponseErrorInterface`
  - `Predis\Options\IOption` => `Predis\Option\OptionInterface`
  - `Predis\Options\IClientOptions` => `Predis\Option\ClientOptionsInterface`
  - `Predis\Profile\IServerProfile` => `Predis\Profile\ServerProfileInterface`
  - `Predis\Pipeline\IPipelineExecutor` => `Predis\Pipeline\PipelineExecutorInterface`
  - `Predis\Distribution\INodeKeyGenerator` => `Predis\Distribution\HashGeneratorInterface`
  - `Predis\Distribution\IDistributionStrategy` => `Predis\Distribution\DistributionStrategyInterface`
  - `Predis\Protocol\IProtocolProcessor` => `Predis\Protocol\ProtocolInterface`
  - `Predis\Protocol\IResponseReader` => `Predis\Protocol\ResponseReaderInterface`
  - `Predis\Protocol\IResponseHandler` => `Predis\Protocol\ResponseHandlerInterface`
  - `Predis\Protocol\ICommandSerializer` => `Predis\Protocol\CommandSerializerInterface`
  - `Predis\Protocol\IComposableProtocolProcessor` => `Predis\Protocol\ComposableProtocolInterface`
  - `Predis\Network\IConnection` => `Predis\Connection\ConnectionInterface`
  - `Predis\Network\IConnectionSingle` => `Predis\Connection\SingleConnectionInterface`
  - `Predis\Network\IConnectionComposable` => `Predis\Connection\ComposableConnectionInterface`
  - `Predis\Network\IConnectionCluster` => `Predis\Connection\ClusterConnectionInterface`
  - `Predis\Network\IConnectionReplication` => `Predis\Connection\ReplicationConnectionInterface`
  - `Predis\Commands\ICommand` => `Predis\Command\CommandInterface`
  - `Predis\Commands\IPrefixable` => `Predis\Command\PrefixableCommandInterface`
  - `Predis\Command\Processor\ICommandProcessor` => `Predis\Command\Processor\CommandProcessorInterface`
  - `Predis\Command\Processor\ICommandProcessorChain` => `Predis\Command\Processor\CommandProcessorChainInterface`
  - `Predis\Command\Processor\IProcessingSupport` => `Predis\Command\Processor\CommandProcessingInterface`

### Renamed classes ###

  - `Predis\Commands\Command` => `Predis\Command\AbstractCommand`
  - `Predis\Network\ConnectionBase` => `Predis\Connection\AbstractConnection`

### Classes or interfaces moved to different namespaces ###

  - `Predis\MonitorContext` => `Predis\Monitor\MonitorContext`
  - `Predis\ConnectionParameters` => `Predis\Connection\ConnectionParameters`
  - `Predis\ConnectionParametersInterface` => `Predis\Connection\ConnectionParametersInterface`
  - `Predis\ConnectionFactory` => `Predis\Connection\ConnectionFactory`
  - `Predis\ConnectionFactoryInterface` => `Predis\Connection\ConnectionFactoryInterface`
