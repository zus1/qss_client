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

    public function setCallClass(Call $call) {
        $this->call = $call;
        return $this;
    }

    /**
     * @param string $email
     * @param string $password
     * @return User
     * @throws Exception
     */
    public function authenticateUser(string $email, string $password) {
        $user = Cache::load()->get(Cache::USER_CACHE_KEY, array("email" => $email));
        if(!empty($user)) {
            throw new Exception("Already logged in");
        }

        $user = $this->call->login($email, $password);

        $this->session->set(Authentication::USER_SESSION_KEY, $email);
        Cache::load()->set(Cache::USER_CACHE_KEY, $user, array("email" => $email), (int)Env::load()->get("QSS_USER_TTL_MIN", "60") *60);

        return $user;
    }

    public function authors() {
        $authors = Cache::load()->get(Cache::AUTHORS_CACHE_KEY);
        if(!empty($authors)) {
            return $authors;
        }

        $authors = $this->call->getAuthors();
        Cache::load()->set(Cache::AUTHORS_CACHE_KEY, $authors, array(), (int)Env::load()->get("QSS_AUTHORS_TTL_MIN", "10") *60);

        return $authors;
    }

    public function authorWithBooks(int $authorId) {
        $authorWithBooks = Cache::load()->get(Cache::AUTHOR_BOOKS_CACHE_KEY, array("author_id" => $authorId));
        if(!empty($authorWithBooks)) {
            return $authorWithBooks;
        }

        $authorWithBooks = $this->call->getAuthorWithBooks($authorId);

        Cache::load()->set(Cache::AUTHOR_BOOKS_CACHE_KEY, $authorWithBooks, array("author_id" => $authorId), (int)Env::load()->get("QSS_AUTHOR_WITH_BOOKS_TTL_MIN", "10") *60);

        return $authorWithBooks;
    }

    public function authorDelete(int $authorId) {
        $this->call->deleteAuthor($authorId);
        Cache::load()->delete(Cache::AUTHORS_CACHE_KEY);
    }

    public function authorAdd(Author $author) {
        $this->call->addAuthor($author);
        Cache::load()->delete(Cache::AUTHORS_CACHE_KEY);
    }

    public function bookAdd(int $authorId, Book $book) {
        $this->call->addBook($authorId, $book);
        Cache::load()->delete(Cache::AUTHOR_BOOKS_CACHE_KEY, array("author_id" => $authorId));
    }

    public function bookDelete(int $bookId, int $authorId) {
        $this->call->deleteBook($bookId);
        Cache::load()->delete(Cache::AUTHOR_BOOKS_CACHE_KEY, array("author_id" => $authorId));
    }

    public function addNumberOfBooksForAuthors(array $authors) {
        array_walk($authors, function (Author $author) {
           $withBooks = $this->authorWithBooks($author->getId());
           $author->setNumberOfBooks(count($withBooks["books"]));
        });

        return $authors;
    }
}