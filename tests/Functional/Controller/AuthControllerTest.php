<?php

namespace App\Tests;

use App\Api\Call;
use App\Service\Authentication;
use App\Service\Cache;

class AuthControllerTest extends QssTestCase
{
    public function testLogin() {
        $client = self::createClient();
        $client->request("GET", '/login');
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testDoLogin() {
        $client = $this->up(false);

        $tokenKey = "1234567890";
        $userName = "Koko";
        $userLName = "Kaga";
        $userEmail = "superFakeEmail123455432@fake.com";
        $userPassword = "1234567890987654321avgtzhnjjuzq";

        Cache::load()->delete(Cache::USER_CACHE_KEY, array("email" => $userEmail));

        $errorMessage = "Api Error";
        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/token"][Call::METHOD_POST] = array("error" => 1, "message" => $errorMessage, "code" => 404);

        $parameters = array(
            'email' => $userEmail,
            "password" => $userPassword
        );

        $client->request("POST", "/do-login", $parameters);
        $response = $client->getResponse();
        $this->assertTrue($response->isRedirect("/login"));
        $session = self::$container->get("Symfony\Component\HttpFoundation\Session\SessionInterface");
        $warnings = $session->getFlashBag()->get("warning");
        $this->assertEquals($errorMessage, $warnings[count($warnings) - 1]);

        $testUserArray = array(
            'token_key' => $tokenKey,
            'user' => array(
                'first_name' => $userName,
                'last_name' => $userLName,
                'email' => $userEmail
            )
        );

        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/token"][Call::METHOD_POST] = $testUserArray;

        $parameters["email"] = "notgood.com";
        $this->makeRedirectAssertions($client, $parameters, self::$container, "/do-login", "/login", "warning",  'The email ""' . $parameters["email"] . '"" is not a valid email.');

        $parameters["email"] = $userEmail;
        $parameters["password"] = "{}[]@";
        $this->makeRedirectAssertions($client, $parameters, self::$container, "/do-login", "/login", "warning",  "Invalid characters in password.");

        $parameters["password"] = $userPassword;
        $client->request("POST", "/do-login", $parameters);
        $response = $client->getResponse();
        $this->assertTrue($response->isRedirect("/author-list"));

        $session = self::$container->get("Symfony\Component\HttpFoundation\Session\SessionInterface");
        $userEmailSes = $session->get(Authentication::USER_SESSION_KEY);
        $this->assertEquals($userEmail, $userEmailSes);

        Cache::$_mock = false;
        Call::$_mock = false;
        Call::$_mockedCallResponses = array();
    }

    public function testLogout() {
        $client = $this->up(false);

        $userEmail = "superFakeEmail123455432@fake.com";
        $tokenKey = "1234567890";

        $loggedInUser = Cache::load()->get(Cache::USER_CACHE_KEY, array("email" => $userEmail));
        $this->assertEquals($userEmail, $loggedInUser->getEmail());
        $this->assertEquals($tokenKey, $loggedInUser->getToken());

        $session = self::$container->get("Symfony\Component\HttpFoundation\Session\SessionInterface");
        $session->set(Authentication::USER_SESSION_KEY, $userEmail); //we boot new container so no session. Already tested that session was started

        $client->request("POST", '/logout');
        $this->assertTrue($client->getResponse()->isRedirect("/login"));

        $loggedInUser = Cache::load()->get(Cache::USER_CACHE_KEY, array("email" => $userEmail));
        $this->assertFalse($loggedInUser);

        $this->down();
    }
}
