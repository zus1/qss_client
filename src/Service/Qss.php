<?php
declare(strict_types = 1);

namespace App\Service;

use App\Api\Call;
use App\Entity\Author;
use App\Entity\Book;
use App\Entity\User;
use Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Qss
{
    private $call;
    private $session;

    public function __construct(SessionInterface $session) {
        $this->session = $session;
    }

    /**
     *
     * Sets object of call class that will be used to make requests
     *
     * @param Call $call
     * @return $this
     */
    public function setCallClass(Call $call) : Qss {
        $this->call = $call;
        return $this;
    }

    /**
     *
     * Handler fo authentication call
     *
     * @param string $email
     * @param string $password
     * @return User
     * @throws Exception
     */
    public function authenticateUser(string $email, string $password) : User {
        $user = Cache::load()->get(Cache::USER_CACHE_KEY, array("email" => $email));
        if(!empty($user)) {
            throw new Exception("Already logged in");
        }

        $user = $this->call->login($email, $password);

        $this->session->set(Authentication::USER_SESSION_KEY, $email);
        Cache::load()->set(Cache::USER_CACHE_KEY, $user, array("email" => $email), (int)Env::load()->get("QSS_USER_TTL_MIN", "60") *60);

        return $user;
    }

    /**
     *
     * Handles authors list call
     *
     * @return array
     */
    public function authors() : array {
        $authors = Cache::load()->get(Cache::AUTHORS_CACHE_KEY);
        if(!empty($authors)) {
            return $authors;
        }

        $authors = $this->call->getAuthors();
        Cache::load()->set(Cache::AUTHORS_CACHE_KEY, $authors, array(), (int)Env::load()->get("QSS_AUTHORS_TTL_MIN", "10") *60);

        return $authors;
    }

    /**
     *
     * Handles author with book call
     *
     * @param int $authorId
     * @return array
     */
    public function authorWithBooks(int $authorId) : array {
        $authorWithBooks = Cache::load()->get(Cache::AUTHOR_BOOKS_CACHE_KEY, array("author_id" => $authorId));
        if(!empty($authorWithBooks)) {
            return $authorWithBooks;
        }

        $authorWithBooks = $this->call->getAuthorWithBooks($authorId);

        Cache::load()->set(Cache::AUTHOR_BOOKS_CACHE_KEY, $authorWithBooks, array("author_id" => $authorId), (int)Env::load()->get("QSS_AUTHOR_WITH_BOOKS_TTL_MIN", "10") *60);

        return $authorWithBooks;
    }

    /**
     *
     * Handles author delete call
     *
     * @param int $authorId
     */
    public function authorDelete(int $authorId) : void {
        $this->call->deleteAuthor($authorId);
        Cache::load()->delete(Cache::AUTHORS_CACHE_KEY);
    }

    /**
     *
     * Handles add author call
     *
     * @param Author $author
     */
    public function authorAdd(Author $author) : void {
        $this->call->addAuthor($author);
        Cache::load()->delete(Cache::AUTHORS_CACHE_KEY);
    }

    /**
     *
     * Handles add book call
     *
     * @param int $authorId
     * @param Book $book
     */
    public function bookAdd(int $authorId, Book $book) : void {
        $this->call->addBook($authorId, $book);
        Cache::load()->delete(Cache::AUTHOR_BOOKS_CACHE_KEY, array("author_id" => $authorId));
    }

    /**
     *
     * Handles delete book call
     *
     * @param int $bookId
     * @param int $authorId
     */
    public function bookDelete(int $bookId, int $authorId) : void {
        $this->call->deleteBook($bookId);
        Cache::load()->delete(Cache::AUTHOR_BOOKS_CACHE_KEY, array("author_id" => $authorId));
    }

    /**
     *
     * Adds value of total authors books, to Author objects
     *
     * @param array $authors
     * @return array
     */
    public function addNumberOfBooksForAuthors(array $authors) : array {
        array_walk($authors, function (Author $author) {
           $withBooks = $this->authorWithBooks($author->getId());
           $author->setNumberOfBooks(count($withBooks["books"]));
        });

        return $authors;
    }
}