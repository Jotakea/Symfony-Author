<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/showAuthor/{name}', name: 'app_show_author')]
    public function showAuthor($name): Response
    {
        return $this->render('author/show.html.twig', [
            'myname' => $name
        ]);
    }

    #[Route('/listAuthor', name: 'app_list_author')]
    public function list(): Response
    {

        $authors = array(
            array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william-shakespeare.jpg', 'username' => ' William Shakespeare', 'email' => ' william.shakespeare@gmail.com', 'nb_books' => 200),
            array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
        );

        return $this->render('author/list.html.twig', ['mylist' => $authors]);
    }

    #[Route('/detailsAuthor/{id}', name: 'app_author_details')]
    public function details($id): Response
    {
        return $this->render('author/showAuthor.html.twig', ['ida' => $id]);
    }

    #[Route('/author/show', name: 'app_author_show')]
    public function show(AuthorRepository $rep): Response
    {
        $authors = $rep->findAll();
        return $this->render('author/authorlist.html.twig', ['authors' => $authors]);
    }

    #[Route('/author/add', name: 'app_add_author')]
    public function addAuthor(ManagerRegistry $doctrine, Request $req): Response
    {
        $a = new Author();
        $form = $this->createForm(AuthorType::class, $a);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->persist($a);
            $em->flush();
            return new Response("OK");
        }
        return $this->renderForm("author/add.html.twig", ['myForm' => $form]);
    }

    #[Route('/author/edit/{id}', name: 'app_author_edit')]
    public function EditAuthor(ManagerRegistry $doctrine, Request $request, AuthorRepository $rep, $id): Response
    {
        $author = $rep->find($id);
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('author_show');
        }
        return $this->render('author/add.html.twig', [
            'myForm' => $form->createView(),
        ]);
    }

    #[Route('/author/delete/{id}', name: 'app_author_delete')]
    public function deleteAuthor($id, AuthorRepository $rep, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $author = $rep->find($id);
        $em->remove($author);
        $em->flush();
        return $this->redirectToRoute('author_show');
    }
}
