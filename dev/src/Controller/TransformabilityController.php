<?php 

namespace App\Controller;

use App\Entity\Transformability;
use App\Form\TransformabilityFormType;
use App\Repository\TransformabilityRepository;
use App\Repository\TransformabilityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class TransformabilityController extends AbstractController
{
    public function __construct(
        #[Autowire(service: TransformabilityRepository::class)]
        private TransformabilityRepositoryInterface $transformabilityRepository,
    ) {
    }

    #[Route(path: 'strains/page_transformabilities', name: 'page_transformabilities')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')){
            $transformabilityAdd = $this->addForm($request, $em); 
        }

        $transformabilitys = $this->transformabilityRepository->findAll();

        return $this->render('transformability/main.html.twig', [
            'transformabilityForm' => $transformabilityAdd, 
            'transformabilitys' => $transformabilitys
        ]);
    }

    #[Route(path: '/transformability', name: 'list_transformabilitys')]
    public function showAll(): Response
    {
        $transformabilitys = $this->transformabilityRepository->findAll();

        return $this->render('transformability/list.html.twig', ['transformabilitys' => $transformabilitys]);
    }

    #[Route(path: 'strains/transformabilitys/ajout', name: 'add_transformability')]
    public function addForm(Request $request, EntityManagerInterface $em): Form
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $transformability = new Transformability();

        //Create the form
        $transformabilityForm = $this->createForm(TransformabilityFormType::class, $transformability);

        //
        $transformabilityForm->handleRequest($request);

        if ($transformabilityForm->isSubmitted() && $transformabilityForm->isValid()) {
            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($transformability);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $transformabilityForm;
        }
        return $transformabilityForm;
    }

    #[Route(path: 'strains/transformabilitys/ajout/response', name: 'add_transformability_response')]
    public function addResponse(Request $request, EntityManagerInterface $em): Response
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $transformability = new Transformability();

        //Create the form
        $transformabilityForm = $this->createForm(TransformabilityFormType::class, $transformability);

        //
        $transformabilityForm->handleRequest($request);

        if ($transformabilityForm->isSubmitted() && $transformabilityForm->isValid()) {
            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($transformability);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $this->redirectToRoute('page_strains');
        }
        return $this->render('transformability/create.html.twig', compact('transformabilityForm'));
    }

    #[Route('strains/transformability/edit/{id}', name: 'edit_transformability')]
    public function edit(
        Transformability $transformability,
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        /*
        if ($vehicule) {
            $this->denyAccessUnlessGranted('vehicule.is_creator', $vehicule);
        }
        */

        //Create the form
        $transformabilityForm = $this->createForm(TransformabilityFormType::class, $transformability);

        //treat the request
        $transformabilityForm->handleRequest($request);

        if ($transformabilityForm->isSubmitted() && $transformabilityForm->isValid()) {
            //generate the slug

            //stock data
            $em->persist($transformability);
            $em->flush();

            $this->addFlash('success', 'transformability ' . $transformability->getNom() . ' modified with succes !');

            return $this->redirectToRoute('page_transformabilities');
        }
        return $this->render('transformability/edit.html.twig', compact('transformabilityForm'));
    }

    #[Route('strains/transformability/delete/{id}', name: 'delete_transformability')]
    public function delete(Request $request, Transformability $transformability, EntityManagerInterface $em): Response
    {
        /*
        if ($vehicule) {
            $this->denyAccessUnlessGranted('vehicule.is_creator', $vehicule);
        }
        */

        if ($request->query->get('confirm') === 'yes') {
            $em->remove($transformability);
            $em->flush();

            $this->addFlash('success', 'Transformability ' . $transformability->getTechnique() . ' deleted successfully!');
            return $this->redirectToRoute('page_transformabilities');
        }

        $this->addFlash('warning', 'Are you sure you want to delete this transformability '.$transformability->getTechnique(). ' ? (Be aware. This action cannot be undone !)');

        return $this->redirectToRoute('page_transformabilities');
    }


}