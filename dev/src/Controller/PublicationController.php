<?php 

namespace App\Controller;

use App\Entity\Publication;
use App\Form\PublicationFormType;
use App\Repository\PublicationRepository;
use App\Repository\PublicationRepositoryInterface;
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

class PublicationController extends AbstractController
{
    public function __construct(
        #[Autowire(service: PublicationRepository::class)]
        private PublicationRepositoryInterface $publicationRepository,
    ) {
    }

    #[Route(path: 'page_publications', name: 'page_publications')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {

        $publicationAdd = $this->createForm(PublicationFormType::class); 

        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')){
            $publicationAdd = $this->addForm($request, $em); 
        } 

        $publications = $this->publicationRepository->findAll();

        return $this->render('publication/main.html.twig', [
            'publicationForm' => $publicationAdd, 
            'publications' => $publications
        ]);
    }

    #[Route(path: '/publication', name: 'list_publications')]
    #[IsGranted('ROLE_INTERN')]
    public function showAll(): Response
    {
        $publications = $this->publicationRepository->findAll();

        return $this->render('publication/list.html.twig', ['publications' => $publications]);
    }

    #[Route(path: 'strains/publications/ajout', name: 'add_publication')]
    #[IsGranted('ROLE_SEARCH')]
    public function addForm(Request $request, EntityManagerInterface $em): Form
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $publication = new Publication();

        //Create the form
        $publicationForm = $this->createForm(PublicationFormType::class, $publication);

        //
        $publicationForm->handleRequest($request);

        if ($publicationForm->isSubmitted() && $publicationForm->isValid()) {
            //generate the slug -- not done yet
            $slug = $publication->getTitle(). ' - ' .$publication->getAutor(). ' - ' .$publication->getYear();
            $publication->setSlug($slug);

            //get the user -- not done yet

            //stock data
            $em->persist($publication);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $publicationForm;
        }
        return $publicationForm;
    }

    #[Route(path: 'strains/publications/ajout/response', name: 'add_publication_response')]
    #[IsGranted('ROLE_SEARCH')]
    public function addResponse(Request $request, EntityManagerInterface $em): Response
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $publication = new Publication();

        //Create the form
        $publicationForm = $this->createForm(PublicationFormType::class, $publication);

        //
        $publicationForm->handleRequest($request);

        if ($publicationForm->isSubmitted() && $publicationForm->isValid()) {
            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($publication);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $this->redirectToRoute('page_strains');
        }
        return $this->render('publication/create.html.twig', compact('publicationForm'));
    }

    #[Route('strains/publication/edit/{id}', name: 'edit_publication')]
    #[IsGranted('ROLE_SEARCH')]
    public function edit(
        Publication $publication,
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        /*
        if ($vehicule) {
            $this->denyAccessUnlessGranted('vehicule.is_creator', $vehicule);
        }
        */

        //Create the form
        $publicationForm = $this->createForm(PublicationFormType::class, $publication);

        //treat the request
        $publicationForm->handleRequest($request);

        if ($publicationForm->isSubmitted() && $publicationForm->isValid()) {
            //generate the slug
            $slug = $publication->getTitle(). ' - ' .$publication->getAutor(). ' - ' .$publication->getYear();
            $publication->setSlug($slug);

            //stock data
            $em->persist($publication);
            $em->flush();

            $this->addFlash('success', 'publication ' . $publication->getTitle() . ' modified with succes !');

            return $this->redirectToRoute('page_publications');
        }
        return $this->render('publication/edit.html.twig', compact('publicationForm'));
    }

    #[Route('strains/publication/delete/{id}', name: 'delete_publication')]
    #[IsGranted('ROLE_SEARCH')]
    public function delete(Request $request, Publication $publication, EntityManagerInterface $em): Response
    {

        if ($request->query->get('confirm') === 'yes') {
            $em->remove($publication);
            $em->flush();

            $this->addFlash('success', 'Publication ' . $publication->getTitle() . ' deleted successfully!');
            return $this->redirectToRoute('page_publications');
        }

        $this->addFlash('warning', 'Are you sure you want to delete this publication ' . $publication->getTitle(). ' ? (Be aware. This action cannot be undone !)');

        return $this->redirectToRoute('page_publications');
    }


}