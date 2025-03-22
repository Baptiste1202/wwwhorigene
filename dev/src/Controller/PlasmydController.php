<?php 

namespace App\Controller;

use App\Entity\Plasmyd;
use App\Form\PlasmydFormType;
use App\Repository\PlasmydRepository;
use App\Repository\PlasmydRepositoryInterface;
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
use Symfony\Component\String\Slugger\SluggerInterface;

class PlasmydController extends AbstractController
{
    public function __construct(
        #[Autowire(service: PlasmydRepository::class)]
        private PlasmydRepositoryInterface $plasmydRepository,
    ) {
    }

    #[Route(path: 'page_plasmyds', name: 'page_plasmyds')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {

        $plasmydAdd = $this->createForm(PlasmydFormType::class); 

        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')){
            $plasmydAdd = $this->addForm($request, $em);   
        } 

        $plasmyds = $this->plasmydRepository->findAll();

        return $this->render('plasmyd/main.html.twig', [
            'plasmydForm' => $plasmydAdd, 
            'plasmyds' => $plasmyds
        ]);
    }

    #[Route(path: '/plasmyd', name: 'list_plasmyds')]
    #[IsGranted('ROLE_INTERN')]
    public function showAll(): Response
    {
        $plasmyds = $this->plasmydRepository->findAll();

        return $this->render('plasmyd/list.html.twig', ['plasmyds' => $plasmyds]);
    }

    #[Route(path: 'strains/plasmyds/ajout', name: 'add_plasmyd')]
    #[IsGranted('ROLE_SEARCH')]
    public function addForm(Request $request, EntityManagerInterface $em): Form
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $plasmyd = new Plasmyd();

        //Create the form
        $plasmydForm = $this->createForm(PlasmydFormType::class, $plasmyd);

        //
        $plasmydForm->handleRequest($request);

        if ($plasmydForm->isSubmitted() && $plasmydForm->isValid()) {
            //generate the slug -- not done yet
            $slug = $plasmyd->getNamePlasmyd(). ' - ' . $plasmyd->getType();
            $plasmyd->setSlug($slug);

            //get the user -- not done yet

            //stock data
            $em->persist($plasmyd);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $plasmydForm;
        }
        return $plasmydForm;
    }

    #[Route(path: 'strains/plasmyds/ajout/response', name: 'add_plasmyd_reponse')]
    #[IsGranted('ROLE_SEARCH')]
    public function addResponse(Request $request, EntityManagerInterface $em): Response
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $plasmyd = new Plasmyd();

        //Create the form
        $plasmydForm = $this->createForm(PlasmydFormType::class, $plasmyd);

        //
        $plasmydForm->handleRequest($request);

        if ($plasmydForm->isSubmitted() && $plasmydForm->isValid()) {
            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($plasmyd);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $this->redirectToRoute('page_strains');
        }
        return $this->render('plasmyd/create.html.twig', compact('plasmydForm'));
    }
    
    #[Route('strains/plasmyd/edit/{id}', name: 'edit_plasmyd')]
    #[IsGranted('ROLE_SEARCH')]
    public function edit(
        Plasmyd $plasmyd,
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        /*
        if ($vehicule) {
            $this->denyAccessUnlessGranted('vehicule.is_creator', $vehicule);
        }
        */

        //Create the form
        $plasmydForm = $this->createForm(PlasmydFormType::class, $plasmyd);

        //treat the request
        $plasmydForm->handleRequest($request);

        if ($plasmydForm->isSubmitted() && $plasmydForm->isValid()) {
            //generate the slug
            $slug = $plasmyd->getNamePlasmyd(). ' - ' . $plasmyd->getType();
            $plasmyd->setSlug($slug);

            //stock data
            $em->persist($plasmyd);
            $em->flush();

            $this->addFlash('success', 'plasmyd ' . $plasmyd->getNamePlasmyd() . ' modified with succes !');

            return $this->redirectToRoute('page_plasmyds');
        }
        return $this->render('plasmyd/edit.html.twig', compact('plasmydForm'));
    }

    #[Route('strains/plasmyd/delete/{id}', name: 'delete_plasmyd')]
    #[IsGranted('ROLE_SEARCH')]
    public function delete(Request $request, Plasmyd $plasmyd, EntityManagerInterface $em): Response
    {
        if ($request->query->get('confirm') === 'yes') {
            $em->remove($plasmyd);
            $em->flush();

            $this->addFlash('success', 'Plasmyd ' . $plasmyd->getNamePlasmyd() . ' deleted successfully!');
            return $this->redirectToRoute('page_plasmyds');
        }

        $this->addFlash('warning', 'Are you sure you want to delete this plasmyd ' . $plasmyd->getNamePlasmyd(). ' ? (Be aware. This action cannot be undone !)');

        return $this->redirectToRoute('page_plasmyds');
    }


}