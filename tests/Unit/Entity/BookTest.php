<?php

namespace App\Tests;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase {

    public function testBookEntity() {
        $id = 1;
        $description = "Hokus Pokus Fokus";
        $isnb = "1234";
        $format = "100 x 100";
        $numOfPages = 1000000;
        $releaseDate = "01/01/2001";
        $title = "Hokus Pokus";

        $book = new Book();
        $book->setId($id);
        $book->setDescription($description);
        $book->setIsbn($isnb);
        $book->setFormat($format);
        $book->setNumOfPages($numOfPages);
        $book->setReleaseDate($releaseDate);
        $book->setTitle($title);

        $this->assertEquals($id, $book->getId());
        $this->assertEquals($description, $book->getDescription());
        $this->assertEquals($isnb, $book->getIsbn());
        $this->assertEquals($format, $book->getFormat());
        $this->assertEquals($numOfPages, $book->getNumOfPages());
        $expectedReleaseDate = "2001-01-01";
        $this->assertEquals($expectedReleaseDate, $book->getReleaseDate());
        $this->assertEquals($title, $book->getTitle());

        $expectedBookArr = array(
            'title' => $title,
            'isbn' => $isnb,
            'format' => $format,
            'number_of_pages' => $numOfPages,
            "description" => $description,
            "release_date" => "2001-01-01T00:00:00.000+00:00"
        );
        $bookArr = $book->toArray();
        $this->assertEquals($expectedBookArr, $bookArr);
    }
}
