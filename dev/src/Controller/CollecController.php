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
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

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
    #[IsGranted('ROLE_INTERN')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $collecAdd = $this->createForm(CollecFormType::class); 

        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')) {
            $collecAdd = $this->addForm($request, $em);  
        }

        $allCollec = $this->collecRepository->findBy([], ['id' => 'DESC'], 10000);

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

    #[Route('strains/collec/edit/{id}', name: 'edit_collec')]
    public function edit(
        Collec $collec,
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): Response {

        if (!$security->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to edit a collection.');
            return $this->redirectToRoute('page_collecs');
        }
        
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

    #[Route('strains/collec/duplicate/{id}', name: 'duplicate_collec')]
    public function duplicateCollec(Collec $collec, EntityManagerInterface $em, Security $security): Response
    {
        try {

            if (!$security->isGranted('ROLE_SEARCH')) {
                $this->addFlash('error', 'You do not have permission to duplicate a collection.');
                return $this->redirectToRoute('page_collecs');
            }

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

        } catch (AccessDeniedException $e) {
            $this->addFlash('error', 'You do not have permission to edit this strain.');
            return $this->redirectToRoute('page_strains');
        } catch (\Throwable $e) {
            $this->addFlash('error', 'Error occurred while duplicating the collection.');
            return $this->redirectToRoute('page_collecs');
        }
    }

    #[Route('/collecs/delete/{id}', name: 'delete_collec')]
    public function delete(Collec $collec, EntityManagerInterface $em, Security $security): Response
    {
        if (!$security->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to delete a collection.');
            return $this->redirectToRoute('page_collecs');
        }

        // Check if collec is associated with any strains
        $strainIds = $collec->getStrain()->map(fn($strain) => $strain->getId())->toArray();

        if (count($strainIds) > 0) {
            $this->addFlash(
                'error',
                sprintf(
                    'Cannot delete Collection (ID: %d, Name: "%s") because it is associated with the following strain IDs: %s.',
                    $collec->getId(),
                    $collec->getName(),
                    implode(', ', $strainIds)
                )
            );
        } else {
            // Directly delete
            $em->remove($collec);
            $em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    'Collection (ID: %d, Name: "%s") has been successfully deleted!',
                    $collec->getId(),
                    $collec->getName()
                )
            );
        }

        return $this->redirectToRoute('page_collecs');
    }

    #[Route('/collecs/delete-multiple', name: 'delete_multiple_collecs', methods: ['POST'])]
    public function deleteMultiple(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to perform this action.');
            return $this->redirectToRoute('page_collecs'); // ou vers le referer
        }
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
            $strainIds = $collec->getStrain()->map(fn($strain) => $strain->getId())->toArray();

            if (!empty($strainIds)) {
                $detailsBlocked[] = sprintf(
                    '[ID: %d - Name: "%s" → Linked Strains: %s]',
                    $collec->getId(),
                    $collec->getName(),
                    implode(', ', $strainIds)
                );
                continue;
            }

            // Préparer la suppression et stocker les détails pour message
            $detailsDeleted[] = sprintf('[ID: %d - Name: "%s"]', $collec->getId(), $collec->getName());
            $em->remove($collec);
        }

        // Valider la suppression en base (pour les collections sans souches)
        if (!empty($detailsDeleted)) {
            $em->flush();
            $this->addFlash('success', sprintf(
                '%d collection(s) successfully deleted: %s',
                count($detailsDeleted),
                implode(', ', $detailsDeleted)
            ));
        }

        // Message d’erreur pour les collections bloquées
        if (!empty($detailsBlocked)) {
        $this->addFlash(
            'error',
            'Unable to delete some collections because they are linked to strains: ' . implode(', ', $detailsBlocked)
        );
        }

        // Rediriger vers la page des collections
        return $this->redirectToRoute('page_collecs');
    }


}