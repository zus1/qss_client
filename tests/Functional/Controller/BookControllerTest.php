<?php

namespace App\Tests;

use App\Api\Call;


class BookControllerTest extends QssTestCase
{
    public function testAddBook() {
        $client = $this->up();

        $authorNameOne = "Bubu";
        $authorNameTwo = "Donald";
        $authorLNameOne = "Lubu";
        $authorLNameTwo = "Trump";
        $authorsTestArray = array(
            $this->makeAuthorTestArray(1, "Bubu", "Lubu", "Lorem ipsum bipsum tipsum kimpsum opsum dopsum",
                "1986-08-09T00:00:00.000+00", "female", "Earth"),
            $this->makeAuthorTestArray(1, "Donald", "Trump", "var dump Trump var dump Trump",
                "1986-08-09T00:00:00.000+00", "female", "Born in SAD"),
        );
        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/authors"][Call::METHOD_GET] = $authorsTestArray;

        $client->request("GET", "/book-add");
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());

        $content = $response->getContent();
        $this->assertNotFalse(strpos($content, $authorNameOne));
        $this->assertNotFalse(strpos($content, $authorNameTwo));
        $this->assertNotFalse(strpos($content, $authorLNameOne));
        $this->assertNotFalse(strpos($content, $authorLNameTwo));

        $isbns = array(
            '9787426660698', '9784849037151', '9788809476356', '9781283885966', '9781996579350',
            '9784246380256', '9783691963885', '9788956929293', '9786231938756', '9786347958303',
            '9782363323828', '9782350414294', '9783623754284', '9780653311302', '9783428417926',
            '9780683441208', '9780874644791', '9780102092837', '9789509588110', '9787663448745',
        );

        $isbnsFound = 0;
        foreach($isbns as $isbn) {
            if(strpos($response, $isbn)) {
                $isbnsFound++;
            }
        }
        $this->assertEquals(10, $isbnsFound);

        $this->down();
    }

    public function testDoAddBook() {
        $client = $this->up();

        $errorMessage = "Api Error";
        $code = 404;
        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/books"][Call::METHOD_POST] = array("error" => 1, "message" => $errorMessage, "code" => $code);

        $parameters = array(
            'title' => "Valid title",
            'release_date' => "2020-11-11",
            'format' => "1 x 1",
            'num_of_pages' => 123,
            'description' => "description one",
        );
        $this->makeRedirectAssertions($client, $parameters, self::$container, "/book-do-add", "/book-add", "warning", "Author is required");

        $parameters["author_id"] = 123;
        $this->makeRedirectAssertions($client, $parameters, self::$container, "/book-do-add", "/book-add", "warning", "ISBN is required");

        $parameters["isbn"] = "1234567891234";
        $parameters["title"] = "Invalid title 123@";
        $this->makeRedirectAssertions($client, $parameters, self::$container, "/book-do-add", "/book-add", "warning", "Title can only contain letters, numbers and spaces.");

        $parameters["title"] = "Valid title";
        $parameters["release_date"] = "fsgdh";
        $this->makeRedirectAssertions($client, $parameters, self::$container, "/book-do-add", "/book-add", "warning", "Release date is not valid date.");

        $parameters["release_date"] = "2020-11-11";
        $parameters["format"] = "100{} : 100[]";
        $this->makeRedirectAssertions($client, $parameters, self::$container, "/book-do-add", "/book-add", "warning", "Format is invalid.");

        $parameters["format"] = "100 x 100";
        $parameters["num_of_pages"] = "abcd";
        $this->makeRedirectAssertions($client, $parameters, self::$container, "/book-do-add", "/book-add", "warning", "Number of pages must be a number.");

        $parameters["num_of_pages"] = 123;
        $parameters["description"] = "invalid description {} [] @";
        $this->makeRedirectAssertions($client, $parameters, self::$container, "/book-do-add", "/book-add", "warning", "Description contains invalid characters.");

        $parameters["description"] = "This is valid description!";
        $parameters["isbn"] = "dagfsg";
        $this->makeRedirectAssertions($client, $parameters, self::$container, "/book-do-add", "/book-add", "warning", "Isbn can only contain only numbers.");

        $parameters["isbn"] = "1234567891234";
        $this->makeRedirectAssertions($client, $parameters, self::$container, "/book-do-add", "/book-add", "warning", "Api Error");

        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/books"][Call::METHOD_POST] = array("error" => 0, "message" => "Bok added", "code" => 200);

        $this->makeRedirectAssertions($client, $parameters, self::$container, "/book-do-add", "/book-add", "success", "Book Added");

        $this->down();
    }

    public function testAjaxDeleteBook() {
        $client = $this->up();
        $bookId = 1234;
        $authorId = 4321;

        $errorMessage = "Api Error";
        $code = 404;
        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/books/" . $bookId][Call::METHOD_DELETE] = array("error" => 1, "message" => $errorMessage, "code" => $code);

        $client->request("POST", "/book-delete/author/" . $authorId . "/book/" . $bookId);
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());
        $this->assertEquals('{"error":1,"message":"Api Error"}', $response->getContent());

        Call::$_mockedCallResponses["https://symfony-skeleton.q-tests.com/api/v2/books/" . $bookId][Call::METHOD_DELETE] = array("error" => 0, "message" => "ok", "code" => $code);

        $client->request("POST", "/book-delete/author/" . $authorId . "/book/" . $bookId);
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());
        $this->assertEquals('{"error":0,"message":"Book deleted","book_id":1234}', $response->getContent());

        $this->down();
    }
}
