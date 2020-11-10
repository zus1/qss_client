<?php

namespace App\Api;

class Book extends Qss
{
    public function addBook(int $authorId, \App\Entity\Book $book) {
        $book = $book->toArray();
        $book["author"] = array("id" => $authorId);
        //$params = array($book);
        //dd($book);
        $endpoint = $this->env->get("QSS_BOOK_ADD", "/api/v2/books");
        $url = $this->getBaseUrl() . $endpoint;
        $response = $this->callQss($url, $book, self::METHOD_POST);
        $this->handleError($response);

        return null;
    }

    public function deleteBook(int $bookId) {
        $endpoint = $this->env->get("QSS_BOOK_DELETE", "/api/v2/books/{book}");
        $endpoint = str_replace("{book}", $bookId, $endpoint);
        $url = $this->getBaseUrl() . $endpoint;
        $response = $this->callQss($url, array(), self::METHOD_DELETE);
        $this->handleError($response);

        return null;
    }
}