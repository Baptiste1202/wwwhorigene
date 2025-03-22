<?php 

namespace App\Controller;

use App\Entity\Storage;
use App\Form\StorageFormType;
use App\Repository\StorageRepository;
use App\Repository\StorageRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class StorageController extends AbstractController
{
    public function __construct(
        #[Autowire(service: StorageRepository::class)]
        private StorageRepositoryInterface $storageRepository,
    ) {
    }

    #[Route(path: '/storage', name: 'list_storages')]
    public function showAll(): Response
    {
        $storages = $this->storageRepository->findAll();

        return $this->render('storage/list.html.twig', ['storages' => $storages]);
    }

    #[Route(path: 'strains/storages/ajout', name: 'add_storage')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $storage = new Storage();

        //Create the form
        $storageForm = $this->createForm(StorageFormType::class, $storage);

        //
        $storageForm->handleRequest($request);

        if ($storageForm->isSubmitted() && $storageForm->isValid()) {
            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($storage);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $this->redirectToRoute('page_strain');
        }
        return $this->render('storage/create.html.twig', ['storageForm' => $storageForm->createView()]);
    }

}