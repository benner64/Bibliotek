<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Series;
use App\Form\SeriesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SeriesController extends AbstractController
{
    #[Route('/Series/Update/{id}', name: 'SeriesUpdate')]
    public function UpdateSeries(EntityManagerInterface $entityManager, Series $series, Request $request):Response
    {
        $form = $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $seriesUpdated = $form->getData();

            $series->setName($seriesUpdated->getName());
            $series->setDescription($seriesUpdated->getDescription());

            $entityManager->flush();

            return $this->redirectToRoute("SeriesUpdate", ["id" => $series->getId()]); //It's the name of the route, not the web path
        }
        
        return $this->render('Series/Series.html.twig', [
            'form' => $form,
            "delete" => true,
            "series" => $series,
            "books" => $series->getBooks()
        ]);
    }

    #[Route('/Series/Create', name: 'SeriesCreate')]
    public function CreateSeries(EntityManagerInterface $entityManager, Request $request):Response
    {
        $series = new Series();
        $form = $this->createForm(SeriesType::class, $series);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $series = $form->getData();


            // ... perform some action, such as saving the task to the database
            $entityManager->persist($series);
            $entityManager->flush();

            return $this->redirectToRoute("SeriesUpdate", ["id" => $series->getId()]); //It's the name of the route, not the web path
        }

        return $this->render('Series/Series.html.twig', [
            'form' => $form,
            "delete" => false,
            "books" => []
        ]);
    }

    #[Route('/Series', name: 'SeriesRead')]
    public function GetAllSeriess(EntityManagerInterface $entityManager):Response
    {
        $repository = $entityManager->getRepository(Series::class);
        $series = $repository->GetAllSeriesOrdered();

        return $this->render('Series/Seriess.html.twig', [
            'seriess' => $series
        ]);
    }

    #[Route('/Series/Delete/{id}', name: 'SeriesDelete')]
    public function DeleteSeriess(EntityManagerInterface $entityManager, Series $series, Request $request):Response
    {
        $form = $this->createFormBuilder($series)
        ->add("button", ButtonType::class, ['label' => "Back", "attr" => ['onClick' => "history.back()"]])
        ->add('save', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'MyClass']])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // ... perform some action, such as saving the task to the database
            $entityManager->remove($series);
            $entityManager->flush();

            return $this->redirectToRoute("SeriesRead"); //It's the name of the route, not the web path
        }

        return $this->render('Series/Delete.html.twig', [
            'series' => $series,
            "form" => $form
        ]);
    }
}