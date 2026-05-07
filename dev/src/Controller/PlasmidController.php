<?php 

namespace App\Controller;

use App\Entity\Plasmid;
use App\Form\PlasmidFormType;
use App\Repository\PlasmidRepository;
use App\Repository\PlasmidRepositoryInterface;
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

class PlasmidController extends AbstractController
{
    public function __construct(
        #[Autowire(service: PlasmidRepository::class)]
        private PlasmidRepositoryInterface $plasmidRepository,
        private PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder
    ) {
    }

    #[Route(path: 'page_plasmids', name: 'page_plasmids')]
    #[IsGranted('ROLE_INTERN')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        // Créer le formulaire
        $plasmidAdd = $this->createForm(PlasmidFormType::class); 

        // Ajouter si l'utilisateur a les bons rôles
        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')) {
            $plasmidAdd = $this->addForm($request, $em, $security);   
        } 

        // Récupérer tous les plasmids (sans pagination)
        $plasmids = $this->plasmidRepository->findBy([], ['id' => 'DESC'], 10000);

        return $this->render('plasmid/main.html.twig', [
            'plasmidForm' => $plasmidAdd, 
            'plasmids' => $plasmids
        ]);
    }


    #[Route(path: '/plasmid', name: 'list_plasmids')]
    #[IsGranted('ROLE_INTERN')]
    public function showAll(): Response
    {
        $plasmids = $this->plasmidRepository->findAll();

        return $this->render('plasmid/list.html.twig', ['plasmids' => $plasmids]);
    }

    #[Route(path: 'strains/plasmids/ajout', name: 'add_plasmid')]
    #[IsGranted('ROLE_SEARCH')]
    public function addForm(Request $request, EntityManagerInterface $em, Security $security): Form
    {
        //Create a new vehicule
        $plasmid = new Plasmid();

        //Create the form
        $plasmidForm = $this->createForm(PlasmidFormType::class, $plasmid);

        //
        $plasmidForm->handleRequest($request);

        if ($plasmidForm->isSubmitted() && $plasmidForm->isValid()) {
            //generate the slug -- not done yet
            $slug = $plasmid->getNamePlasmid(). ' - ' . $plasmid->getType();
            $plasmid->setSlug($slug);

            //get the user -- not done yet

            //stock data
            $em->persist($plasmid);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $plasmidForm;
        }
        return $plasmidForm;
    }

    #[Route(path: 'strains/plasmids/ajout/response', name: 'add_plasmid_reponse')]
    #[IsGranted('ROLE_SEARCH')]
    public function addResponse(Request $request, EntityManagerInterface $em, Security $security): Response
    {

        //Create a new vehicule
        $plasmid = new Plasmid();

        //Create the form
        $plasmidForm = $this->createForm(PlasmidFormType::class, $plasmid);

        //
        $plasmidForm->handleRequest($request);

        if ($plasmidForm->isSubmitted() && $plasmidForm->isValid()) {
            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($plasmid);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $this->redirectToRoute('page_strains');
        }
        return $this->render('plasmid/create.html.twig', compact('plasmidForm'));
    }
    
    #[Route('strains/plasmid/edit/{id}', name: 'edit_plasmid')]
    public function edit(
        Plasmid $plasmid,
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): Response {

        if (!$security->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to edit a plasmid.');
            return $this->redirectToRoute('page_plasmids');
        }

        //Create the form
        $plasmidForm = $this->createForm(PlasmidFormType::class, $plasmid);

        //treat the request
        $plasmidForm->handleRequest($request);

        if ($plasmidForm->isSubmitted() && $plasmidForm->isValid()) {
            //generate the slug
            $slug = $plasmid->getNamePlasmid(). ' - ' . $plasmid->getType();
            $plasmid->setSlug($slug);

            //stock data
            $em->persist($plasmid);
            $em->flush();

            $this->addFlash('success', 'plasmid ' . $plasmid->getNamePlasmid() . ' modified with succes !');

            return $this->redirectToRoute('page_plasmids');
        }
        return $this->render('plasmid/edit.html.twig', compact('plasmidForm'));
    }

    #[Route('strains/plasmid/delete/{id}', name: 'delete_plasmid')]
    public function delete(Plasmid $plasmid, EntityManagerInterface $em, Security $security): Response
    {
        if (!$security->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to delete a plasmid.');
            return $this->redirectToRoute('page_plasmids');
        }

        // Get IDs of strains associated with the plasmid
        $strainIds = $plasmid->getStrain()->map(fn($strain) => $strain->getId())->toArray();

        if (count($strainIds) > 0) {
            $this->addFlash(
                'error',
                sprintf(
                    'Cannot delete Plasmid (ID: %d, Name: "%s") because it is associated with the following strain IDs: %s.',
                    $plasmid->getId(),
                    $plasmid->getNamePlasmid(),
                    implode(', ', $strainIds)
                )
            );
        } else {
            // Directly delete if no associated strains
            $em->remove($plasmid);
            $em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    'Plasmid (ID: %d, Name: "%s") has been successfully deleted!',
                    $plasmid->getId(),
                    $plasmid->getNamePlasmid()
                )
            );
        }

        return $this->redirectToRoute('page_plasmids');
    }


    #[Route('plasmids/duplicate/{id}', name: 'duplicate_plasmid')]
    public function duplicatePlasmid(Plasmid $plasmid, EntityManagerInterface $em, Security $security): Response
    {
        if (!$security->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to duplicate a plasmid.');
            return $this->redirectToRoute('page_plasmids');
        }

        try {
            // Récupérer l'utilisateur actuellement connecté
            $user = $security->getUser();

            // Créer une nouvelle instance de Plasmid (copie)
            $clone = new Plasmid();

            // Copier les champs simples de l'entité d'origine
            $clone->setNamePlasmid($plasmid->getNamePlasmid());
            $clone->setType($plasmid->getType());
            $clone->setDescription($plasmid->getDescription());
            $clone->setComment($plasmid->getComment());

            // Sauvegarder en base
            $em->persist($clone);
            $em->flush();

            // Message flash 
            $this->addFlash('success', 'Plasmid "' . $clone->getNamePlasmid() . '" duplicated successfully!');

            return $this->redirectToRoute('page_plasmids');
            
        } catch (\Throwable $e) {
            $this->addFlash('error', 'An error occurred while duplicating the plasmid.');
            return $this->redirectToRoute('page_plasmids');
        }
    }


    #[Route('/plasmids/delete-multiple', name: 'delete_multiple_plasmids', methods: ['POST'])]
    public function deleteMultiplePlasmids(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to perform this action.');
            return $this->redirectToRoute('page_plasmids'); // ou vers le referer
        }

        // Récupérer les IDs sélectionnés depuis la requête POST
        $ids = $request->request->all('selected_plasmids');

        // Vérifier que ce soit un tableau non vide
        if (!is_array($ids) || empty($ids)) {
            $this->addFlash('error', 'No plasmid selected.');
            return $this->redirectToRoute('page_plasmids');
        }

        // Chercher tous les plasmids correspondants
        $plasmids = $em->getRepository(Plasmid::class)->findBy(['id' => $ids]);

        if (!$plasmids) {
            $this->addFlash('error', 'No plasmids found for deletion.');
            return $this->redirectToRoute('page_plasmids');
        }

        $detailsDeleted = [];
        $detailsBlocked = [];

        foreach ($plasmids as $plasmid) {
            $strainIds = $plasmid->getStrain()->map(fn($strain) => $strain->getId())->toArray();

            if (!empty($strainIds)) {
                // Bloqué : on ajoute le plasmide + ses strains
                $detailsBlocked[] = sprintf(
                    '[ID: %d - Name: %s - Strain IDs: %s]',
                    $plasmid->getId(),
                    $plasmid->getNamePlasmid(),
                    implode(', ', $strainIds)
                );
                continue;
            }

            // Préparer la suppression
            $detailsDeleted[] = sprintf('[ID: %d - Name: %s]', $plasmid->getId(), $plasmid->getNamePlasmid());
            $em->remove($plasmid);
        }

        // Exécuter la suppression si des plasmids sont valides
        if (!empty($detailsDeleted)) {
            $em->flush();
        }

        // Message succès
        if (!empty($detailsDeleted)) {
            $this->addFlash('success', sprintf(
                '%d plasmid(s) successfully deleted: %s',
                count($detailsDeleted),
                implode(', ', $detailsDeleted)
            ));
        }

        // Message erreur si des suppressions ont été bloquées
        if (!empty($detailsBlocked)) {
            $this->addFlash(
                'error',
                'Unable to delete some plasmids because they are linked to strains: ' . implode(', ', $detailsBlocked)
            );
        }

        // Redirection finale
        return $this->redirectToRoute('page_plasmids');
    }

    
    #[Route('/api/plasmid/create', name: 'api_create_plasmid', methods: ['POST'])]
    public function apiCreatePlasmid(Request $request, EntityManagerInterface $em): Response
    {
        // Vérifier les permissions
        if (!$this->isGranted('ROLE_SEARCH')) {
            return $this->json([
                'success' => false,
                'message' => 'You do not have the necessary permissions to create a plasmid.'
            ], 403);
        }
        
        // Récupérer les données
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return $this->json([
                'success' => false,
                'message' => 'Données invalides.'
            ], 400);
        }
        
        try {
            // Créer le nouveau plasmide
            $plasmid = new Plasmid();
            $plasmid->setNamePlasmid($data['namePlasmid']);
            $plasmid->setType($data['type']);
            
            // Champs optionnels
            if (isset($data['description'])) {
                $plasmid->setDescription($data['description']);
            }
            
            if (isset($data['comment'])) {
                $plasmid->setComment($data['comment']);
            }
            
            // Générer le slug
            $slug = $plasmid->getNamePlasmid(). ' - ' . $plasmid->getType();
            $plasmid->setSlug($slug);
            
            // Persister et flusher
            $em->persist($plasmid);
            $em->flush();
            
            // Retourner le résultat avec les données du nouveau plasmide
            return $this->json([
                'success' => true,
                'message' => 'Plasmid created successfully.',
                'plasmid' => [
                    'id' => $plasmid->getId(),
                    'name' => $plasmid->getNamePlasmid(),
                    'type' => $plasmid->getType()
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'An error occurred while creating the plasmid: ' . $e->getMessage()
            ], 500);
        }
    }
}