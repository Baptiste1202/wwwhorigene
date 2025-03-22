<?php 

namespace App\Controller;

use App\Entity\DrugResistance;
use App\Form\DrugFormType;
use App\Repository\DrugRepository;
use App\Repository\DrugRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DrugController extends AbstractController
{
    public function __construct(
        #[Autowire(service: DrugRepository::class)]
        private DrugRepositoryInterface $drugRepository,
    ) {
    }

    #[Route(path: 'page_drugs', name: 'page_drugs')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $role = $security->getUser()->getRoles(); 

        $drugAdd = $this->createForm(DrugFormType::class); 

        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')){
            $drugAdd = $this->addForm($request, $em);  
        } 

        $drugs = $this->drugRepository->findAll();

        return $this->render('drug/main.html.twig', [
            'drugForm' => $drugAdd, 
            'drugs' => $drugs
        ]);
    }

    #[Route(path: '/drug', name: 'list_drugs')]
    #[IsGranted('ROLE_INTERN')]
    public function showAll(): Response
    {
        $drugs = $this->drugRepository->findAll();

        return $this->render('drug/list.html.twig', ['drugs' => $drugs]);
    }

    #[Route(path: 'strains/drugs/ajout', name: 'add_drug')]
    #[IsGranted('ROLE_SEARCH')]
    public function addForm(Request $request, EntityManagerInterface $em): Form
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $drug = new DrugResistance();

        //Create the form
        $drugForm = $this->createForm(DrugFormType::class, $drug);

        //
        $drugForm->handleRequest($request);

        if ($drugForm->isSubmitted() && $drugForm->isValid()) {
            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($drug);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $drugForm;
        }
        return $drugForm;
    }

    #[Route(path: 'strains/drugs/ajout/response', name: 'add_drug')]
    #[IsGranted('ROLE_SEARCH')]
    public function addResponse(Request $request, EntityManagerInterface $em): Response
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $drug = new DrugResistance();

        //Create the form
        $drugForm = $this->createForm(DrugFormType::class, $drug);

        //
        $drugForm->handleRequest($request);

        if ($drugForm->isSubmitted() && $drugForm->isValid()) {
            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($drug);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $this->redirectToRoute('page_strains');
        }
        return $this->render('drug/create.html.twig', compact('drugForm'));
    }

    #[Route('strains/drug/edit/{id}', name: 'edit_drug')]
    #[IsGranted('ROLE_SEARCH')]
    public function edit(
        DrugResistance $drug,
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        /*
        if ($vehicule) {
            $this->denyAccessUnlessGranted('vehicule.is_creator', $vehicule);
        }
        */

        //Create the form
        $drugForm = $this->createForm(DrugFormType::class, $drug);

        //treat the request
        $drugForm->handleRequest($request);

        if ($drugForm->isSubmitted() && $drugForm->isValid()) {
            //generate the slug

            //stock data
            $em->persist($drug);
            $em->flush();

            $this->addFlash('success', 'drug ' . $drug->getName() . ' modified with succes !');

            return $this->redirectToRoute('page_drugs');
        }
        return $this->render('drug/edit.html.twig', compact('drugForm'));
    }

    #[Route('strains/drug/delete/{id}', name: 'delete_drug')]
    #[IsGranted('ROLE_SEARCH')]
    public function delete(Request $request, DrugResistance $drug, EntityManagerInterface $em): Response
    {
        if ($request->query->get('confirm') === 'yes') {
            $em->remove($drug);
            $em->flush();

            $this->addFlash('success', 'Drug Resistance ' . $drug->getName() . ' deleted successfully!');
            return $this->redirectToRoute('page_drugs');
        }

        $this->addFlash('warning', 'Are you sure you want to delete this Drug Resistance ' . $drug->getName(). ' ? (Be aware. This action cannot be undone !)');

        return $this->redirectToRoute('page_drugs');
    }

}