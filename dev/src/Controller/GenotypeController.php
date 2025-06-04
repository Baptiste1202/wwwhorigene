<?php 

namespace App\Controller;

use App\Entity\Genotype;
use App\Form\GenotypeFormType;
use App\Repository\GenotypeRepository;
use App\Repository\GenotypeRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class GenotypeController extends AbstractController
{
    public function __construct(
        #[Autowire(service: GenotypeRepository::class)]
        private GenotypeRepositoryInterface $genotypeRepository,
    ) {
    }

    #[Route(path: '/genotype', name: 'list_genotypes')]
    public function showAll(): Response
    {
        $genotypes = $this->genotypeRepository->findAll();

        return $this->render('genotype/list.html.twig', ['genotypes' => $genotypes]);
    }

    #[Route(path: 'genotypes/ajout', name: 'add_genotype')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $genotype = new Genotype();

        //Create the form
        $genotypeForm = $this->createForm(GenotypeFormType::class, $genotype);

        //
        $genotypeForm->handleRequest($request);

        if ($genotypeForm->isSubmitted() && $genotypeForm->isValid()) {
            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($genotype);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $this->redirectToRoute('list_genotypes');
        }
        return $this->render('genotype/create.html.twig', ['genotypeForm' => $genotypeForm->createView()]);
    }

}