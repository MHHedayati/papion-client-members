<?php

namespace Papion\MembersClient;

class ApiCommand
{
    private $needsToken;
    private $method;
    private $endpoint;

    private $token;
    private $data;
    private $queryParams;
    private $headers;

    function __construct($endpoint, $method, $needsToken)
    {
        $this->endpoint = $endpoint;
        $this->method = $method;
        $this->needsToken = $needsToken;
    }

    /**
     * @param mixed $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return mixed
     */
    public function getNeedsToken()
    {
        return $this->needsToken;
    }

    /**
     * @param mixed $needsToken
     */
    public function setNeedsToken($needsToken)
    {
        $this->needsToken = $needsToken;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param mixed $queryParams
     */
    public function setQueryParams($queryParams)
    {
        $this->queryParams = $queryParams;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }
}