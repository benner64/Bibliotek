<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\BookType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

class BookController extends AbstractController
{

    private Serializer $serializer;

    function __construct() {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer($classMetadataFactory)];

        $this->serializer = new Serializer($normalizers, $encoders);
    }


    #[Route('/Book/Update/{id}', name: 'BookUpdate')]
    public function UpdateBook(SluggerInterface $slugger, EntityManagerInterface $entityManager, Book $book, Request $request):Response
    {
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $bookUpdated = $form->getData();  

            $cover = $form->get("coverImageFile")->getData();

            if ($cover) {
                $originalFilename = pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cover->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $cover->move(
                        $this->getParameter('covers_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'covername' property to store the PDF file name
                // instead of its contents
                $book->setCoverImageFile($newFilename);
            }

            $book->setName($bookUpdated->getName());
            $book->setPages($bookUpdated->getPages());
            $book->setPublisher($bookUpdated->getPublisher());
            $book->setSeries($bookUpdated->getSeries());    

            $entityManager->flush();

            return $this->redirectToRoute("BookUpdate", ["id" => $book->getId()]); //It's the name of the route, not the web path
        }
        
        return $this->render('Book/Book.html.twig', [
            'form' => $form,
            "delete" => true,
            "book" => $book
        ]);
    }

    #[Route('/Book/Create', name: 'BookCreate')]
    public function CreateBook(EntityManagerInterface $entityManager, Request $request):Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book, ['CreateOrUpdate' => true]);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $book = $form->getData();

            $cover = $form->get("coverImageFile")->getData();

            if ($cover) {
                $originalFilename = pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cover->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $cover->move(
                        $this->getParameter('covers_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'covername' property to store the PDF file name
                // instead of its contents
                $book->setCoverImageFile($newFilename);
            }

            // ... perform some action, such as saving the task to the database
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute("BookUpdate", ["id" => $book->getId()]); //It's the name of the route, not the web path
        }

        return $this->render('Book/Book.html.twig', [
            'form' => $form,
            "delete" => false
        ]);
    }

    #[Route('/Book', name: 'BookRead')]
    public function GetAllBooks(EntityManagerInterface $entityManager):Response
    {
        $repository = $entityManager->getRepository(Book::class);
        $books = $repository->GetAllBooksOrdered();

        return $this->render('Book/Books.html.twig', [
            'books' => $books
        ]);
    }

    #[Route('/Book/Search/{searchText}', name: 'BookSearch')]
    public function SearchBooksInLibrary(EntityManagerInterface $entityManager, String $searchText):Response
    {
        $repository = $entityManager->getRepository(Book::class);
        $books = $repository->GetSearchedBooksInLibrary($searchText);

        return new Response($this->serializer->serialize($books, 'json', ['groups' => 'search']));
    }

    #[Route('/Book/Delete/{id}', name: 'BookDelete')]
    public function DeleteBooks(EntityManagerInterface $entityManager, Book $book, Request $request):Response
    {
        $form = $this->createFormBuilder($book)
        ->add("button", ButtonType::class, ['label' => "Back", "attr" => ['onClick' => "history.back()", 'class' => 'btn-warning']])
        ->add('save', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // ... perform some action, such as saving the task to the database
            $entityManager->remove($book);
            $entityManager->flush();

            return $this->redirectToRoute("BookRead"); //It's the name of the route, not the web path
        }

        return $this->render('Delete.html.twig', [
            'book' => $book,
            "form" => $form,
            'type' => 'book'
        ]);
    }
}