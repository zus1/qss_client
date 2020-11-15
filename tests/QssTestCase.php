<?php

namespace App\Tests;

use App\Api\Call;
use App\Entity\Author;
use App\Entity\Book;
use App\Entity\User;
use App\Service\Authentication;
use App\Service\Cache;
use App\Service\Package;
use App\Service\Qss;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class QssTestCase extends WebTestCase
{
    protected function up(bool $overrideUser=true) {
        $client = self::createClient();
        $container = self::$container;
        Cache::$_mock = true;
        Call::$_mock = true;

        if($overrideUser === true) {
            $this->addUserOverride($container);
        }

        return $client;
    }

    protected function down() {
        Cache::$_mock = false;
        Call::$_mock = false;
        Call::$_mockedCallResponses = array();
    }

    /**
     * @param string $serviceName
     * @return object|Qss|Package
     */
    protected function getService(string $serviceName) {
        $container = $this->getContainer();
        return $container->get('App\Service\\' . $serviceName);
    }

    /**
     * @param string $apiName
     * @return object|\App\Api\Author|\App\Api\Book
     */
    protected function getApi(string $apiName) {
        $container = $this->getContainer();
        return $container->get('App\Api\\' . $apiName);
    }

    protected function getContainer() {
        self::bootKernel();
        return self::$container;
    }

    protected function makeRedirectAssertions(KernelBrowser $client, array $parameters, ContainerInterface $container, string $route, string $redirectRoute, string $flashKey, string $flashMessage) {
        $client->request("POST", $route, $parameters);
        $response = $client->getResponse();
        $this->assertTrue($response->isRedirect($redirectRoute));
        $session = $container->get("Symfony\Component\HttpFoundation\Session\SessionInterface");
        $warnings = $session->getFlashBag()->get($flashKey);
        $this->assertEquals($flashMessage, $warnings[count($warnings) - 1]);
    }

    protected function makeAuthorTestArray(int $id, string $name, string $lname, string $biography, string $birthDay, string $gender, string $placeOfBirth) {
        return array(
            'id' => $id,
            'first_name' => $name,
            'last_name' => $lname,
            'birthday' => $birthDay,
            'gender' => $gender,
            "biography" => $biography,
            'place_of_birth' => $placeOfBirth
        );
    }

    protected function makeAuthorAssertions(Author $author, array $testArray, $expectedBirthDay) {
        $this->assertInstanceOf(Author::class, $author);
        $this->assertEquals($testArray["first_name"], $author->getName());
        $this->assertEquals($testArray["last_name"], $author->getLName());
        $this->assertEquals($testArray["biography"], $author->getBiography());
        $this->assertEquals($expectedBirthDay, $author->getBirthDay());
        $this->assertEquals($testArray["place_of_birth"], $author->getPlaceOfBirth());
        $this->assertEquals($testArray["gender"], $author->getGender());
        $this->assertEquals($testArray["id"], $author->getId());
    }

    protected function makeBookTestArray(int $id, string $title, string $format, string $isbn, string $releaseDate, int $numOfPages, string $description) {
        return array(
            'id' => $id,
            'title' => $title,
            'format' => $format,
            'isbn' => $isbn,
            'release_date' => $releaseDate,
            'number_of_pages' => $numOfPages,
            'description' => $description,
        );
    }

    protected function makeBookAssertions(Book $book, array $testArray, string $expectedReleaseDate) {
        $this->assertInstanceOf(Book::class, $book);
        $this->assertEquals($testArray["id"], $book->getId());
        $this->assertEquals($testArray["description"], $book->getDescription());
        $this->assertEquals($testArray["isbn"], $book->getIsbn());
        $this->assertEquals($testArray["format"], $book->getFormat());
        $this->assertEquals($testArray["number_of_pages"], $book->getNumOfPages());
        $this->assertEquals($expectedReleaseDate, $book->getReleaseDate());
        $this->assertEquals($testArray["title"], $book->getTitle());
    }

    protected function makeUserTestArray(string $name, string $lname, string $email, string $token) {
        return array(
            'user' => array(
                'first_name' => $name,
                'last_name' => $lname,
                'email' => $email
            ),
            'token_key' => $token
        );
    }

    protected function makeUserAssertions(User $user, array $testArray) {
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($testArray["user"]["first_name"], $user->getName());
        $this->assertEquals($testArray["user"]["last_name"], $user->getLName());
        $this->assertEquals($testArray["user"]["email"], $user->getEmail());
        $this->assertEquals($testArray["token_key"], $user->getToken());
    }

    protected function addUserOverride(ContainerInterface $container) {
        $session = $container->get("Symfony\Component\HttpFoundation\Session\SessionInterface");
        $userEmail = "totalyFakeEmail1234554321@fake.fake";
        $session->set(Authentication::USER_SESSION_KEY, $userEmail);
        $hash = array("email" => $userEmail);

        $tokenOverride = "12345678";
        $userName = "UserName";
        $userLName = "UserLName";
        $user = new User();
        $user->setName($userName);
        $user->setLName($userLName);
        $user->setToken($tokenOverride);
        Cache::load()->set(Cache::USER_CACHE_KEY, $user, $hash);
    }
}