<?php

namespace App\Api;

use App\Service\Authentication;
use App\Service\Package;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Qss extends Call
{
    protected $authentication;

    public function __construct(HttpClientInterface $client, Package $package, Authentication $auth, LoggerInterface $logger) {
        $this->authentication = $auth;
        parent::__construct($client, $package, $logger);
    }

    protected function getBaseUrl() {
        return $this->env->get("QSS_BASE", "https://symfony-skeleton.q-tests.com");
    }

    public function callQss(string $url, ?array $params=array(), ?string $method=self::METHOD_GET) {
        $apiKey = (empty($this->tokenOverride))? $this->getApiKey() : $this->tokenOverride;
        $this->addHeader("Authorization", "Bearer " . $apiKey);
        return $this->callApi($url, $params, $method);
    }

    protected function getApiKey() {
        if(!$this->authentication->isAuthenticated()) {
            throw new Exception("Not logged in");
        }
        $authenticatedUser = $this->authentication->getAuthenticatedUser();
        return $authenticatedUser->getToken();
    }

    protected function handleError(array $response) {
        if(isset($response["error"]) && $response["error"] === 1) {
            throw new Exception($response["message"], $response["code"]);
        }
    }
}