<?php

namespace Fayda\SDK\Http;

class Request
{
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_PUT    = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $baseUri;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $requestUri;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var string
     */
    protected $bodyParams = null;

    public function getMethod(): string
    {
        return $this->method;
    }


    public function setMethod(string $method)
    {
        $this->method = strtoupper($method);
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    public function setBaseUri(string $baseUri)
    {
        $this->baseUri = $baseUri ? rtrim($baseUri, '/') : null;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri)
    {
        $this->uri = rtrim($uri, '/');
    }

    public function getRequestUri(): string
    {
        if ($this->requestUri) {
            return $this->requestUri;
        }

        // GET/DELETE: move parameters into query
        if ($this->isGetOrDeleteMethod() && !empty($this->params)) {
            $query = http_build_query($this->params);
            if ($query !== '') {
                $this->uri .= strpos($this->uri, '?') === false ? '?' : '&';
                $this->uri .= $query;
            }
        }

        $url = $this->baseUri . $this->uri;
        $this->requestUri = substr($url, strpos($url, '/', 8));
        return $this->requestUri;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }


    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getBodyParams(): string
    {
        if ($this->bodyParams === null) {
            if ($this->isGetOrDeleteMethod()) {
                $this->bodyParams = '';
            } else {
                $this->bodyParams = empty($this->params) ? '' : json_encode($this->params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
        return $this->bodyParams;
    }

    protected function isGetOrDeleteMethod(): bool
    {
        return in_array($this->getMethod(), [self::METHOD_GET, self::METHOD_DELETE], true);
    }

    public function __toString()
    {
        $str = $this->getMethod() . ' ' . $this->getRequestUri();
        $str .= ' with headers=' . json_encode($this->getHeaders(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $str .= ' with body=' . $this->getBodyParams();
        return $str;
    }
}
