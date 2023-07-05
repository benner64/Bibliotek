<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Publisher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PublisherController extends AbstractController
{
    #[Route('/Publisher/Update/{id}', name: 'PublisherUpdate')]
    public function UpdatePublisher(EntityManagerInterface $entityManager, Publisher $publisher, Request $request):Response
    {
        $form = $this->createFormBuilder($publisher)
        ->add('Name', TextType::class)
        ->add('Link', TextType::class, ["required" => false])
        ->add('save', SubmitType::class, ['label' => 'Update Publisher', 'attr' => ['class' => 'btn-success']])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $publisherUpdated = $form->getData();

            $publisher->setName($publisherUpdated->getName());
            $publisher->setLink($publisherUpdated->getLink());

            $entityManager->flush();

            return $this->redirectToRoute("PublisherUpdate", ["id" => $publisher->getId()]); //It's the name of the route, not the web path
        }
        
        return $this->render('Publisher/Publisher.html.twig', [
            'form' => $form,
            "delete" => true,
            "publisher" => $publisher
        ]);
    }

    #[Route('/Publisher/Create', name: 'PublisherCreate')]
    public function CreatePublisher(EntityManagerInterface $entityManager, Request $request):Response
    {
        $publisher = new Publisher();
        $form = $this->createFormBuilder($publisher)
        ->add('Name', TextType::class)
        ->add('Link', TextType::class, ["required" => false])
        ->add('save', SubmitType::class, ['label' => 'Update Publisher', 'attr' => ['class' => 'btn-success']])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $publisher = $form->getData();


            // ... perform some action, such as saving the task to the database
            $entityManager->persist($publisher);
            $entityManager->flush();

            return $this->redirectToRoute("PublisherUpdate", ["id" => $publisher->getId()]); //It's the name of the route, not the web path
        }

        return $this->render('Publisher/Publisher.html.twig', [
            'form' => $form,
            "delete" => false
        ]);
    }

    #[Route('/Publisher', name: 'PublisherRead')]
    public function GetAllPublishers(EntityManagerInterface $entityManager):Response
    {
        $repository = $entityManager->getRepository(Publisher::class);
        $publishers = $repository->GetAllPublishersOrdered();

        return $this->render('Publisher/Publishers.html.twig', [
            'publishers' => $publishers
        ]);
    }

    #[Route('/Publisher/Delete/{id}', name: 'PublisherDelete')]
    public function DeletePublishers(EntityManagerInterface $entityManager, Publisher $publisher, Request $request):Response
    {
        $form = $this->createFormBuilder($publisher)
        ->add("button", ButtonType::class, ['label' => "Back", "attr" => ['onClick' => "history.back()", 'class' => 'btn-warning']])
        ->add('save', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // ... perform some action, such as saving the task to the database
            $entityManager->remove($publisher);
            $entityManager->flush();

            return $this->redirectToRoute("PublisherRead"); //It's the name of the route, not the web path
        }

        return $this->render('Delete.html.twig', [
            'publisher' => $publisher,
            "form" => $form,
            'type' => 'publisher'
        ]);
    }
}