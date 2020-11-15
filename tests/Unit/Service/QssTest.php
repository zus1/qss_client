<?php

namespace App\Tests;

use App\Api\Call;
use App\Entity\Author;
use App\Entity\Book;
use App\Service\Cache;
use App\Service\Qss;

class QssTest extends QssTestCase
{
    private function localUp() {
        Call::$_mock = true;
        Cache::$_mock = true;

        $qss = $this->getService("Qss");
        $author = $this->getApiWithTokenOverride("Author");
        $book = $this->getApiWithTokenOverride("Book");

        $services = new \stdClass();
        $services->qss = $qss;
        $services->author = $author;
        $services->book = $book;

        return $services;
    }

    public function testAuthors() {
        $services = $this->localUp();

        $testArray = array(
            $this->makeAuthorTestArray(1, "Bubu", "Lubu", "Lorem ipsum bipsum tipsum kimpsum opsum dopsum",
                "1986-08-09T00:00:00.000+00", "female", "Earth"),
            $this->makeAuthorTestArray(1, "Donald", "Trump", "var dump Trump var dump Trump",
                "1986-08-09T00:00:00.000+00", "female", "Born in SAD"),
        );
        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/authors"][Call::METHOD_GET] = $testArray;

        $authors = $services->qss->setCallClass($services->author)->authors();
        $this->assertCount(2, $authors);

        $expectedBirthday = "1986-08-09";
        for($i = 0; $i < count($authors); $i++) {
            $this->makeAuthorAssertions($authors[$i], $testArray[$i], $expectedBirthday);
        }

        $cachedAuthors = Cache::load()->get(Cache::AUTHORS_CACHE_KEY);
        $this->assertEquals($authors, $cachedAuthors);

        $this->down();
    }

    public function testAuthorWithBooks() {
        $services = $this->localUp();
        $authorId = 5;

        $booksTestArray = array(
            $this->makeBookTestArray(1, "Eine kleine schweine", "1 x 1", "987654321123",
                "2020-11-14T00:00:00.000+00", 356, "Eine kleine schweine meets Eine bige woolfe"),
            $this->makeBookTestArray(2, "Breaf history of my oreo cookie", "1 x 1", "987654321123",
                "2020-11-14T00:00:00.000+00", 1, "I came, i saw i eat "),
        );
        $authorTestArray = $this->makeAuthorTestArray(1, "Bubu", "Lubu", "Lorem ipsum bipsum tipsum kimpsum opsum dopsum",
            "1986-08-09T00:00:00.000+00", "female", "Earth");
        $authorTestArray["books"] = $booksTestArray;
        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/authors/" . $authorId][Call::METHOD_GET] = $authorTestArray;

        $authorWithBooks = $services->qss->setCallClass($services->author)->authorWithBooks($authorId);
        $this->assertCount(2, $authorWithBooks);
        $this->assertCount(2, $authorWithBooks["books"]);

        $author = $authorWithBooks["author"];
        $books = $authorWithBooks["books"];

        $expectedBirthday = "1986-08-09";
        $this->makeAuthorAssertions($author, $authorTestArray, $expectedBirthday);
        $expectedReleaseDate = "2020-11-14";
        for($i = 0; $i < count($books); $i++) {
            $this->makeBookAssertions($books[$i], $booksTestArray[$i], $expectedReleaseDate);
        }

        $hash = array("author_id" => $authorId);
        $cachedAuthorWithBooks = Cache::load()->get(Cache::AUTHOR_BOOKS_CACHE_KEY, $hash);
        $this->assertEquals($authorWithBooks, $cachedAuthorWithBooks);

        $this->down();
    }

    public function testAuthorDelete() {
        $services = $this->localUp();
        $authorId = 9999;

        $errorMessage = "Api Error";
        $code = 404;
        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/authors/" . $authorId][Call::METHOD_DELETE] = array("error" => 1, "message" => $errorMessage, "code" => $code);

        $this->makeExceptionAssertion($services->qss, $services->author, "author_delete", $errorMessage, $code, array($authorId));

        $this->down();
    }

