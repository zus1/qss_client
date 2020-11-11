<?php

namespace App\Api;

use App\Service\Authentication;
use App\Service\Package;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Qss extends Call
{
    protected $authentication;

    public function __construct(HttpClientInterface $client, Package $package, Authentication $auth) {
        $this->authentication = $auth;
        parent::__construct($client, $package);
    }

    protected function getBaseUrl() {
        return $this->env->get("QSS_BASE", "https://symfony-skeleton.q-tests.com");
    }

    protected function callQss(string $url, ?array $params=array(), ?string $method=self::METHOD_GET) {
        $apiKey = $this->getApiKey();
        $this->addHeader("Authorization", "Bearer " . $apiKey);
        return $this->callApi($url, $params, $method);
    }

    protected function getApiKey() {
        if(!$this->authentication->isAuthenticated()) {
            throw new Exception("Not logged in");
        }
        $authenticatedUser = $this->authentication->getAuthenticatedUser();
        /*$cachedUser = Cache::load()->get(Cache::USER_CACHE_KEY, array("email" => $userEmail));
        if(!$cachedUser) {
            throw new Exception("Not logged in");
        }*/
        return $authenticatedUser->getToken();
    }

    protected function handleError(array $response) {
        if(isset($response["error"]) && $response["error"] === 1) {
            throw new Exception($response["message"], $response["code"]);
        }
    }
}