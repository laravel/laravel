<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\ErrorHandler\ErrorRenderer;

use Psr\Log\LoggerInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerConfigurator;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class HtmlErrorRenderer implements ErrorRendererInterface
{
    private const GHOST_ADDONS = [
        '02-14' => self::GHOST_HEART,
        '02-29' => self::GHOST_PLUS,
        '10-18' => self::GHOST_GIFT,
    ];

    private const GHOST_GIFT = 'M124.00534057617188,5.3606138080358505 C124.40059661865234,4.644828304648399 125.1237564086914,3.712414965033531 123.88127899169922,3.487462028861046 C123.53517150878906,3.3097832053899765 123.18894958496094,2.9953975528478622 122.8432846069336,3.345616325736046 C122.07421112060547,3.649444565176964 121.40750122070312,4.074306473135948 122.2164306640625,4.869479164481163 C122.57514953613281,5.3830065578222275 122.90142822265625,6.503447040915489 123.3077621459961,6.626829609274864 C123.55027770996094,6.210384353995323 123.7774658203125,5.785196766257286 124.00534057617188,5.3606138080358505 zM122.30630493164062,7.336987480521202 C121.60028076171875,6.076864704489708 121.03211975097656,4.72498320043087 120.16796875,3.562500938773155 C119.11695098876953,2.44033907353878 117.04605865478516,2.940566048026085 116.57544708251953,4.387995228171349 C115.95028686523438,5.819030746817589 117.2991714477539,7.527640804648399 118.826171875,7.348545059561729 C119.98493194580078,7.367936596274376 121.15027618408203,7.420116886496544 122.30630493164062,7.336987480521202 zM128.1732177734375,7.379541382193565 C129.67486572265625,7.17823551595211 130.53842163085938,5.287807449698448 129.68344116210938,4.032590612769127 C128.92578125,2.693056806921959 126.74605560302734,2.6463639587163925 125.98509216308594,4.007616028189659 C125.32617950439453,5.108129009604454 124.75428009033203,6.258124336600304 124.14962768554688,7.388818249106407 C125.48638916015625,7.465229496359825 126.8357162475586,7.447416767477989 128.1732177734375,7.379541382193565 zM130.6601104736328,8.991325363516808 C131.17202758789062,8.540884003043175 133.1543731689453,8.009847149252892 131.65304565429688,7.582054600119591 C131.2811279296875,7.476506695151329 130.84751892089844,6.99234913289547 130.5132598876953,7.124847874045372 C129.78744506835938,8.02728746831417 128.67140197753906,8.55669592320919 127.50616455078125,8.501235947012901 C127.27806091308594,8.576229080557823 126.11459350585938,8.38720129430294 126.428955078125,8.601900085806847 C127.25099182128906,9.070617660880089 128.0523223876953,9.579657539725304 128.902587890625,9.995706543326378 C129.49813842773438,9.678531631827354 130.0761260986328,9.329126343131065 130.6601104736328,8.991325363516808 zM118.96446990966797,9.246344551444054 C119.4022445678711,8.991325363516808 119.84001922607422,8.736305221915245 120.27779388427734,8.481284126639366 C118.93965911865234,8.414779648184776 117.40827941894531,8.607666000723839 116.39698791503906,7.531384453177452 C116.11186981201172,7.212117180228233 115.83845520019531,6.846597656607628 115.44329071044922,7.248530372977257 C114.96995544433594,7.574637398123741 113.5140609741211,7.908811077475548 114.63501739501953,8.306883797049522 C115.61112976074219,8.883499130606651 116.58037567138672,9.474181160330772 117.58061218261719,10.008124336600304 C118.05723571777344,9.784612640738487 118.50651550292969,9.5052699893713 118.96446990966797,9.246344551444054 zM125.38018035888672,12.091858848929405 C125.9474868774414,11.636047348380089 127.32159423828125,11.201767906546593 127.36749267578125,10.712632164359093 C126.08487701416016,9.974547371268272 124.83960723876953,9.152772888541222 123.49772644042969,8.528907760977745 C123.03594207763672,8.353693947196007 122.66152954101562,8.623294815421104 122.28982543945312,8.857431396842003 C121.19065856933594,9.51122473180294 120.06505584716797,10.12446115911007 119.00167083740234,10.835315689444542 C120.39238739013672,11.69529627263546 121.79983520507812,12.529837593436241 123.22095489501953,13.338589653372765 C123.94580841064453,12.932025894522667 124.66128540039062,12.508862480521202 125.38018035888672,12.091858848929405 zM131.07164001464844,13.514615997672081 C131.66018676757812,13.143282875418663 132.2487335205078,12.771927818655968 132.8372802734375,12.400571808218956 C132.8324737548828,11.156818374991417 132.8523406982422,9.912529930472374 132.81829833984375,8.669195160269737 C131.63046264648438,9.332009300589561 130.45948791503906,10.027913078665733 129.30828857421875,10.752535805106163 C129.182373046875,12.035354599356651 129.24623107910156,13.33940313756466 129.27359008789062,14.628684982657433 C129.88104248046875,14.27079389989376 130.4737548828125,13.888019546866417 131.07164001464844,13.514640793204308 zM117.26847839355469,12.731024727225304 C117.32825469970703,11.67083452641964 117.45709991455078,10.46224020421505 116.17853546142578,10.148179039359093 C115.37110900878906,9.77159021794796 114.25194549560547,8.806716904044151 113.62991333007812,8.81639002263546 C113.61052703857422,10.0110072940588 113.62078857421875,11.20585821568966 113.61869049072266,12.400571808218956 C114.81139373779297,13.144886955618858 115.98292541503906,13.925040230154991 117.20137023925781,14.626662239432335 C117.31951141357422,14.010867103934288 117.24227905273438,13.35805033147335 117.26847839355469,12.731024727225304 zM125.80937957763672,16.836034759879112 C126.51483917236328,16.390663132071495 127.22030639648438,15.945291504263878 127.92576599121094,15.49991987645626 C127.92250061035156,14.215868934988976 127.97560119628906,12.929980263113976 127.91757202148438,11.647302612662315 C127.14225769042969,11.869626984000206 126.25550079345703,12.556857094168663 125.43866729736328,12.983742699027061 C124.82704162597656,13.342005714774132 124.21542358398438,13.700271591544151 123.60379028320312,14.05853746831417 C123.61585235595703,15.429577812552452 123.57081604003906,16.803131088614464 123.64839172363281,18.172149643301964 C124.37957000732422,17.744937881827354 125.09130859375,17.284801468253136 125.80937957763672,16.836034759879112 zM122.8521499633789,16.115344032645226 C122.8521499633789,15.429741844534874 122.8521499633789,14.744139656424522 122.8521499633789,14.05853746831417 C121.43595123291016,13.230924591422081 120.02428436279297,12.395455345511436 118.60256958007812,11.577354416251183 C118.52394104003906,12.888403877615929 118.56887817382812,14.204405769705772 118.55702209472656,15.517732605338097 C119.97289276123047,16.4041957706213 121.37410736083984,17.314891800284386 122.80789947509766,18.172149643301964 C122.86368560791016,17.488990768790245 122.84332275390625,16.800363525748253 122.8521499633789,16.115344032645226 zM131.10684204101562,18.871450409293175 C131.68399047851562,18.48711584508419 132.2611541748047,18.10278509557247 132.8383026123047,17.718475326895714 C132.81423950195312,16.499977096915245 132.89776611328125,15.264989838004112 132.77627563476562,14.05993078649044 C131.5760040283203,14.744719490408897 130.41763305664062,15.524359688162804 129.23875427246094,16.255397781729698 C129.26707458496094,17.516149505972862 129.18060302734375,18.791316971182823 129.3108367919922,20.041303619742393 C129.91973876953125,19.667551025748253 130.51010131835938,19.264152511954308 131.10684204101562,18.871450409293175 zM117.2557373046875,18.188333496451378 C117.25104522705078,17.549470886588097 117.24633026123047,16.91058538854122 117.24163055419922,16.271720871329308 C116.04924774169922,15.525708183646202 114.87187957763672,14.75476549565792 113.66158294677734,14.038097366690636 C113.5858383178711,15.262084946036339 113.62901306152344,16.49083898961544 113.61761474609375,17.717010483145714 C114.82051086425781,18.513254150748253 116.00987243652344,19.330610260367393 117.22888946533203,20.101993545889854 C117.27559661865234,19.466014847159386 117.25241088867188,18.825733169913292 117.2557373046875,18.188333496451378 zM125.8398666381836,22.38675306737423 C126.54049682617188,21.921453461050987 127.24110412597656,21.456151947379112 127.94172668457031,20.99083136022091 C127.94009399414062,19.693386062979698 127.96646118164062,18.395381912589073 127.93160247802734,17.098379120230675 C126.50540924072266,17.97775076329708 125.08877563476562,18.873308166861534 123.68258666992188,19.78428266942501 C123.52366638183594,21.03710363805294 123.626708984375,22.32878302037716 123.62647247314453,23.595300659537315 C124.06291198730469,23.86113165318966 125.1788101196289,22.68297766149044 125.8398666381836,22.38675306737423 zM122.8521499633789,21.83134649693966 C122.76741790771484,20.936696991324425 123.21651458740234,19.67745779454708 122.0794677734375,19.330633148550987 C120.93280029296875,18.604360565543175 119.7907485961914,17.870157226920128 118.62899780273438,17.16818617284298 C118.45966339111328,18.396427139639854 118.63676452636719,19.675991043448448 118.50668334960938,20.919256195425987 C119.89984130859375,21.92635916173458 121.32942199707031,22.88914106786251 122.78502655029297,23.803510650992393 C122.90177917480469,23.1627406924963 122.82917022705078,22.48402212560177 122.8521499633789,21.83134649693966 zM117.9798355102539,21.59483526647091 C116.28416442871094,20.46288488805294 114.58848571777344,19.330957397818565 112.892822265625,18.199007019400597 C112.89473724365234,14.705654129385948 112.84647369384766,11.211485847830772 112.90847778320312,7.718807205557823 C113.7575912475586,7.194885239005089 114.66117858886719,6.765397056937218 115.5350341796875,6.284702762961388 C114.97061157226562,4.668964847922325 115.78496551513672,2.7054970115423203 117.42159271240234,2.1007001250982285 C118.79354095458984,1.537783369421959 120.44731903076172,2.0457767099142075 121.32200622558594,3.23083733022213 C121.95732116699219,2.9050118774175644 122.59264373779297,2.5791852325201035 123.22796630859375,2.253336176276207 C123.86669921875,2.5821153968572617 124.50543975830078,2.9108948558568954 125.1441650390625,3.23967407643795 C126.05941009521484,2.154020771384239 127.62747192382812,1.5344576686620712 128.986328125,2.1429056972265244 C130.61741638183594,2.716217741370201 131.50650024414062,4.675290569663048 130.9215545654297,6.2884936183691025 C131.8018341064453,6.78548763692379 132.7589111328125,7.1738648265600204 133.5660400390625,7.780336365103722 C133.60182189941406,11.252970680594444 133.56637573242188,14.726140961050987 133.5631103515625,18.199007019400597 C130.18914794921875,20.431867584586143 126.86984252929688,22.74994657933712 123.44108581542969,24.897907242178917 C122.44406127929688,24.897628769278526 121.5834732055664,23.815067276358604 120.65831756591797,23.37616156041622 C119.76387023925781,22.784828171133995 118.87168884277344,22.19007681310177 117.9798355102539,21.59483526647091 z';
    private const GHOST_HEART = 'M125.91386369681868,8.305165958366445 C128.95033202169043,-0.40540639102854037 140.8469835342744,8.305165958366445 125.91386369681868,19.504526138305664 C110.98208663272044,8.305165958366445 122.87795231771452,-0.40540639102854037 125.91386369681868,8.305165958366445 z';
    private const GHOST_PLUS = 'M111.36824226379395,8.969108581542969 L118.69175148010254,8.969108581542969 L118.69175148010254,1.6455793380737305 L126.20429420471191,1.6455793380737305 L126.20429420471191,8.969108581542969 L133.52781105041504,8.969108581542969 L133.52781105041504,16.481630325317383 L126.20429420471191,16.481630325317383 L126.20429420471191,23.805158615112305 L118.69175148010254,23.805158615112305 L118.69175148010254,16.481630325317383 L111.36824226379395,16.481630325317383 z';

    private bool|\Closure $debug;
    private string $charset;
    private FileLinkFormatter $fileLinkFormat;
    private string|\Closure $outputBuffer;

    private static string $template = 'views/error.html.php';

    /**
     * @param bool|callable   $debug        The debugging mode as a boolean or a callable that should return it
     * @param string|callable $outputBuffer The output buffer as a string or a callable that should return it
     */
    public function __construct(
        bool|callable $debug = false,
        ?string $charset = null,
        string|FileLinkFormatter|null $fileLinkFormat = null,
        private ?string $projectDir = null,
        string|callable $outputBuffer = '',
        private ?LoggerInterface $logger = null,
    ) {
        $this->debug = \is_bool($debug) ? $debug : $debug(...);
        $this->charset = $charset ?: (\ini_get('default_charset') ?: 'UTF-8');
        $this->fileLinkFormat = $fileLinkFormat instanceof FileLinkFormatter ? $fileLinkFormat : new FileLinkFormatter($fileLinkFormat);
        $this->outputBuffer = \is_string($outputBuffer) ? $outputBuffer : $outputBuffer(...);
    }

    public function render(\Throwable $exception): FlattenException
    {
        $headers = ['Content-Type' => 'text/html; charset='.$this->charset];
        if (\is_bool($this->debug) ? $this->debug : ($this->debug)($exception)) {
            $headers['X-Debug-Exception'] = rawurlencode(substr($exception->getMessage(), 0, 2000));
            $headers['X-Debug-Exception-File'] = rawurlencode($exception->getFile()).':'.$exception->getLine();
        }

        $exception = FlattenException::createWithDataRepresentation($exception, null, $headers);

        return $exception->setAsString($this->renderException($exception));
    }

    /**
     * Gets the HTML content associated with the given exception.
     */
    public function getBody(FlattenException $exception): string
    {
        return $this->renderException($exception, 'views/exception.html.php');
    }

    /**
     * Gets the stylesheet associated with the given exception.
     */
    public function getStylesheet(): string
    {
        if (!$this->debug) {
            return $this->include('assets/css/error.css');
        }

        return $this->include('assets/css/exception.css');
    }

    public static function isDebug(RequestStack $requestStack, bool $debug): \Closure
    {
        return static function () use ($requestStack, $debug): bool {
            if (!$request = $requestStack->getCurrentRequest()) {
                return $debug;
            }

            return $debug && $request->attributes->getBoolean('showException', true);
        };
    }

    public static function getAndCleanOutputBuffer(RequestStack $requestStack): \Closure
    {
        return static function () use ($requestStack): string {
            if (!$request = $requestStack->getCurrentRequest()) {
                return '';
            }

            $startObLevel = $request->headers->get('X-Php-Ob-Level', -1);

            if (ob_get_level() <= $startObLevel) {
                return '';
            }

            Response::closeOutputBuffers($startObLevel + 1, true);

            return ob_get_clean();
        };
    }

    private function renderException(FlattenException $exception, string $debugTemplate = 'views/exception_full.html.php'): string
    {
        $debug = \is_bool($this->debug) ? $this->debug : ($this->debug)($exception);
        $statusText = $this->escape($exception->getStatusText());
        $statusCode = $this->escape($exception->getStatusCode());

        if (!$debug) {
            return $this->include(self::$template, [
                'statusText' => $statusText,
                'statusCode' => $statusCode,
            ]);
        }

        $exceptionMessage = $this->escape($exception->getMessage());

        return $this->include($debugTemplate, [
            'exception' => $exception,
            'exceptionMessage' => $exceptionMessage,
            'statusText' => $statusText,
            'statusCode' => $statusCode,
            'logger' => null !== $this->logger && class_exists(DebugLoggerConfigurator::class) ? DebugLoggerConfigurator::getDebugLogger($this->logger) : null,
            'currentContent' => \is_string($this->outputBuffer) ? $this->outputBuffer : ($this->outputBuffer)(),
        ]);
    }

    private function dumpValue(Data $value): string
    {
        $dumper = new HtmlDumper();
        $dumper->setTheme('light');

        return $dumper->dump($value, true);
    }

    private function formatArgs(array $args): string
    {
        $result = [];
        foreach ($args as $key => $item) {
            if ('object' === $item[0]) {
                $formattedValue = \sprintf('<em>object</em>(%s)', $this->abbrClass($item[1]));
            } elseif ('array' === $item[0]) {
                $formattedValue = \sprintf('<em>array</em>(%s)', \is_array($item[1]) ? $this->formatArgs($item[1]) : $item[1]);
            } elseif ('null' === $item[0]) {
                $formattedValue = '<em>null</em>';
            } elseif ('boolean' === $item[0]) {
                $formattedValue = '<em>'.strtolower(var_export($item[1], true)).'</em>';
            } elseif ('resource' === $item[0]) {
                $formattedValue = '<em>resource</em>';
            } elseif (preg_match('/[^\x07-\x0D\x1B\x20-\xFF]/', $item[1])) {
                $formattedValue = '<em>binary string</em>';
            } else {
                $formattedValue = str_replace("\n", '', $this->escape(var_export($item[1], true)));
            }

            $result[] = \is_int($key) ? $formattedValue : \sprintf("'%s' => %s", $this->escape($key), $formattedValue);
        }

        return implode(', ', $result);
    }

    private function formatArgsAsText(array $args): string
    {
        return strip_tags($this->formatArgs($args));
    }

    private function escape(string $string): string
    {
        return htmlspecialchars($string, \ENT_COMPAT | \ENT_SUBSTITUTE, $this->charset);
    }

    private function abbrClass(string $class): string
    {
        $parts = explode('\\', $class);
        $short = array_pop($parts);

        return \sprintf('<abbr title="%s">%s</abbr>', $class, $short);
    }

    private function getFileRelative(string $file): ?string
    {
        $file = str_replace('\\', '/', $file);

        if (null !== $this->projectDir && str_starts_with($file, $this->projectDir)) {
            return ltrim(substr($file, \strlen($this->projectDir)), '/');
        }

        return null;
    }

    /**
     * Formats a file path.
     *
     * @param string $file An absolute file path
     * @param int    $line The line number
     * @param string $text Use this text for the link rather than the file path
     */
    private function formatFile(string $file, int $line, ?string $text = null): string
    {
        $file = trim($file);

        if (null === $text) {
            $text = $file;
            if (null !== $rel = $this->getFileRelative($text)) {
                $rel = explode('/', $rel, 2);
                $text = \sprintf('<abbr title="%s%2$s">%s</abbr>%s', $this->projectDir, $rel[0], '/'.($rel[1] ?? ''));
            }
        }

        if (0 < $line) {
            $text .= ' at line '.$line;
        }

        if (!file_exists($file)) {
            return $text;
        }

        $link = $this->fileLinkFormat->format($file, $line);

        return \sprintf('<a href="%s" title="Click to open this file" class="file_link">%s</a>', $this->escape($link), $text);
    }

    /**
     * Returns an excerpt of a code file around the given line number.
     *
     * @param string $file       A file path
     * @param int    $line       The selected line number
     * @param int    $srcContext The number of displayed lines around or -1 for the whole file
     */
    private function fileExcerpt(string $file, int $line, int $srcContext = 3): string
    {
        if (is_file($file) && is_readable($file)) {
            // highlight_file could throw warnings
            // see https://bugs.php.net/25725
            $code = @highlight_file($file, true);
            if (\PHP_VERSION_ID >= 80300) {
                // remove main pre/code tags
                $code = preg_replace('#^<pre.*?>\s*<code.*?>(.*)</code>\s*</pre>#s', '\\1', $code);
                // split multiline span tags
                $code = preg_replace_callback('#<span ([^>]++)>((?:[^<\\n]*+\\n)++[^<]*+)</span>#', static fn ($m) => "<span $m[1]>".str_replace("\n", "</span>\n<span $m[1]>", $m[2]).'</span>', $code);
                $content = explode("\n", $code);
            } else {
                // remove main code/span tags
                $code = preg_replace('#^<code.*?>\s*<span.*?>(.*)</span>\s*</code>#s', '\\1', $code);
                // split multiline spans
                $code = preg_replace_callback('#<span ([^>]++)>((?:[^<]*+<br \/>)++[^<]*+)</span>#', static fn ($m) => "<span $m[1]>".str_replace('<br />', "</span><br /><span $m[1]>", $m[2]).'</span>', $code);
                $content = explode('<br />', $code);
            }

            $lines = [];
            if (0 > $srcContext) {
                $srcContext = \count($content);
            }

            for ($i = max($line - $srcContext, 1), $max = min($line + $srcContext, \count($content)); $i <= $max; ++$i) {
                $lines[] = '<li'.($i == $line ? ' class="selected"' : '').'><code>'.$this->fixCodeMarkup($content[$i - 1]).'</code></li>';
            }

            return '<ol start="'.max($line - $srcContext, 1).'">'.implode("\n", $lines).'</ol>';
        }

        return '';
    }

    private function fixCodeMarkup(string $line): string
    {
        // </span> ending tag from previous line
        $opening = strpos($line, '<span');
        $closing = strpos($line, '</span>');
        if (false !== $closing && (false === $opening || $closing < $opening)) {
            $line = substr_replace($line, '', $closing, 7);
        }

        // missing </span> tag at the end of line
        $opening = strrpos($line, '<span');
        $closing = strrpos($line, '</span>');
        if (false !== $opening && (false === $closing || $closing < $opening)) {
            $line .= '</span>';
        }

        return trim($line);
    }

    private function formatFileFromText(string $text): string
    {
        return preg_replace_callback('/in ("|&quot;)?(.+?)\1(?: +(?:on|at))? +line (\d+)/s', fn ($match) => 'in '.$this->formatFile($match[2], $match[3]), $text) ?? $text;
    }

    private function formatLogMessage(string $message, array $context): string
    {
        if ($context && str_contains($message, '{')) {
            $replacements = [];
            foreach ($context as $key => $val) {
                if (\is_scalar($val)) {
                    $replacements['{'.$key.'}'] = $val;
                }
            }

            if ($replacements) {
                $message = strtr($message, $replacements);
            }
        }

        return $this->escape($message);
    }

    private function addElementToGhost(): string
    {
        if (!isset(self::GHOST_ADDONS[date('m-d')])) {
            return '';
        }

        return '<path d="'.self::GHOST_ADDONS[date('m-d')].'" fill="#fff" fill-opacity="0.6"></path>';
    }

    private function include(string $name, array $context = []): string
    {
        extract($context, \EXTR_SKIP);
        ob_start();

        include is_file(\dirname(__DIR__).'/Resources/'.$name) ? \dirname(__DIR__).'/Resources/'.$name : $name;

        return trim(ob_get_clean());
    }

    /**
     * Allows overriding the default non-debug template.
     *
     * @param string $template path to the custom template file to render
     */
    public static function setTemplate(string $template): void
    {
        self::$template = $template;
    }
}
