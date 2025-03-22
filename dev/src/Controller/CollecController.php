<?php 

namespace App\Controller;

use App\Entity\Collec;
use App\Form\CollecFormType;
use App\Repository\CollecRepository;
use App\Repository\CollecRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CollecController extends AbstractController
{
    public function __construct(
        #[Autowire(service: CollecRepository::class)]
        private CollecRepositoryInterface $collecRepository,
    ) {
    }

    #[Route(path: 'page_collecs', name: 'page_collecs')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $collecAdd = $this->createForm(CollecFormType::class); 

        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')){
            $collecAdd = $this->addForm($request, $em);  
        }

        $collecs = $this->collecRepository->findAll();

        return $this->render('collec/main.html.twig', [
            'collecForm' => $collecAdd, 
            'collecs' => $collecs
        ]);

    }

    #[Route(path: '/collec', name: 'list_collecs')]
    #[IsGranted('ROLE_INTERN')]
    public function showAll(): Response
    {
        $collecs = $this->collecRepository->findAll();

        return $this->render('collec/list.html.twig', ['collecs' => $collecs]);
    }

    #[Route(path: 'strains/collections/ajout', name: 'add_collec')]
    #[IsGranted('ROLE_SEARCH')]
    public function addForm(Request $request, EntityManagerInterface $em): Form
    {

        //Create a new vehicule
        $collec = new Collec();

        //Create the form
        $collecForm = $this->createForm(CollecFormType::class, $collec);

        //
        $collecForm->handleRequest($request);

        if ($collecForm->isSubmitted() && $collecForm->isValid()) {
            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($collec);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $collecForm;
        }
        return $collecForm;
    }

    #[Route(path: 'strains/collections/ajout/response', name: 'add_collec')]
    #[IsGranted('ROLE_SEARCH')]
    public function addResponse(Request $request, EntityManagerInterface $em): Response
    {

        //Create a new vehicule
        $collec = new Collec();

        //Create the form
        $collecForm = $this->createForm(CollecFormType::class, $collec);

        //
        $collecForm->handleRequest($request);

        if ($collecForm->isSubmitted() && $collecForm->isValid()) {
            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($collec);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $this->redirectToRoute('page_strains');
        }
        return $this->render('collec/create.html.twig', compact('collecForm'));
    }

    #[Route('strains/collec/edit/{id}', name: 'edit_collec')]
    #[IsGranted('ROLE_SEARCH')]
    public function edit(
        Collec $collec,
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        /*
        if ($vehicule) {
            $this->denyAccessUnlessGranted('vehicule.is_creator', $vehicule);
        }
        */

        //Create the form
        $collecForm = $this->createForm(CollecFormType::class, $collec);

        //treat the request
        $collecForm->handleRequest($request);

        if ($collecForm->isSubmitted() && $collecForm->isValid()) {
            //generate the slug

            //stock data
            $em->persist($collec);
            $em->flush();

            $this->addFlash('success', 'collec ' . $collec->getName() . ' modified with succes !');

            return $this->redirectToRoute('page_collecs');
        }
        return $this->render('collec/edit.html.twig', compact('collecForm'));
    }

    #[Route('strains/collec/delete/{id}', name: 'delete_collec')]
    #[IsGranted('ROLE_SEARCH')]
    public function delete(Request $request, Collec $collec, EntityManagerInterface $em): Response
    {
        if ($request->query->get('confirm') === 'yes') {
            $em->remove($collec);
            $em->flush();

            $this->addFlash('success', 'Collection ' . $collec->getName() . ' deleted successfully!');
            return $this->redirectToRoute('page_collecs');
        }

        $this->addFlash('warning', 'Are you sure you want to delete this Collection ' . $collec->getName(). ' ? (Be aware. This action cannot be undone !)');

        return $this->redirectToRoute('page_collecs');
    }

}