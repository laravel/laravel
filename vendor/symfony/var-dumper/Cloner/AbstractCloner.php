<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Cloner;

use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Exception\ThrowingCasterException;

/**
 * AbstractCloner implements a generic caster mechanism for objects and resources.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractCloner implements ClonerInterface
{
    public static array $defaultCasters = [
        '__PHP_Incomplete_Class' => ['Symfony\Component\VarDumper\Caster\Caster', 'castPhpIncompleteClass'],

        'AddressInfo' => ['Symfony\Component\VarDumper\Caster\AddressInfoCaster', 'castAddressInfo'],
        'Socket' => ['Symfony\Component\VarDumper\Caster\SocketCaster', 'castSocket'],

        'Symfony\Component\VarDumper\Caster\CutStub' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'castStub'],
        'Symfony\Component\VarDumper\Caster\CutArrayStub' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'castCutArray'],
        'Symfony\Component\VarDumper\Caster\ConstStub' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'castStub'],
        'Symfony\Component\VarDumper\Caster\EnumStub' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'castEnum'],
        'Symfony\Component\VarDumper\Caster\ScalarStub' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'castScalar'],

        'Fiber' => ['Symfony\Component\VarDumper\Caster\FiberCaster', 'castFiber'],

        'Closure' => ['Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castClosure'],
        'Generator' => ['Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castGenerator'],
        'ReflectionType' => ['Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castType'],
        'ReflectionAttribute' => ['Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castAttribute'],
        'ReflectionGenerator' => ['Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castReflectionGenerator'],
        'ReflectionClass' => ['Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castClass'],
        'ReflectionClassConstant' => ['Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castClassConstant'],
        'ReflectionFunctionAbstract' => ['Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castFunctionAbstract'],
        'ReflectionMethod' => ['Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castMethod'],
        'ReflectionParameter' => ['Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castParameter'],
        'ReflectionProperty' => ['Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castProperty'],
        'ReflectionReference' => ['Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castReference'],
        'ReflectionExtension' => ['Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castExtension'],
        'ReflectionZendExtension' => ['Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castZendExtension'],

        'Doctrine\Common\Persistence\ObjectManager' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Doctrine\Common\Proxy\Proxy' => ['Symfony\Component\VarDumper\Caster\DoctrineCaster', 'castCommonProxy'],
        'Doctrine\ORM\Proxy\Proxy' => ['Symfony\Component\VarDumper\Caster\DoctrineCaster', 'castOrmProxy'],
        'Doctrine\ORM\PersistentCollection' => ['Symfony\Component\VarDumper\Caster\DoctrineCaster', 'castPersistentCollection'],
        'Doctrine\Persistence\ObjectManager' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],

        'DOMException' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castException'],
        'Dom\Exception' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castException'],
        'DOMStringList' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'],
        'DOMNameList' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'],
        'DOMImplementation' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castImplementation'],
        'Dom\Implementation' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castImplementation'],
        'DOMImplementationList' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'],
        'DOMNode' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'],
        'Dom\Node' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'],
        'DOMNameSpaceNode' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'],
        'DOMDocument' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castDocument'],
        'Dom\XMLDocument' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castXMLDocument'],
        'Dom\HTMLDocument' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castHTMLDocument'],
        'DOMNodeList' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'],
        'Dom\NodeList' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'],
        'DOMNamedNodeMap' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'],
        'Dom\DTDNamedNodeMap' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'],
        'DOMXPath' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'],
        'Dom\XPath' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'],
        'Dom\HTMLCollection' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'],
        'Dom\TokenList' => ['Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'],

        'XMLReader' => ['Symfony\Component\VarDumper\Caster\XmlReaderCaster', 'castXmlReader'],

        'ErrorException' => ['Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castErrorException'],
        'Exception' => ['Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castException'],
        'Error' => ['Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castError'],
        'Symfony\Bridge\Monolog\Logger' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Symfony\Component\DependencyInjection\ContainerInterface' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Symfony\Component\EventDispatcher\EventDispatcherInterface' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Symfony\Component\HttpClient\AmpHttpClient' => ['Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'],
        'Symfony\Component\HttpClient\CurlHttpClient' => ['Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'],
        'Symfony\Component\HttpClient\NativeHttpClient' => ['Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'],
        'Symfony\Component\HttpClient\Response\AmpResponse' => ['Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'],
        'Symfony\Component\HttpClient\Response\AmpResponseV4' => ['Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'],
        'Symfony\Component\HttpClient\Response\AmpResponseV5' => ['Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'],
        'Symfony\Component\HttpClient\Response\CurlResponse' => ['Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'],
        'Symfony\Component\HttpClient\Response\NativeResponse' => ['Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'],
        'Symfony\Component\HttpFoundation\Request' => ['Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castRequest'],
        'Symfony\Component\Uid\Ulid' => ['Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castUlid'],
        'Symfony\Component\Uid\Uuid' => ['Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castUuid'],
        'Symfony\Component\VarExporter\Internal\LazyObjectState' => ['Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castLazyObjectState'],
        'Symfony\Component\VarDumper\Exception\ThrowingCasterException' => ['Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castThrowingCasterException'],
        'Symfony\Component\VarDumper\Caster\TraceStub' => ['Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castTraceStub'],
        'Symfony\Component\VarDumper\Caster\FrameStub' => ['Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castFrameStub'],
        'Symfony\Component\VarDumper\Cloner\AbstractCloner' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Symfony\Component\ErrorHandler\Exception\FlattenException' => ['Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castFlattenException'],
        'Symfony\Component\ErrorHandler\Exception\SilencedErrorContext' => ['Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castSilencedErrorContext'],

        'Imagine\Image\ImageInterface' => ['Symfony\Component\VarDumper\Caster\ImagineCaster', 'castImage'],

        'Ramsey\Uuid\UuidInterface' => ['Symfony\Component\VarDumper\Caster\UuidCaster', 'castRamseyUuid'],

        'ProxyManager\Proxy\ProxyInterface' => ['Symfony\Component\VarDumper\Caster\ProxyManagerCaster', 'castProxy'],
        'PHPUnit_Framework_MockObject_MockObject' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'PHPUnit\Framework\MockObject\MockObject' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'PHPUnit\Framework\MockObject\Stub' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Prophecy\Prophecy\ProphecySubjectInterface' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Mockery\MockInterface' => ['Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],

        'PDO' => ['Symfony\Component\VarDumper\Caster\PdoCaster', 'castPdo'],
        'PDOStatement' => ['Symfony\Component\VarDumper\Caster\PdoCaster', 'castPdoStatement'],

        'AMQPConnection' => ['Symfony\Component\VarDumper\Caster\AmqpCaster', 'castConnection'],
        'AMQPChannel' => ['Symfony\Component\VarDumper\Caster\AmqpCaster', 'castChannel'],
        'AMQPQueue' => ['Symfony\Component\VarDumper\Caster\AmqpCaster', 'castQueue'],
        'AMQPExchange' => ['Symfony\Component\VarDumper\Caster\AmqpCaster', 'castExchange'],
        'AMQPEnvelope' => ['Symfony\Component\VarDumper\Caster\AmqpCaster', 'castEnvelope'],

        'ArrayObject' => ['Symfony\Component\VarDumper\Caster\SplCaster', 'castArrayObject'],
        'ArrayIterator' => ['Symfony\Component\VarDumper\Caster\SplCaster', 'castArrayIterator'],
        'SplDoublyLinkedList' => ['Symfony\Component\VarDumper\Caster\SplCaster', 'castDoublyLinkedList'],
        'SplFileInfo' => ['Symfony\Component\VarDumper\Caster\SplCaster', 'castFileInfo'],
        'SplFileObject' => ['Symfony\Component\VarDumper\Caster\SplCaster', 'castFileObject'],
        'SplHeap' => ['Symfony\Component\VarDumper\Caster\SplCaster', 'castHeap'],
        'SplObjectStorage' => ['Symfony\Component\VarDumper\Caster\SplCaster', 'castObjectStorage'],
        'SplPriorityQueue' => ['Symfony\Component\VarDumper\Caster\SplCaster', 'castHeap'],
        'OuterIterator' => ['Symfony\Component\VarDumper\Caster\SplCaster', 'castOuterIterator'],
        'WeakMap' => ['Symfony\Component\VarDumper\Caster\SplCaster', 'castWeakMap'],
        'WeakReference' => ['Symfony\Component\VarDumper\Caster\SplCaster', 'castWeakReference'],

        'Redis' => ['Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedis'],
        'Relay\Relay' => ['Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedis'],
        'RedisArray' => ['Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedisArray'],
        'RedisCluster' => ['Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedisCluster'],

        'DateTimeInterface' => ['Symfony\Component\VarDumper\Caster\DateCaster', 'castDateTime'],
        'DateInterval' => ['Symfony\Component\VarDumper\Caster\DateCaster', 'castInterval'],
        'DateTimeZone' => ['Symfony\Component\VarDumper\Caster\DateCaster', 'castTimeZone'],
        'DatePeriod' => ['Symfony\Component\VarDumper\Caster\DateCaster', 'castPeriod'],

        'GMP' => ['Symfony\Component\VarDumper\Caster\GmpCaster', 'castGmp'],

        'MessageFormatter' => ['Symfony\Component\VarDumper\Caster\IntlCaster', 'castMessageFormatter'],
        'NumberFormatter' => ['Symfony\Component\VarDumper\Caster\IntlCaster', 'castNumberFormatter'],
        'IntlTimeZone' => ['Symfony\Component\VarDumper\Caster\IntlCaster', 'castIntlTimeZone'],
        'IntlCalendar' => ['Symfony\Component\VarDumper\Caster\IntlCaster', 'castIntlCalendar'],
        'IntlDateFormatter' => ['Symfony\Component\VarDumper\Caster\IntlCaster', 'castIntlDateFormatter'],

        'Memcached' => ['Symfony\Component\VarDumper\Caster\MemcachedCaster', 'castMemcached'],

        'Ds\Collection' => ['Symfony\Component\VarDumper\Caster\DsCaster', 'castCollection'],
        'Ds\Map' => ['Symfony\Component\VarDumper\Caster\DsCaster', 'castMap'],
        'Ds\Pair' => ['Symfony\Component\VarDumper\Caster\DsCaster', 'castPair'],
        'Symfony\Component\VarDumper\Caster\DsPairStub' => ['Symfony\Component\VarDumper\Caster\DsCaster', 'castPairStub'],

        'mysqli_driver' => ['Symfony\Component\VarDumper\Caster\MysqliCaster', 'castMysqliDriver'],

        'CurlHandle' => ['Symfony\Component\VarDumper\Caster\CurlCaster', 'castCurl'],

        'Dba\Connection' => ['Symfony\Component\VarDumper\Caster\ResourceCaster', 'castDba'],
        ':dba' => ['Symfony\Component\VarDumper\Caster\ResourceCaster', 'castDba'],
        ':dba persistent' => ['Symfony\Component\VarDumper\Caster\ResourceCaster', 'castDba'],

        'GdImage' => ['Symfony\Component\VarDumper\Caster\GdCaster', 'castGd'],

        'SQLite3Result' => ['Symfony\Component\VarDumper\Caster\SqliteCaster', 'castSqlite3Result'],

        'PgSql\Lob' => ['Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castLargeObject'],
        'PgSql\Connection' => ['Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castLink'],
        'PgSql\Result' => ['Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castResult'],

        ':process' => ['Symfony\Component\VarDumper\Caster\ResourceCaster', 'castProcess'],
        ':stream' => ['Symfony\Component\VarDumper\Caster\ResourceCaster', 'castStream'],

        'OpenSSLAsymmetricKey' => ['Symfony\Component\VarDumper\Caster\OpenSSLCaster', 'castOpensslAsymmetricKey'],
        'OpenSSLCertificateSigningRequest' => ['Symfony\Component\VarDumper\Caster\OpenSSLCaster', 'castOpensslCsr'],
        'OpenSSLCertificate' => ['Symfony\Component\VarDumper\Caster\OpenSSLCaster', 'castOpensslX509'],

        ':persistent stream' => ['Symfony\Component\VarDumper\Caster\ResourceCaster', 'castStream'],
        ':stream-context' => ['Symfony\Component\VarDumper\Caster\ResourceCaster', 'castStreamContext'],

        'XmlParser' => ['Symfony\Component\VarDumper\Caster\XmlResourceCaster', 'castXml'],

        'RdKafka' => ['Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castRdKafka'],
        'RdKafka\Conf' => ['Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castConf'],
        'RdKafka\KafkaConsumer' => ['Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castKafkaConsumer'],
        'RdKafka\Metadata\Broker' => ['Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castBrokerMetadata'],
        'RdKafka\Metadata\Collection' => ['Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castCollectionMetadata'],
        'RdKafka\Metadata\Partition' => ['Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castPartitionMetadata'],
        'RdKafka\Metadata\Topic' => ['Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopicMetadata'],
        'RdKafka\Message' => ['Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castMessage'],
        'RdKafka\Topic' => ['Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopic'],
        'RdKafka\TopicPartition' => ['Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopicPartition'],
        'RdKafka\TopicConf' => ['Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopicConf'],

        'FFI\CData' => ['Symfony\Component\VarDumper\Caster\FFICaster', 'castCTypeOrCData'],
        'FFI\CType' => ['Symfony\Component\VarDumper\Caster\FFICaster', 'castCTypeOrCData'],
    ];

    protected int $maxItems = 2500;
    protected int $maxString = -1;
    protected int $minDepth = 1;

    /**
     * @var array<string, list<callable>>
     */
    private array $casters = [];

    /**
     * @var callable|null
     */
    private $prevErrorHandler;

    private array $classInfo = [];
    private int $filter = 0;

    /**
     * @param callable[]|null $casters A map of casters
     *
     * @see addCasters
     */
    public function __construct(?array $casters = null)
    {
        $this->addCasters($casters ?? static::$defaultCasters);
    }

    /**
     * Adds casters for resources and objects.
     *
     * Maps resources or object types to a callback.
     * Use types as keys and callable casters as values.
     * Prefix types with `::`,
     * see e.g. self::$defaultCasters.
     *
     * @param array<string, callable> $casters A map of casters
     */
    public function addCasters(array $casters): void
    {
        foreach ($casters as $type => $callback) {
            $this->casters[$type][] = $callback;
        }
    }

    /**
     * Adds default casters for resources and objects.
     *
     * Maps resources or object types to a callback.
     * Use types as keys and callable casters as values.
     * Prefix types with `::`,
     * see e.g. self::$defaultCasters.
     *
     * @param array<string, callable> $casters A map of casters
     */
    public static function addDefaultCasters(array $casters): void
    {
        self::$defaultCasters = [...self::$defaultCasters, ...$casters];
    }

    /**
     * Sets the maximum number of items to clone past the minimum depth in nested structures.
     */
    public function setMaxItems(int $maxItems): void
    {
        $this->maxItems = $maxItems;
    }

    /**
     * Sets the maximum cloned length for strings.
     */
    public function setMaxString(int $maxString): void
    {
        $this->maxString = $maxString;
    }

    /**
     * Sets the minimum tree depth where we are guaranteed to clone all the items.  After this
     * depth is reached, only setMaxItems items will be cloned.
     */
    public function setMinDepth(int $minDepth): void
    {
        $this->minDepth = $minDepth;
    }

    /**
     * Clones a PHP variable.
     *
     * @param int $filter A bit field of Caster::EXCLUDE_* constants
     */
    public function cloneVar(mixed $var, int $filter = 0): Data
    {
        $this->prevErrorHandler = set_error_handler(function ($type, $msg, $file, $line, $context = []) {
            if (\E_RECOVERABLE_ERROR === $type || \E_USER_ERROR === $type) {
                // Cloner never dies
                throw new \ErrorException($msg, 0, $type, $file, $line);
            }

            if ($this->prevErrorHandler) {
                return ($this->prevErrorHandler)($type, $msg, $file, $line, $context);
            }

            return false;
        });
        $this->filter = $filter;

        if ($gc = gc_enabled()) {
            gc_disable();
        }
        try {
            return new Data($this->doClone($var));
        } finally {
            if ($gc) {
                gc_enable();
            }
            restore_error_handler();
            $this->prevErrorHandler = null;
        }
    }

    /**
     * Effectively clones the PHP variable.
     */
    abstract protected function doClone(mixed $var): array;

    /**
     * Casts an object to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     */
    protected function castObject(Stub $stub, bool $isNested): array
    {
        $obj = $stub->value;
        $class = $stub->class;

        if (str_contains($class, "@anonymous\0")) {
            $stub->class = get_debug_type($obj);
        }
        if (isset($this->classInfo[$class])) {
            [$i, $parents, $hasDebugInfo, $fileInfo] = $this->classInfo[$class];
        } else {
            $i = 2;
            $parents = [$class];
            $hasDebugInfo = method_exists($class, '__debugInfo');

            foreach (class_parents($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            foreach (class_implements($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            $parents[] = '*';

            $r = new \ReflectionClass($class);
            $fileInfo = $r->isInternal() || $r->isSubclassOf(Stub::class) ? [] : [
                'file' => $r->getFileName(),
                'line' => $r->getStartLine(),
            ];

            $this->classInfo[$class] = [$i, $parents, $hasDebugInfo, $fileInfo];
        }

        $stub->attr += $fileInfo;
        $a = Caster::castObject($obj, $class, $hasDebugInfo, $stub->class);

        try {
            while ($i--) {
                if (!empty($this->casters[$p = $parents[$i]])) {
                    foreach ($this->casters[$p] as $callback) {
                        $a = $callback($obj, $a, $stub, $isNested, $this->filter);
                    }
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '').'⚠' => new ThrowingCasterException($e)] + $a;
        }

        return $a;
    }

    /**
     * Casts a resource to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     */
    protected function castResource(Stub $stub, bool $isNested): array
    {
        $a = [];
        $res = $stub->value;
        $type = $stub->class;

        try {
            if (!empty($this->casters[':'.$type])) {
                foreach ($this->casters[':'.$type] as $callback) {
                    $a = $callback($res, $a, $stub, $isNested, $this->filter);
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '').'⚠' => new ThrowingCasterException($e)] + $a;
        }

        return $a;
    }
}
