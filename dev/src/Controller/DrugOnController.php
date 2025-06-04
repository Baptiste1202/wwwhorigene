<?php

namespace App\Controller; 

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\DrugResistanceOnStrain;
use App\Form\DrugOnFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DrugOnController extends AbstractController {
    #[Route(path: 'strains/drugs_on_strain/ajout', name: 'add_drug_on_strain')]
    public function addForm(Request $request, EntityManagerInterface $em): Response
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $drugon = new DrugResistanceOnStrain();

        //Create the form
        $drugonForm = $this->createForm(DrugOnFormType::class, $drugon);

        //
        $drugonForm->handleRequest($request);

        if ($drugonForm->isSubmitted() && $drugonForm->isValid()) {
            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($drugon);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $this->redirectToRoute('add_drug_on_strain');
        }

        return $this->render('drugon/add.html.twig', [
            'drugonForm' => $drugonForm->createView(),
        ]);
    }
}