    public function testAuthorAdd() {
        $services = $this->localUp();

        $authorEntity = new Author();
        $authorEntity->setName("name");
        $authorEntity->setLName("lName");
        $authorEntity->setGender("male");
        $authorEntity->setPlaceOfBirth("Mars");
        $authorEntity->setBiography("Biography");
        $authorEntity->setBirthDay("2019-01-01");

        $errorMessage = "Api Error";
        $code = 404;
        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/authors"][Call::METHOD_POST] = array("error" => 1, "message" => $errorMessage, "code" => $code);

        $this->makeExceptionAssertion($services->qss, $services->author, "author_add", $errorMessage, $code, array($authorEntity));

        $this->down();
    }

    public function testBookAdd() {
        $services = $this->localUp();

        $authorId = 4321;
        $bookEntity = new Book();
        $bookEntity->setTitle("title");
        $bookEntity->setIsbn("Isbn");
        $bookEntity->setFormat("1 x 1");
        $bookEntity->setNumOfPages(10);
        $bookEntity->setDescription("Blu bla blu");
        $bookEntity->setReleaseDate("1000-01-01");

        $errorMessage = "Api Error";
        $code = 404;
        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/books"][Call::METHOD_POST] = array("error" => 1, "message" => $errorMessage, "code" => $code);

        $this->makeExceptionAssertion($services->qss, $services->book, "book_add", $errorMessage, $code, array($authorId, $bookEntity));

        $this->down();
    }

    public function testBookDelete() {
        $services = $this->localUp();

        $authorId = 1234;
        $bookId = 4321;

        $errorMessage = "Api Error";
        $code = 404;
        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/books/" . $bookId][Call::METHOD_DELETE] = array("error" => 1, "message" => $errorMessage, "code" => $code);

        $this->makeExceptionAssertion($services->qss, $services->book, "book_delete", $errorMessage, $code, array($bookId, $authorId));

        $this->down();
    }

    private function makeExceptionAssertion(Qss $qss, Call $apiCallObj, string $callType, $errorMessage, $code, ?array $params=array()) {
        $typeToCallMethodMapping = array(
            'author_delete' => "exceptionAuthorDeleteCall",
            'author_add' => "exceptionAuthorAddCall",
            "book_add" => "exceptionBookAddCall",
            "book_delete" => "exceptionBookDeleteCall"
        );
        $params = array_merge(array($apiCallObj, $qss), $params);
        $thrown = false;
        try {
            call_user_func_array(array($this, $typeToCallMethodMapping[$callType]), $params);
        } catch(\Exception $e) {
            $thrown = true;
            $this->assertEquals($errorMessage, $e->getMessage());
            $this->assertEquals($code, $e->getCode());
        }
        $this->assertTrue($thrown);
    }

    private function exceptionAuthorDeleteCall(Call $apiCallObj, Qss $qss, int $authorId) {
        $qss->setCallClass($apiCallObj)->authorDelete($authorId);
    }

    private function exceptionAuthorAddCall(Call $apiCallObj, Qss $qss, Author $author) {
        $qss->setCallClass($apiCallObj)->authorAdd($author);
    }

    private function exceptionBookAddCall(Call $apiCallObj, Qss $qss, int $authorId, Book $book) {
        $qss->setCallClass($apiCallObj)->bookAdd($authorId, $book);
    }

    private function getApiWithTokenOverride(string $apiName) {
        $api = $this->getApi($apiName);
        $api->setTokenOverride("12345");

        return $api;
    }

    private function exceptionBookDeleteCall(Call $apiCallObj, Qss $qss, int $bookId, int $authorId) {
        $qss->setCallClass($apiCallObj)->bookDelete($bookId, $authorId);
    }
}
