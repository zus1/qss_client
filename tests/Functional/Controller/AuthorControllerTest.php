<?php

namespace App\Tests;

use App\Api\Call;
use App\Service\Cache;

class AuthorControllerTest extends QssTestCase
{
    public function testListAuthors() {
        $client = $this->up();

        $authorNameOne = "Bubu";
        $authorLNameOne = "Lubu";
        $authorNameTwo = "Donald";
        $authorLNameTwo = "BigBaby";
        $authorIdOne = 1;
        $authorIdTwo = 2;
        Cache::load()->delete(Cache::AUTHORS_CACHE_KEY);
        Cache::load()->delete(Cache::AUTHOR_BOOKS_CACHE_KEY, array("author_id" => $authorIdOne));
        Cache::load()->delete(Cache::AUTHOR_BOOKS_CACHE_KEY, array("author_id" => $authorIdTwo));
        $errorMessage = "Api Error";

        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/authors"][Call::METHOD_GET] = array("error" => 1, "message" => $errorMessage, "code" => 404);
        $client->request("GET", "/author-list");
        $response = $client->getResponse();
        $content = $response->getContent();
        $this->assertNotFalse(strpos($content, "No authors found"));
        $this->assertNotFalse(strpos($content, $errorMessage));

        $authorsTestArray = array(
            $this->makeAuthorTestArray($authorIdOne, $authorNameOne, $authorLNameOne, "Lorem ipsum bipsum tipsum kimpsum opsum dopsum",
                "1986-08-09T00:00:00.000+00", "female", "Earth"),
            $this->makeAuthorTestArray($authorIdTwo, $authorNameTwo, $authorLNameTwo, "var dump Trump var dump Trump",
                "1986-08-09T00:00:00.000+00", "female", "Born in SAD"),
        );
        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/authors"][Call::METHOD_GET] = $authorsTestArray;

        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/authors/" . $authorIdOne][Call::METHOD_GET] = array("error" => 1, "message" => $errorMessage, "code" => 404);
        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/authors/" . $authorIdTwo][Call::METHOD_GET] = array("error" => 1, "message" => $errorMessage, "code" => 404);
        $client->request("GET", "/author-list");
        $response = $client->getResponse();
        $content = $response->getContent();
        $this->assertNotFalse(strpos($content, "delete-author-1"));
        $this->assertNotFalse(strpos($content, "delete-author-2"));
        $this->assertNotFalse(strpos($content, $errorMessage));


        $booksTestArray = array(
            $this->makeBookTestArray(1, "Eine kleine schweine", "1 x 1", "987654321123",
                "2020-11-14T00:00:00.000+00", 356, "Eine kleine schweine meets Eine bige woolfe"),
            $this->makeBookTestArray(2, "Breaf history of my oreo cookie", "1 x 1", "987654321123",
                "2020-11-14T00:00:00.000+00", 1, "I came, i saw i eat "),
        );

        $authorWithBooksTestArrayOne = $this->getAuthorWithBooksTestArray($authorIdOne, $authorsTestArray[0], array($booksTestArray[0]));
        $authorWithBooksTestArrayTwo = $this->getAuthorWithBooksTestArray($authorIdTwo, $authorsTestArray[1], array());

        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/authors/" . $authorIdOne][Call::METHOD_GET] = $authorWithBooksTestArrayOne;
        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/authors/" . $authorIdTwo][Call::METHOD_GET] = $authorWithBooksTestArrayTwo;

        $client->request("GET", "/author-list");
        $response = $client->getResponse();

        $this->assertTrue($response->isOk());
        $content = $response->getContent();

        $this->assertNotFalse(strpos($content, "/author-preview/1"));
        $this->assertNotFalse(strpos($content, "/author-preview/2"));
        $this->assertFalse(strpos($content, "delete-author-1"));
        $this->assertNotFalse(strpos($content, "delete-author-2"));
        $this->assertNotFalse(strpos($content, $authorNameOne));
        $this->assertNotFalse(strpos($content, $authorLNameOne));
        $this->assertNotFalse(strpos($content, $authorNameTwo));
        $this->assertNotFalse(strpos($content, $authorLNameTwo));

        $this->down();
    }

