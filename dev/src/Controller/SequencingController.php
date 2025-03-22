<?php 

namespace App\Controller;

use App\Entity\MethodSequencing;
use App\Form\SequencingFormType;
use App\Repository\MethodSequencingRepository;
use App\Repository\MethodSequencingRepositoryInterface;
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

class SequencingController extends AbstractController
{
    public function __construct(
        #[Autowire(service: MethodSequencingRepository::class)]
        private MethodSequencingRepositoryInterface $sequencingRepository,
    ) {
    }

    #[Route(path: 'page_sequencings', name: 'page_sequencings')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $role = $security->getUser()->getRoles(); 

        $sequencingAdd = $this->createForm(SequencingFormType::class); 

        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')){
            $sequencingAdd = $this->addForm($request, $em); 
        } 

        $sequencings = $this->sequencingRepository->findAll();

        return $this->render('sequencing/main.html.twig', [
            'sequencingForm' => $sequencingAdd, 
            'sequencings' => $sequencings
        ]);
    }

    #[Route(path: '/sequencing', name: 'list_sequencings')]
    #[IsGranted('ROLE_INTERN')]
    public function showAll(): Response
    {
        $sequencings = $this->sequencingRepository->findAll();

        return $this->render('sequencing/list.html.twig', ['sequencings' => $sequencings]);
    }

    #[Route(path: 'strains/sequencings/ajout', name: 'add_sequencing')]
    #[IsGranted('ROLE_SEARCH')]
    public function addForm(Request $request, EntityManagerInterface $em): Form
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $sequencing = new MethodSequencing();

        //Create the form
        $sequencingForm = $this->createForm(SequencingFormType::class, $sequencing);

        //
        $sequencingForm->handleRequest($request);

        if ($sequencingForm->isSubmitted() && $sequencingForm->isValid()) {
            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($sequencing);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $sequencingForm;
        }
        return $sequencingForm;
    }

    #[Route(path: 'strains/sequencings/ajout/response', name: 'add_sequencing_response')]
    #[IsGranted('ROLE_SEARCH')]
    public function addResponse(Request $request, EntityManagerInterface $em): Response
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $sequencing = new MethodSequencing();

        //Create the form
        $sequencingForm = $this->createForm(SequencingFormType::class, $sequencing);

        //
        $sequencingForm->handleRequest($request);

        if ($sequencingForm->isSubmitted() && $sequencingForm->isValid()) {
            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($sequencing);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $this->redirectToRoute('page_strains');
        }
        return $this->render('sequencing/create.html.twig', compact('sequencingForm'));
    }

    #[Route('strains/sequencing/edit/{id}', name: 'edit_sequencing')]
    #[IsGranted('ROLE_SEARCH')]
    public function edit(
        MethodSequencing $sequencing,
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        /*
        if ($vehicule) {
            $this->denyAccessUnlessGranted('vehicule.is_creator', $vehicule);
        }
        */

        //Create the form
        $sequencingForm = $this->createForm(SequencingFormType::class, $sequencing);

        //treat the request
        $sequencingForm->handleRequest($request);

        if ($sequencingForm->isSubmitted() && $sequencingForm->isValid()) {
            //generate the slug

            //stock data
            $em->persist($sequencing);
            $em->flush();

            $this->addFlash('success', 'sequencing ' . $sequencing->getName() . ' modified with succes !');

            return $this->redirectToRoute('page_sequencings');
        }
        return $this->render('sequencing/edit.html.twig', compact('sequencingForm'));
    }

    #[Route('strains/sequencing/delete/{id}', name: 'delete_sequencing')]
    #[IsGranted('ROLE_SEARCH')]
    public function delete(Request $request, MethodSequencing $sequencing, EntityManagerInterface $em): Response
    {

        if ($request->query->get('confirm') === 'yes') {
            $em->remove($sequencing);
            $em->flush();

            $this->addFlash('success', 'Sequencing ' . $sequencing->getName() . ' deleted successfully!');
            return $this->redirectToRoute('page_sequencings');
        }

        $this->addFlash('warning', 'Are you sure you want to delete this sequencing '. $sequencing->getName(). ' ? (Be aware. This action cannot be undone !)');

        return $this->redirectToRoute('page_sequencings');
    }

}