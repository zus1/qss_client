<?php
declare(strict_types = 1);

namespace App\Service;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\User;
use Exception;

class Package
{
    const PACKAGE_AUTHOR = "author";
    const PACKAGE_AUTHORS = "authors";
    const PACKAGE_BOOK = "book";
    const PACKAGE_AUTHOR_BOOKS = "author-books";
    const PACKAGE_USER = "user";

    /**
     * @return array
     */
    private function packageToMethodMapping() : array {
        return array(
            self::PACKAGE_AUTHOR => "packageAuthor",
            self::PACKAGE_BOOK => "packageBook",
            self::PACKAGE_AUTHOR_BOOKS => "packageAuthorsWithBooks",
            self::PACKAGE_USER => "packageUser",
            self::PACKAGE_AUTHORS => "packageAuthors",
        );
    }

    /**
     *
     * Package api response values to entity objects
     *
     * @param string $packageType
     * @param array $unpacked
     * @return mixed
     * @throws Exception
     */
    public function package(string $packageType, array $unpacked) {
        if(!array_key_exists($packageType, $this->packageToMethodMapping())) {
            throw new Exception("Invalid package");
        }

        return call_user_func_array(array($this, $this->packageToMethodMapping()[$packageType]), array($unpacked));
    }

    /**
     * @param array $response
     * @return array
     * @throws Exception
     */
    private function packageAuthorsWithBooks(array $response) : array {
        $package = array(
            "author" => $this->packageAuthor($response),
            "books" => array()
        );
        $books = $response["books"];
        array_walk($books, function (array $bookArr) use (&$package) {
            $package["books"][] = $this->packageBook($bookArr);
        });

        return $package;
    }

    /**
     * @param array $response
     * @return array
     */
    private function packageAuthors(array $response) : array {
        $package = array();
        array_walk($response, function (array $authorArr) use (&$package) {
            $package[] = $this->packageAuthor($authorArr);
        });

        return $package;
    }

    /**
     * @param array $authorArr
     * @return Author
     * @throws Exception
     */
    private function packageAuthor(array $authorArr) : Author {
        $author = new Author();
        $author->setId($authorArr["id"]);
        $author->setName($authorArr["first_name"]);
        $author->setLName($authorArr["last_name"]);
        $author->setBirthDay($authorArr["birthday"]);
        $author->setGender($authorArr["gender"]);
        $author->setBiography($authorArr["biography"]);
        $author->setPlaceOfBirth($authorArr["place_of_birth"]);
        return $author;
    }

    /**
     * @param array $bookArr
     * @return Book
     * @throws Exception
     */
    private function packageBook(array $bookArr) : Book {
        $book = new Book();
        $book->setId($bookArr["id"]);
        $book->setTitle($bookArr["title"]);
        $book->setFormat($bookArr["format"]);
        $book->setIsbn($bookArr["isbn"]);
        $book->setReleaseDate($bookArr["release_date"]);
        $book->setNumOfPages($bookArr["number_of_pages"]);
        if(isset($bookArr["description"])) {
            $book->setDescription($bookArr["description"]);
        }
        return $book;
    }

    /**
     * @param array $response
     * @return User
     */
    private function packageUser(array $response) : User {
        $user = new User();
        $user->setName($response["user"]["first_name"]);
        $user->setLName($response["user"]["last_name"]);
        $user->setEmail($response["user"]["email"]);
        $user->setToken($response["token_key"]);
        return $user;
    }
}