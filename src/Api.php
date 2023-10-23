<?php

namespace Fayda\SDK;

use Exception;
use Fayda\SDK\Http\GuzzleHttp;
use Fayda\SDK\Http\IHttp;
use Fayda\SDK\Http\Request;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

abstract class Api
{
    /**
     * @var string SDK Version
     */
    const SDK_VERSION = '0.0.1';

    /**
     * @var string
     */
    protected static $baseUri = 'https://dev.fayda.et';

    /**
     * @var bool
     */
    protected static $skipVerifyTls = false;

    /**
     * @var bool
     */
    protected static $debugMode = false;

    /**
     * @var string
     */
    protected static $logPath;

    /**
     * @var LoggerInterface $logger
     */
    protected static $logger;

    /**
     * @var int
     */
    protected static $logLevel = Logger::DEBUG;

    /**
     * @var array
     */
    protected static $customHeaders;

    /**
     * @var IAuth $auth
     */
    protected $auth;

    /**
     * @var IHttp $http
     */
    protected $http;

    public function __construct(IAuth $auth = null, IHttp $http = null)
    {
        if ($http === null) {
            $http = new GuzzleHttp(['skipVerifyTls' => &self::$skipVerifyTls]);
        }
        $this->auth = $auth;
        $this->http = $http;
    }

    public static function getBaseUri(): string
    {
        return static::$baseUri;
    }

    public static function setBaseUri(string $baseUri)
    {
        static::$baseUri = $baseUri;
    }

    public static function isSkipVerifyTls(): bool
    {
        return static::$skipVerifyTls;
    }

    public static function setSkipVerifyTls(bool $skipVerifyTls)
    {
        static::$skipVerifyTls = $skipVerifyTls;
    }

    public static function isDebugMode(): bool
    {
        return self::$debugMode;
    }

    public static function setDebugMode(bool $debugMode)
    {
        self::$debugMode = $debugMode;
    }

    /**
     * @param LoggerInterface $logger
     */
    public static function setLogger(LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

    /**
     * @return Logger|LoggerInterface
     * @throws Exception
     */
    public static function getLogger()
    {
        if (self::$logger === null) {
            self::$logger = new Logger('Fayda-sdk');
            $handler = new RotatingFileHandler(static::getLogPath() . '/Fayda-sdk.log', 0, static::$logLevel);
            $formatter = new LineFormatter(null, null, false, true);
            $handler->setFormatter($formatter);
            self::$logger->pushHandler($handler);
        }

        return self::$logger;
    }

    public static function getLogPath(): string
    {
        return self::$logPath ?? '/tmp';
    }

    public static function setLogPath(string $logPath)
    {
        self::$logPath = $logPath;
    }

    public static function getLogLevel(): int
    {
        return self::$logLevel;
    }

    public static function setLogLevel(int $logLevel)
    {
        self::$logLevel = $logLevel;
    }

    public static function setCustomHeaders(array $headers)
    {
        self::$customHeaders = $headers;
    }

    public static function getCustomHeaders(): array
    {
        return self::$customHeaders;
    }

    /**
     *
     * @throws Exceptions\HttpException
     * @throws Exceptions\InvalidApiUriException
     * @throws Exception
     */
    public function call(
        string $method,
        string $uri,
        array $params = [],
        array $headers = [],
        int $timeout = 30
    ) {
        $request = new Request();
        $request->setMethod($method);
        $request->setBaseUri(static::getBaseUri());
        $request->setUri($uri);
        $request->setParams($params);

        if ($this->auth) {
            $authHeaders = $this->auth->getHeaders(
                $request->getMethod(),
                $request->getRequestUri(),
                $request->getBodyParams()
            );
            $headers = array_merge($headers, $authHeaders);
        }
        $headers['User-Agent'] = 'Fayda-PHP-SDK/' . static::SDK_VERSION . ' (PHP ' . PHP_VERSION . ')';

        if (self::$customHeaders) {
            $headers = array_merge($headers, self::$customHeaders);
        }

        $request->setHeaders($headers);

        $requestId = uniqid();

        if (self::isDebugMode()) {
            static::getLogger()->debug(sprintf('Sent a HTTP request#%s: %s', $requestId, $request));
        }
        $requestStart = microtime(true);
        $response = $this->http->request($request, $timeout);
        if (self::isDebugMode()) {
            $cost = (microtime(true) - $requestStart) * 1000;
            static::getLogger()->debug(sprintf('Received a HTTP response#%s: cost %.2fms, %s', $requestId, $cost,
                $response));
        }

        return $response;
    }
}
