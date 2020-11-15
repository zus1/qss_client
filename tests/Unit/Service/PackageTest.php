<?php

namespace App\Tests;

use App\Service\Package;

class PackageTest extends QssTestCase
{
    public function testPackageAuthor()
    {
        $package = $this->getService("Package");

        $testArray = $this->makeAuthorTestArray(1, "Bubu", "Lubu", "Lorem ipsum bipsum tipsum kimpsum opsum dopsum",
            "10/10/2020", "female", "Earth");

        $author = $package->package(Package::PACKAGE_AUTHOR, $testArray);
        $expectedBirthDay = "2020-10-10";
        $this->makeAuthorAssertions($author, $testArray, $expectedBirthDay);
    }

    public function testPackageBook() {
        $package = $this->getService("Package");

        $testArray = $this->makeBookTestArray(1, "Eine kleine schweine", "1 x 1", "987654321123",
            "1986-08-09T00:00:00.000+00", 356, "Eine kleine schweine meets Eine bige woolfe");
        $book = $package->package(Package::PACKAGE_BOOK, $testArray);
        $expectedReleaseDate = "1986-08-09";
        $this->makeBookAssertions($book, $testArray, $expectedReleaseDate);
    }

    public function testPackageUser() {
        $package = $this->getService("Package");

        $testArray = $this->makeUserTestArray("Indy", "Jones", "indy.jones@awsome.com", "1a2b3c4d5e");
        $user = $package->package(Package::PACKAGE_USER, $testArray);
        $this->makeUserAssertions($user, $testArray);
    }

    public function testPackageAuthors() {
        $package = $this->getService("Package");

        $testArray = array(
            $this->makeAuthorTestArray(1, "Bubu", "Lubu", "Lorem ipsum bipsum tipsum kimpsum opsum dopsum",
                "1986-08-09T00:00:00.000+00", "female", "Earth"),
            $this->makeAuthorTestArray(1, "Donald", "Trump", "var dump Trump var dump Trump",
                "1986-08-09T00:00:00.000+00", "female", "Born in SAD"),
        );
        $authors = $package->package(Package::PACKAGE_AUTHORS, $testArray);
        $this->assertIsArray($authors);
        $this->assertCount(2, $authors);

        $expectedBirthday = "1986-08-09";
        for($i = 0; $i < count($authors); $i++) {
            $this->makeAuthorAssertions($authors[$i], $testArray[$i], $expectedBirthday);
        }
    }

    public function testPackageAuthorWithBook() {
        $package = $this->getService("Package");

        $booksTestArray = array(
            $this->makeBookTestArray(1, "Eine kleine schweine", "1 x 1", "987654321123",
                "2020-11-14T00:00:00.000+00", 356, "Eine kleine schweine meets Eine bige woolfe"),
            $this->makeBookTestArray(2, "Breaf history of my oreo cookie", "1 x 1", "987654321123",
                "2020-11-14T00:00:00.000+00", 1, "I came, i saw i eat "),
        );
        $authorTestArray = $this->makeAuthorTestArray(1, "Bubu", "Lubu", "Lorem ipsum bipsum tipsum kimpsum opsum dopsum",
            "1986-08-09T00:00:00.000+00", "female", "Earth");
        $authorTestArray["books"] = $booksTestArray;

        $authorWithBooks = $package->package(Package::PACKAGE_AUTHOR_BOOKS, $authorTestArray);
        $this->assertIsArray($authorWithBooks);
        $this->assertCount(2, $authorWithBooks);
        $this->assertCount(2, $authorWithBooks["books"]);;

        $author = $authorWithBooks["author"];
        $books = $authorWithBooks["books"];

        unset($authorTestArray["books"]);
        $expectedBirthday = "1986-08-09";
        $this->makeAuthorAssertions($author, $authorTestArray, $expectedBirthday);

        $expectedReleaseDate = "2020-11-14";
        for($i = 0; $i < count($books); $i++) {
            $this->makeBookAssertions($books[$i], $booksTestArray[$i], $expectedReleaseDate);
        }
    }
}
