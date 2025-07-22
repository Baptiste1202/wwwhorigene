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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Environment;

class StorageController extends AbstractController
{
    public function __construct(
        #[Autowire(service: StorageRepository::class)]
        private StorageRepositoryInterface $storageRepository,
    ) {
    }

    #[Route(path: 'strains/storages/ajout', name: 'add_storage')]
    #[IsGranted('ROLE_SEARCH')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $storage = new Storage();

        $storageForm = $this->createForm(StorageFormType::class, $storage);

        $storageForm->handleRequest($request);

        if ($storageForm->isSubmitted() && $storageForm->isValid()) {

            $em->persist($storage);
            $em->flush();

            return $this->redirectToRoute('page_strain');
        }
        return $this->render('storage/create.html.twig', ['storageForm' => $storageForm->createView()]);
    }

}