<?php

namespace App\Tests;

use App\Entity\User;
use App\Service\Cache;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthenticationTest extends WebTestCase
{
    public function testIsAuthenticated() {
        $userEmail = "coolFakeemail12345678@email.com";
        $user = new User();
        $user->setEmail($userEmail);

        $cacheHash = array("email" => $userEmail);
        Cache::load()->set(Cache::USER_CACHE_KEY, $user, $cacheHash);

        $services = $this->loadFromKernel();

        $services->session->set("user_email", $userEmail);
        $authenticated = $services->auth->isAuthenticated();
        $this->assertTrue($authenticated);

        $services->session->remove("user_email");
        $authenticated = $services->auth->isAuthenticated();
        $this->assertFalse($authenticated);

        $services->session->set("user_email", $userEmail);
        Cache::load()->delete(Cache::USER_CACHE_KEY, $cacheHash);

        $authenticated = $services->auth->isAuthenticated();
        $this->assertFalse($authenticated);
        $this->assertEmpty($services->session->get("user_email"));
    }

    public function testGetAuthenticatedUser() {
        $services = $this->loadFromKernel();

        $authUser = $services->auth->getAuthenticatedUser();
        $this->assertNull($authUser);

        $userEmail = "coolFakeemail12345678@email.com";
        $user = new User();
        $user->setEmail($userEmail);

        $services->session->set("user_email", $userEmail);
        $cacheHash = array("email" => $userEmail);
        Cache::load()->set(Cache::USER_CACHE_KEY, $user, $cacheHash);

        $authUser = $services->auth->getAuthenticatedUser();
        $this->assertEquals($userEmail, $authUser->getEmail());

        Cache::load()->delete(Cache::USER_CACHE_KEY, $cacheHash);
    }

    public function testGetAuthenticatedUserName() {
        $services = $this->loadFromKernel();

        $authUserName = $services->auth->getAuthenticatedUserName();
        $this->assertEquals("", $authUserName);

        $userName = "bubuLubu";
        $cacheHash = array();
        $this->addCachedUser($services->session, $cacheHash, "first_name", $userName);

        $authUserName = $services->auth->getAuthenticatedUserName();
        $this->assertEquals($userName, $authUserName);

        Cache::load()->delete(Cache::USER_CACHE_KEY, $cacheHash);
    }

    public function testGetAuthenticatedUserLastName() {
        $services = $this->loadFromKernel();

        $authUserLastName = $services->auth->getAuthenticatedUserLastName();
        $this->assertEquals("", $authUserLastName);

        $userLastName = "lubuBubu";

        $cacheHash = array();
        $this->addCachedUser($services->session, $cacheHash, "last_name", $userLastName);


        $authUserLastName = $services->auth->getAuthenticatedUserLastName();
        $this->assertEquals($userLastName, $authUserLastName);

        Cache::load()->delete(Cache::USER_CACHE_KEY, $cacheHash);
    }

    public function testLogout() {
        $services = $this->loadFromKernel();
        $cacheHash = array();
        $this->addCachedUser($services->session,$cacheHash);
        $services->auth->logout();
        $cachedUser = Cache::load()->get(Cache::USER_CACHE_KEY, $cacheHash);
        $this->assertFalse($cachedUser);
    }

    private function loadFromKernel() {
        self::bootKernel();
        $container = self::$container;
        $auth = $container->get("App\Service\Authentication");
        $session = $container->get("Symfony\Component\HttpFoundation\Session\SessionInterface");

        $dto = new \stdClass();
        $dto->auth = $auth;
        $dto->session = $session;

        return $dto;
    }

    private function addCachedUser(SessionInterface $session, array &$cacheHash, ?string $setter="", ?string $value="") {
        $setterToMethod = array(
            "first_name" => "firstNameSetter",
            "last_name" => "lastNameSetter",
        );
        $userEmail = "coolFakeemail12345678@email.com";
        $user = new User();
        $session->set("user_email", $userEmail);
        $cacheHash = array("email" => $userEmail);
        if($setter !== "") {
            call_user_func_array(array($this, $setterToMethod[$setter]), array($user, $value));
        }
        Cache::load()->set(Cache::USER_CACHE_KEY, $user, $cacheHash);
    }

    private function firstNameSetter(User $user, string $value) {
        $user->setName($value);
    }

    private function lastNameSetter(User $user, string $value) {
        $user->setLName($value);
    }
}