    public function testPreviewAuthor() {
        $client = $this->up();

        $authorId = 1;
        Cache::load()->delete(Cache::AUTHOR_BOOKS_CACHE_KEY, array("author_id" => $authorId));

        $errorMessage = "Api Error";
        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/authors/" . $authorId][Call::METHOD_GET] = array("error" => 1, "message" => $errorMessage, "code" => 404);

        $client->request("GET", "/author-preview/" . $authorId);
        $response = $client->getResponse();
        $this->assertTrue($response->isRedirect("/author-list"));
        $session = self::$container->get("Symfony\Component\HttpFoundation\Session\SessionInterface");
        $warnings = $session->getFlashBag()->get("warning");
        $this->assertEquals("Api Error", $warnings[0]);

        $authorName = "Bubu";
        $authorLName = "Lubu";
        $bookIdOne = 123;
        $bookTitleOne = "Eine kleine schweine";
        $bookIsbnOne = "987654321123";
        $bookIdTwo = 321;
        $bookTitleTwo = "Breaf history of my oreo cookie";
        $bookIsbnTwo = "987654321321";

        $authorTestArray = $this->makeAuthorTestArray($authorId, $authorName, $authorLName, "Lorem ipsum bipsum tipsum kimpsum opsum dopsum",
            "1986-08-09T00:00:00.000+00", "female", "Earth");

        $booksTestArray = array(
            $this->makeBookTestArray($bookIdOne, $bookTitleOne, "1 x 1", $bookIsbnOne,
                "2020-11-14T00:00:00.000+00", 356, "Eine kleine schweine meets Eine bige woolfe"),
            $this->makeBookTestArray($bookIdTwo, $bookTitleTwo, "1 x 1", $bookIsbnTwo,
                "2020-11-14T00:00:00.000+00", 1, "I came, i saw i eat "),
        );

        $authorWithBooksTestArray = $this->getAuthorWithBooksTestArray($authorId, $authorTestArray, array($booksTestArray[0], $booksTestArray[1]));

        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/authors/" . $authorId][Call::METHOD_GET] = $authorWithBooksTestArray;

        $client->request("GET", "/author-preview/" . $authorId);
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertTrue($response->isOk());
        $this->assertNotFalse(strpos($content, $authorName));
        $this->assertNotFalse(strpos($content, $authorLName));
        $this->assertNotFalse(strpos($content, $bookTitleOne));
        $this->assertNotFalse(strpos($content, $bookTitleTwo));
        $this->assertNotFalse(strpos($content, $bookIsbnOne));
        $this->assertNotFalse(strpos($content, $bookIsbnTwo));
        $this->assertNotFalse(strpos($content, "delete-book-" . $bookIdOne));
        $this->assertNotFalse(strpos($content, "delete-book-" . $bookIdOne));

        $this->down();
    }

    private function getAuthorWithBooksTestArray(int $authorId, array $authorTestArray, array $books) {
        return array(
            "id" => $authorId,
            "first_name" => $authorTestArray["first_name"],
            "last_name" => $authorTestArray["last_name"],
            "birthday" => $authorTestArray["birthday"],
            "biography" => $authorTestArray["biography"],
            "gender" => $authorTestArray["gender"],
            "place_of_birth" => $authorTestArray["place_of_birth"],
            "books" => $books
        );
    }

    public function testAjaxDeleteAuthor() {
        $client = $this->up();

        $authorId = 123456;

        $errorMessage = "Api Error";
        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/authors/" . $authorId][Call::METHOD_DELETE] = array("error" => 1, "message" => $errorMessage, "code" => 404);

        $client->request("POST", "/author-delete/" . $authorId);
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());
        $this->assertEquals('{"error":1,"message":"Api Error"}', $response->getContent());

        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/authors/" . $authorId][Call::METHOD_DELETE] = array("error" => 0, "message" => "ok", "code" => 200);

        $client->request("POST", "/author-delete/" . $authorId);
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());
        $this->assertEquals('{"error":0,"message":"Author deleted","author_id":' . $authorId . '}', $response->getContent());
    }
}
