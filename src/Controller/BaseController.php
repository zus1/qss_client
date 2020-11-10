<?php

namespace App\Controller;

use App\Api\Author;
use App\Api\Book;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseController extends AbstractController {

    /**
     * @Route("/test", name="test")
     */
    public function testApi(Author $author, Book $book) {
        $authorId = 95106;
        $response = $author->getAuthorWithBooks($authorId);
        //$response = $author->getAuthors();
        dd($response);

        /*$bookId = 950653;
        $response = $book->deleteBook($bookId);
        dd($response);*/

        /*$authorId = 95106;
        $bookEntity = new \App\Entity\Book();
        $bookEntity->setTitle("Strange Book");
        $bookEntity->setReleaseDate("2020-01-01 12:11:10");
        $bookEntity->setNumOfPages(300);
        $bookEntity->setFormat("100 x 100");
        $bookEntity->setIsbn(9788913365324);
        $bookEntity->setDescription("Dono, its to strange");

        $response = $book->addBook($authorId, $bookEntity);
        dd($response);*/
    }

    protected function makeValidation(ValidatorInterface $validator, $entity, string $name, string $value) {
        $failed = $validator->validatePropertyValue($entity, $name, $value);
        if($failed->count()) {
            throw new Exception($failed->get(0)->getMessage());
        }
    }

}