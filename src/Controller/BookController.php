<?php

namespace App\Controller;

use App\Api\Author;
use App\Api\Book;
use App\Services\Env;
use App\Services\Isbn;
use App\Services\Qss;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookController extends BaseController
{
    /**
     * @Route("/book", name="book")
     * @param Qss $qss
     * @param Author $author
     * @param Isbn $isbn
     * @return Response
     */
    public function addBook(Qss $qss, Author $author, Isbn $isbn) : Response {
        $isbnToGenerate = (int)Env::load()->get("ISBN_GENERATE_NUM", 10);
        $isbnArr = $isbn->generate($isbnToGenerate);
        $authors = array();
        try {
            $authors = $qss->setCallClass($author)->authors();
        } catch(Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('book/add.html.twig', [
            'authors' => $authors,
            'isbn' => $isbnArr
        ]);
    }

    /**
     * @Route("/book-add", name="book-add")
     * @param Request $request
     * @param Qss $qss
     * @param Book $bookApi
     * @param ValidatorInterface $validator
     * @return RedirectResponse
     */
    public function doAddBook(Request $request, Qss $qss, Book $bookApi, ValidatorInterface $validator) : RedirectResponse {
        $authorId = $request->request->get("author_id");
        $title = $request->request->get("title");
        $releaseDate = $request->request->get("release_date");
        $isbn = $request->request->get("isbn");
        $format = $request->request->get("format");
        $numPages = $request->request->get("num_of_pages");
        $description = $request->request->get("description");

        $bookEntity = new \App\Entity\Book();

        try {
            $this->makeValidation($validator, new \App\Entity\Author(), "id", $authorId);
            $this->makeValidation($validator, $bookEntity, "title", $title);
            $this->makeValidation($validator, $bookEntity, "releaseDate", $releaseDate);
            $this->makeValidation($validator, $bookEntity, "isbn", $isbn);
            $this->makeValidation($validator, $bookEntity, "format", $format);
            $this->makeValidation($validator, $bookEntity, "numOfPages", $numPages);
            $this->makeValidation($validator, $bookEntity, "description", $description);

            $bookEntity->setTitle($title);
            $bookEntity->setReleaseDate($releaseDate);
            $bookEntity->setIsbn($isbn);
            $bookEntity->setFormat($format);
            $bookEntity->setNumOfPages($numPages);
            $bookEntity->setDescription($description);

            $qss->setCallClass($bookApi)->bookAdd($authorId, $bookEntity);
        } catch(Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('book');
        }

        $this->addFlash('success', "Book Added");
        return $this->redirectToRoute('book');
    }

    /**
     * @Route("/author/{bookId}", name="author")
     * @param int $bookId
     * @param int $authorId
     * @param Qss $qss
     * @param Book $bookApi
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function ajaxDeleteBook(int $bookId, int $authorId, Qss $qss, Book $bookApi, ValidatorInterface $validator) : JsonResponse {
        try {
            $this->makeValidation($validator, new \App\Entity\Book(), "id", $bookId);
            $this->makeValidation($validator, new \App\Entity\Author(), "id", $authorId);
            $qss->setCallClass($bookApi)->bookDelete($bookId, $authorId);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 1, "message" => $e->getMessage()]);
        }

        return new JsonResponse(['error' => 0, "message" => "Book deleted"]);
    }
}
