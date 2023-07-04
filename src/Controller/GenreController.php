<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GenreController extends AbstractController
{
    #[Route('/Genre/Update/{id}', name: 'GenreUpdate')]
    public function UpdateGenre(EntityManagerInterface $entityManager, Genre $genre, Request $request):Response
    {
        $form = $this->createFormBuilder($genre)
        ->add('name', TextType::class)
        ->add('colour', ColorType::class)
        ->add('save', SubmitType::class, ['label' => 'Update Author', 'attr' => ['class' => 'MyClass']])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $genreUpdated = $form->getData();

            $genre->setName($genreUpdated->getName());
            $genre->setColour($genreUpdated->getColour());

            $entityManager->flush();

            return $this->redirectToRoute("GenreUpdate", ["id" => $genre->getId()]); //It's the name of the route, not the web path
        }
        
        return $this->render('Genre/Genre.html.twig', [
            'form' => $form,
            "delete" => true,
            "genre" => $genre
        ]);
    }

    #[Route('/Genre/Create', name: 'GenreCreate')]
    public function CreateGenre(EntityManagerInterface $entityManager, Request $request):Response
    {
        $genre = new Genre();
        $form = $this->createFormBuilder($genre)
        ->add('name', TextType::class)
        ->add('colour', ColorType::class)
        ->add('save', SubmitType::class, ['label' => 'Create Author', 'attr' => ['class' => 'MyClass']])
        ->getForm();

        //$form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $genre = $form->getData();


            // ... perform some action, such as saving the task to the database
            $entityManager->persist($genre);
            $entityManager->flush();

            return $this->redirectToRoute("GenreUpdate", ["id" => $genre->getId()]); //It's the name of the route, not the web path
        }

        return $this->render('Genre/Genre.html.twig', [
            'form' => $form,
            "delete" => false
        ]);
    }

    #[Route('/Genre', name: 'GenreRead')]
    public function GetAllGenres(EntityManagerInterface $entityManager):Response
    {
        $repository = $entityManager->getRepository(Genre::class);
        $genres = $repository->GetAllGenresOrdered();

        return $this->render('Genre/Genres.html.twig', [
            'genres' => $genres
        ]);
    }

    #[Route('/Genre/Delete/{id}', name: 'GenreDelete')]
    public function DeleteGenres(EntityManagerInterface $entityManager, Genre $genre, Request $request):Response
    {
        $form = $this->createFormBuilder($genre)
        ->add("button", ButtonType::class, ['label' => "Back", "attr" => ['onClick' => "history.back()"]])
        ->add('save', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'MyClass']])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // ... perform some action, such as saving the task to the database
            $entityManager->remove($genre);
            $entityManager->flush();

            return $this->redirectToRoute("GenreRead"); //It's the name of the route, not the web path
        }

        return $this->render('Genre/Delete.html.twig', [
            'genre' => $genre,
            "form" => $form
        ]);
    }
}