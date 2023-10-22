<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/book/add', name: 'app_add_book')]
    public function AddBook(ManagerRegistry $doctrine, Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->persist($book);
            $em->flush();
            return new Response("ok");
        }
        return $this->renderForm('book/form.html.twig', [
            'formA' => $form
        ]);
    }

    #[Route('/book/show', name: 'app_show_book')]
    public function getAll(ManagerRegistry $doctrine): Response
    {
        $rep = $doctrine->getRepository(Book::class);
        $books = $rep->findAll();
        return $this->render('book/list.html.twig', ['books' => $books]);
    }

    #[Route('/book/edit/{id}', name: 'app_book_edit')]
    public function EditBook(ManagerRegistry $doctrine, Request $request, BookRepository $rep, $id): Response
    {
        $book = $rep->find($id);
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('app_show_book');
        }
        return $this->render('book/form.html.twig', [
            'formA' => $form->createView(),
        ]);
    }

    #[Route('/book/detail/{id}', name: 'app_book_detail')]
    public function showBook(BookRepository $rep, $id): Response
    {
        $book = $rep->find($id);
        return $this->render('book/detail.html.twig', [
            'book' => $book
        ]);
    }

    #[Route('/book/delete/{id}', name: 'app_book_delete')]
    public function deleteBook($id, BookRepository $rep, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $book = $rep->find($id);
        $em->remove($book);
        $em->flush();
        return $this->redirectToRoute('app_show_book');
    }
}
