<?php 

namespace App\Controller;

use App\Entity\Plasmyd;
use App\Form\PlasmydFormType;
use App\Repository\PlasmydRepository;
use App\Repository\PlasmydRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Knp\Component\Pager\PaginatorInterface;
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
        private PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder
    ) {
    }

    #[Route(path: 'page_plasmyds', name: 'page_plasmyds')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        // Créer le formulaire
        $plasmydAdd = $this->createForm(PlasmydFormType::class); 

        // Ajouter si l'utilisateur a les bons rôles
        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')) {
            $plasmydAdd = $this->addForm($request, $em);   
        } 

        // Récupérer tous les plasmyds (sans pagination)
        $plasmyds = $this->plasmydRepository->findAll(10000); // S'assurer que cette méthode accepte un limit

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
    public function delete(Plasmyd $plasmyd, EntityManagerInterface $em): Response
    {
        // Get IDs of strains associated with the plasmyd
        $strainIds = $plasmyd->getStrain()->map(fn($strain) => $strain->getId())->toArray();

        if (count($strainIds) > 0) {
            $this->addFlash('error', 'Cannot delete this plasmyd because it is associated with the following strain IDs: ' . implode(', ', $strainIds) . '.');
        } else {
            // Directly delete if no associated strains
            $em->remove($plasmyd);
            $em->flush();

            $this->addFlash('success', 'Plasmyd "' . $plasmyd->getNamePlasmyd() . '" has been successfully deleted!');
        }

        return $this->redirectToRoute('page_plasmyds');
    }

    #[Route('plasmyds/duplicate/{id}', name: 'duplicate_plasmyd')]
    #[IsGranted('ROLE_SEARCH')]
    public function duplicatePlasmyd(Plasmyd $plasmyd, EntityManagerInterface $em, Security $security): Response
    {
        try {
            // Récupérer l'utilisateur actuellement connecté
            $user = $security->getUser();

            // Créer une nouvelle instance de Plasmyd (copie)
            $clone = new Plasmyd();

            // Copier les champs simples de l'entité d'origine
            $clone->setNamePlasmyd($plasmyd->getNamePlasmyd());
            $clone->setType($plasmyd->getType());
            $clone->setDescription($plasmyd->getDescription());
            $clone->setComment($plasmyd->getComment());

            // Sauvegarder en base
            $em->persist($clone);
            $em->flush();

            // Message flash 
            $this->addFlash('success', 'Plasmyd "' . $clone->getNamePlasmyd() . '" duplicated successfully!');

            return $this->redirectToRoute('page_plasmyds');
            
        } catch (\Throwable $e) {
            $this->addFlash('error', 'An error occurred while duplicating the plasmyd.');
            return $this->redirectToRoute('page_plasmyds');
        }
    }


    #[Route('/plasmyds/delete-multiple', name: 'delete_multiple_plasmyds', methods: ['POST'])]
    #[IsGranted('ROLE_SEARCH')]
    public function deleteMultiplePlasmyds(Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer les IDs sélectionnés depuis la requête POST
        $ids = $request->request->all('selected_plasmyds');

        // Vérifier que ce soit un tableau non vide
        if (!is_array($ids) || empty($ids)) {
            $this->addFlash('error', 'No plasmyd selected.');
            return $this->redirectToRoute('page_plasmyds');
        }

        // Chercher tous les plasmyds correspondants
        $plasmyds = $em->getRepository(Plasmyd::class)->findBy(['id' => $ids]);

        if (!$plasmyds) {
            $this->addFlash('error', 'No plasmyds found for deletion.');
            return $this->redirectToRoute('page_plasmyds');
        }

        $detailsDeleted = [];
        $detailsBlocked = [];

        foreach ($plasmyds as $plasmyd) {
            // Exemple : bloquer si le plasmyd est lié à des souches (strains)
            if (count($plasmyd->getStrain()) > 0) {
                $detailsBlocked[] = sprintf('[ID: %d - Name: %s]', $plasmyd->getId(), $plasmyd->getNamePlasmyd());
                continue;
            }

            // Préparer la suppression
            $detailsDeleted[] = sprintf('[ID: %d - Name: %s]', $plasmyd->getId(), $plasmyd->getNamePlasmyd());
            $em->remove($plasmyd);
        }

        // Exécuter la suppression si des plasmyds sont valides
        if (!empty($detailsDeleted)) {
            $em->flush();
        }

        // Message succès
        if (!empty($detailsDeleted)) {
            $this->addFlash('success', sprintf(
                '%d plasmyd(s) successfully deleted: %s',
                count($detailsDeleted),
                implode(', ', $detailsDeleted)
            ));
        }

        // Message erreur si des suppressions ont été bloquées
        if (!empty($detailsBlocked)) {
            $this->addFlash('error', 'Unable to delete some plasmyds because they are linked to strains: ' . implode(', ', $detailsBlocked));
        }

        // Redirection finale
        return $this->redirectToRoute('page_plasmyds');
    }

    



}