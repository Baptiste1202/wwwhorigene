<?php 

namespace App\Controller;

use App\Entity\Collec;
use App\Form\CollecFormType;
use App\Repository\CollecRepository;
use App\Repository\CollecRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Knp\Component\Pager\PaginatorInterface;
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
        private PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder
    ) {
    }

    #[Route(path: 'page_collecs', name: 'page_collecs')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $collecAdd = $this->createForm(CollecFormType::class); 

        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')) {
            $collecAdd = $this->addForm($request, $em);  
        }

        $allCollec = $this->collecRepository->findAll(10000);

        return $this->render('collec/main.html.twig', [
            'collecForm' => $collecAdd, 
            'collecs' => $allCollec
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
        $collec = new Collec();

        $collecForm = $this->createForm(CollecFormType::class, $collec);

        $collecForm->handleRequest($request);

        if ($collecForm->isSubmitted() && $collecForm->isValid()) {
            $em->persist($collec);
            $em->flush();

            $this->addFlash('success', 'Collection ' . $collec->getName() . 'added with success !');

            return $collecForm;
        }
        return $collecForm;
    }

    #[Route(path: 'strains/collections/ajout/response', name: 'add_collec')]
    #[IsGranted('ROLE_SEARCH')]
    public function addResponse(Request $request, EntityManagerInterface $em): Response
    {
        $collec = new Collec();

        $collecForm = $this->createForm(CollecFormType::class, $collec);

        $collecForm->handleRequest($request);

        if ($collecForm->isSubmitted() && $collecForm->isValid()) {
            $em->persist($collec);
            $em->flush();

            $this->addFlash('success', 'Collection ' . $collec->getName() . 'added with success !');

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
 
            $em->persist($collec);
            $em->flush();

            $this->addFlash('success', 'Collection ' . $collec->getName() . ' modified with succes !');

            return $this->redirectToRoute('page_collecs');
        }
        return $this->render('collec/edit.html.twig', compact('collecForm'));
    }

    #[Route('/collecs/delete/{id}', name: 'delete_collec')]
    #[IsGranted('ROLE_SEARCH')]
    public function delete(Collec $collec, EntityManagerInterface $em): Response
    {
        // Check if collec is associated with any strains
        $strainIds = $collec->getStrain()->map(fn($strain) => $strain->getId())->toArray();

        if (count($strainIds) > 0) {
            $this->addFlash('error', 'Cannot delete this collection because it is associated with the following strain IDs: ' . implode(', ', $strainIds) . '.');
        } else {
            // Directly delete
            $em->remove($collec);
            $em->flush();

            $this->addFlash('success', 'Collection "' . $collec->getName() . '" has been successfully deleted!');
        }

        return $this->redirectToRoute('page_collecs');
    }

    #[Route('strains/collec/duplicate/{id}', name: 'duplicate_collec')]
    #[IsGranted('ROLE_SEARCH')]
    public function duplicateCollec(Collec $collec, EntityManagerInterface $em, Security $security): Response
    {
        try {
            // Récupérer l'utilisateur connecté
            $user = $security->getUser();

            // Créer une nouvelle instance de Collec (la copie)
            $clone = new Collec();

            // Copier les champs simples de l'entité originale
            $clone->setName($collec->getName());
            $clone->setDescription($collec->getDescription());
            $clone->setComment($collec->getComment()); 

            // Enregistrement en base de la copie
            $em->persist($clone);
            $em->flush();

            // Message flash
            $this->addFlash('success', 'Collection "' . $clone->getName() . '" duplicated successfully!');

            return $this->redirectToRoute('page_collecs');

        } catch (\Throwable $e) {
            $this->addFlash('error', 'Error occurred while duplicating the collection.');
            return $this->redirectToRoute('page_collecs');
        }
    }

    #[Route('/collecs/delete-multiple', name: 'delete_multiple_collecs', methods: ['POST'])]
    #[IsGranted('ROLE_SEARCH')]
    public function deleteMultiple(Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer les ids sélectionnés via la requête POST
        $ids = $request->request->all('selected_collecs');

        // Vérifier que ce soit un tableau non vide
        if (!is_array($ids) || empty($ids)) {
            $this->addFlash('error', 'No collection selected.');
            return $this->redirectToRoute('page_collecs');
        }

        // Chercher toutes les collections correspondantes
        $collecs = $em->getRepository(Collec::class)->findBy(['id' => $ids]);

        if (!$collecs) {
            $this->addFlash('error', 'No collection found for deletion.');
            return $this->redirectToRoute('page_collecs');
        }

        $detailsDeleted = [];
        $detailsBlocked = [];

        foreach ($collecs as $collec) {
            // Si la collection a des souches associées, on bloque la suppression
            if (count($collec->getStrain()) > 0) {
                $detailsBlocked[] = sprintf('[ID: %d - Nom: %s]', $collec->getId(), $collec->getName());
                continue;
            }
            // Sinon on prépare la suppression et on stocke les détails pour message
            $detailsDeleted[] = sprintf('[ID: %d - Nom: %s]', $collec->getId(), $collec->getName());
            $em->remove($collec);
        }

        // Valider la suppression en base (pour les collections sans souches)
        if (!empty($detailsDeleted)) {
            $em->flush();
        }

        // Message succès pour les collections supprimées
        if (!empty($detailsDeleted)) {
            $this->addFlash('success', sprintf(
                '%d collection(s) successfully deleted: %s',
                count($detailsDeleted),
                implode(', ', $detailsDeleted)
            ));
        }

        // Message d’erreur pour les collections bloquées
        if (!empty($detailsBlocked)) {
           $this->addFlash('error', 'Unable to delete some collections because they are associated with strains: ' . implode(', ', $detailsBlocked));
        }

        // Rediriger vers la page des collections
        return $this->redirectToRoute('page_collecs');
    }


}