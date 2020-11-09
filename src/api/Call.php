<?php


namespace App\Api;


use App\Services\Env;
use Exception;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

class Call
{

    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_PUT = "PUT";
    const METHOD_DELETE = "DELETE";

    private $client;
    protected $env;

    private $postAsForm = false;
    private $nonJson = false;
    protected $headers = array(
        "Accept" => "application/json"
    );

    public function __construct(HttpClientInterface $client) {
        $this->client = $client;
        $this->env = Env::load();
    }

    protected function setPostAsForm() {
        $this->postAsForm = true;
    }

    protected function setNonJson() {
        $this->nonJson = true;
    }

    protected function addHeader(string $key, string $value) {
        if(!array_key_exists($key, $this->headers)) {
            $this->headers[$key] = $value;
        }
    }

    private function getAllowedRequestMethods() {
        return array(self::METHOD_GET, self::METHOD_POST, self::METHOD_DELETE, self::METHOD_PUT);
    }

    /**
     *
     * Sends request to api using curl and sends response to processing
     *
     * @param string $url
     * @param array $params
     * @param string $method
     * @return array|mixed|string
     * @throws TransportExceptionInterface
     */
    protected function callApi(string $url, ?array $params=array(), ?string $method=self::METHOD_GET)  {
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
     * @return array|mixed|string
     */
    protected function processCallResult(ResponseInterface $response) {
        try {
            $decoded = ($this->nonJson === false)? $response->toArray() : $response->getContent();
            $httpCode = $response->getStatusCode();
        } catch(Throwable $e) {
            return array("error" => 1, "message" => $e->getMessage(), "code" => $e->getCode());
        }


        if($httpCode === 200) {
            return $decoded;
        }
        return array("error" => 1, "message" => "Api error", "code" => $httpCode);
    }

    protected function processDeleteCallResult(ResponseInterface $response) {
        try {
            $httpCode = $response->getStatusCode();
        } catch(Throwable $e) {
            return array("error" => 1, "message" => $e->getMessage(), "code" => $e->getCode());
        }
        if($httpCode === 204) {
            return array("error" => 0, "message" => "ok");
        }

        return array("error" => 1, "message" => "Api error", "code" => $httpCode);
    }
}