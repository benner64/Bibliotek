<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorController extends AbstractController
{
    #[Route('/Author/Update/{id}', name: 'AuthorUpdate')]
    public function UpdateAuthor(EntityManagerInterface $entityManager, Author $author, Request $request):Response
    {
        $form = $this->createFormBuilder($author)
        ->add('name', TextType::class)
        ->add('last_name', TextType::class)
        ->add('save', SubmitType::class, ['label' => 'Update Author', 'attr' => ['class' => 'btn-success']])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $authorUpdated = $form->getData();

            $author->setName($authorUpdated->getName());
            $author->setLastName($authorUpdated->getLastName());

            $entityManager->flush();

            return $this->redirectToRoute("AuthorUpdate", ["id" => $author->getId()]); //It's the name of the route, not the web path
        }
        
        return $this->render('Author/Author.html.twig', [
            'form' => $form,
            "delete" => true,
            "author" => $author
        ]);
    }

    #[Route('/Author/Create', name: 'AuthorCreate')]
    public function CreateAuthor(EntityManagerInterface $entityManager, Request $request):Response
    {
        $author = new Author();
        $form = $this->createFormBuilder($author)
        ->add('name', TextType::class)
        ->add('last_name', TextType::class)
        ->add('save', SubmitType::class, ['label' => 'Create Author', 'attr' => ['class' => 'btn-success']])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $author = $form->getData();


            // ... perform some action, such as saving the task to the database
            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute("AuthorUpdate", ["id" => $author->getId()]); //It's the name of the route, not the web path
        }

        return $this->render('Author/Author.html.twig', [
            'form' => $form,
            "delete" => false
        ]);
    }

    #[Route('/Author', name: 'AuthorRead')]
    public function GetAllAuthors(EntityManagerInterface $entityManager):Response
    {
        $repository = $entityManager->getRepository(Author::class);
        $authors = $repository->GetAllAuthorsOrdered();

        return $this->render('Author/Authors.html.twig', [
            'authors' => $authors
        ]);
    }

    #[Route('/Author/Delete/{id}', name: 'AuthorDelete')]
    public function DeleteAuthors(EntityManagerInterface $entityManager, Author $author, Request $request):Response
    {
        $form = $this->createFormBuilder($author)
        ->add("button", ButtonType::class, ['label' => "Back", "attr" => ['onClick' => "history.back()", 'class' => 'btn-warning']])
        ->add('save', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // ... perform some action, such as saving the task to the database
            $entityManager->remove($author);
            $entityManager->flush();

            return $this->redirectToRoute("AuthorRead"); //It's the name of the route, not the web path
        }

        return $this->render('Delete.html.twig', [
            'author' => $author,
            "form" => $form,
            'type' => 'author'
        ]);
    }
}