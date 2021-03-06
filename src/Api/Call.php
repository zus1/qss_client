<?php

namespace App\Api;

use App\Service\Env;
use App\Service\Package;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

abstract class Call
{
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_PUT = "PUT";
    const METHOD_DELETE = "DELETE";

    public static $_mock = false;
    public static $_mockedCallResponses = array(); //will be used in unit test, for mocking api calls

    private $client;
    protected $env;
    protected $package;
    protected $logger;
    protected $tokenOverride = "";

    private $postAsForm = false;
    private $nonJson = false;
    protected $headers = array(
        "Content-Type" => "application/json"
    );

    public function __construct(HttpClientInterface $client, Package $package, LoggerInterface $logger) {
        $this->client = $client;
        $this->env = Env::load();
        $this->package = $package;
        $this->logger = $logger;
    }

    public function setTokenOverride(string $override) : void {
        $this->tokenOverride = $override;
    }

    protected function setPostAsForm() : void {
        $this->postAsForm = true;
    }

    protected function setNonJson() : void {
        $this->nonJson = true;
    }

    protected function addHeader(string $key, string $value) : void {
        if(!array_key_exists($key, $this->headers)) {
            $this->headers[$key] = $value;
        }
    }

    private function getAllowedRequestMethods() : array {
        return array(self::METHOD_GET, self::METHOD_POST, self::METHOD_DELETE, self::METHOD_PUT);
    }

    /**
     *
     * Sends request to api using Symfony http-client and sends response to processing
     * https://symfony.com/doc/current/http_client.html
     *
     * @param string $url
     * @param array $params
     * @param string $method
     * @return array
     * @throws TransportExceptionInterface
     */
    protected function callApi(string $url, ?array $params=array(), ?string $method=self::METHOD_GET) : array  {
        if(!in_array($method, $this->getAllowedRequestMethods())) {
            throw new Exception("Request method invalid");
        }

        $optionsArray = array();
        if(!empty($params)) {
            if($method === self::METHOD_POST) {
                if($this->postAsForm === false) {
                    $optionsArray["body"] = json_encode($params);
                } else {
                    $optionsArray["body"] = $params;
                }
            } elseif($method === self::METHOD_GET) {
                $url .= "?" . http_build_query($params);
            } elseif($method === self::METHOD_PUT) {
                $optionsArray["body"] = json_encode($params);
            }
        }
        $optionsArray["headers"] = $this->headers;

        if(self::$_mock === true) {
            return self::$_mockedCallResponses[$url][$method];
        }
        $response = $this->client->request($method, $url, $optionsArray);

        if($method === self::METHOD_DELETE) {
            $result = $this->processDeleteCallResult($response);
        } else {
            $result = $this->processCallResult($response);
        }

        return $result;
    }

    /**
     *
     * Handles api call responses
     * Dose decoding if JSON and handles logging
     *
     * @param ResponseInterface $response
     * @return array
     */
    protected function processCallResult(ResponseInterface $response) : array {
        try {
            $decoded = ($this->nonJson === false)? $response->toArray() : $response->getContent();
            $httpCode = $response->getStatusCode();
        } catch(Throwable $e) {
            return $this->returnError($e->getCode(), $e->getMessage());
        }


        if($httpCode === 200) {
            return $decoded;
        }

        return $this->returnError((int)$httpCode, "Api error");
    }

    /**
     *
     * Separate processing for delete call responses
     *
     * @param ResponseInterface $response
     * @return array
     */
    protected function processDeleteCallResult(ResponseInterface $response) : array {
        try {
            $httpCode = $response->getStatusCode();
        } catch(Throwable $e) {
            return $this->returnError($e->getCode(), $e->getMessage());
        }
        if($httpCode === 204) {
            return array("error" => 0, "message" => "ok");
        }

        return $this->returnError((int)$httpCode, "Api error");
    }

    private function returnError(int $httpCode, string $message) : array {
        $this->logger->error(sprintf("%s: %s", $message, $httpCode));
        return array("error" => 1, "message" => $message, "code" => $httpCode);
    }
}