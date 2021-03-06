<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Api\Author;
use App\Api\Book;
use App\Service\Env;
use App\Service\Isbn;
use App\Service\Qss;
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
     * @Route("/book-add", name="book_add")
     * @param Qss $qss
     * @param Author $author
     * @param Isbn $isbn
     * @return Response
     */
    public function addBook(Qss $qss, Author $author, Isbn $isbn) : Response {
        $isbnToGenerate = (int)Env::load()->get("ISBN_GENERATE_NUM", "10");
        $isbnArr = $isbn->generate($isbnToGenerate);
        $authors = array();
        try {
            $authors = $qss->setCallClass($author)->authors();
        } catch(Exception $e) {
            $this->addFlash('warning', $e->getMessage());
        }

        return $this->render('book/add.html.twig', [
            'authors' => $authors,
            'isbns' => $isbnArr
        ]);
    }

    /**
     * @Route("/book-do-add", name="book_do_add")
     * @param Request $request
     * @param Qss $qss
     * @param Book $bookApi
     * @param ValidatorInterface $validator
     * @return RedirectResponse
     */
    public function doAddBook(Request $request, Qss $qss, Book $bookApi, ValidatorInterface $validator) : RedirectResponse {
        $title = $request->get("title");
        $releaseDate = $request->get("release_date");
        $format = $request->get("format");
        $numPages = $request->get("num_of_pages");
        $description = $request->get("description");

        $bookEntity = new \App\Entity\Book();

        try {
            if(!$request->request->has("author_id")) {
                throw new Exception("Author is required");
            }
            $authorId = (int)$request->get("author_id");
            if(!$request->request->has("isbn")) {
                throw new Exception("ISBN is required");
            }
            $isbn = $request->get("isbn");
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
            $bookEntity->setNumOfPages((int)$numPages);
            $bookEntity->setDescription($description);

            $qss->setCallClass($bookApi)->bookAdd($authorId, $bookEntity);
        } catch(Exception $e) {
            $this->addFlash('warning', $e->getMessage());
            return $this->redirectToRoute('book_add');
        }

        $this->addFlash('success', "Book Added");
        return $this->redirectToRoute('book_add');
    }

    /**
     * @Route("/book-delete/author/{authorId}/book/{bookId}", name="book_delete")
     * @param int $bookId
     * @param int $authorId
     * @param Qss $qss
     * @param Book $bookApi
     * @return JsonResponse
     */
    public function ajaxDeleteBook(int $bookId, int $authorId, Qss $qss, Book $bookApi) : JsonResponse {
        try {
            $qss->setCallClass($bookApi)->bookDelete($bookId, $authorId);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 1, "message" => $e->getMessage()]);
        }

        return new JsonResponse(['error' => 0, "message" => "Book deleted", "book_id" => $bookId]);
    }
}
