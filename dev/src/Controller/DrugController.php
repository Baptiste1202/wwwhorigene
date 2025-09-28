<?php 

namespace App\Controller;

use App\Entity\DrugResistance;
use App\Form\DrugFormType;
use App\Repository\DrugRepository;
use App\Repository\DrugRepositoryInterface;
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

class DrugController extends AbstractController
{
    public function __construct(
        #[Autowire(service: DrugRepository::class)]
        private DrugRepositoryInterface $drugRepository,
        private PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder
    ) {
    }

    #[Route(path: 'page_drugs', name: 'page_drugs')]
    #[IsGranted('ROLE_INTERN')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        // Créer le formulaire
        $drugAdd = $this->createForm(DrugFormType::class); 

        // Ajouter si l'utilisateur a les bons rôles
        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')) {
            $drugAdd = $this->addForm($request, $em);   
        } 

        // Récupérer tous les drugs (sans pagination)
        $drugs = $this->drugRepository->findAll(10000); // S'assurer que cette méthode accepte un limit

        return $this->render('drug/main.html.twig', [
            'drugForm' => $drugAdd, 
            'drugs' => $drugs
        ]);
    }

    #[Route(path: '/drug', name: 'list_drugs')]
    #[IsGranted('ROLE_INTERN')]
    public function showAll(): Response
    {
        $drugs = $this->drugRepository->findAll();

        return $this->render('drug/list.html.twig', ['drugs' => $drugs]);
    }

    #[Route(path: 'strains/drugs/ajout', name: 'add_drug')]
    #[IsGranted('ROLE_SEARCH')]
    public function addForm(Request $request, EntityManagerInterface $em): Form
    {
        $drug = new DrugResistance();
        $drugForm = $this->createForm(DrugFormType::class, $drug);
        $drugForm->handleRequest($request);

        if ($drugForm->isSubmitted() && $drugForm->isValid()) {
            $em->persist($drug);
            $em->flush();

            $this->addFlash('success', 'Drug ' . $drug->getName() . 'added with success !');

            return $drugForm;
        }
        return $drugForm;
    }

    #[Route(path: 'strains/drugs/ajout/response', name: 'add_drug')]
    #[IsGranted('ROLE_INTERN')]
    public function addResponse(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        if (!$security->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to add a drug.');
            return $this->redirectToRoute('page_drugs');
        }

        $drug = new DrugResistance();
        $drugForm = $this->createForm(DrugFormType::class, $drug);
        $drugForm->handleRequest($request);

        if ($drugForm->isSubmitted() && $drugForm->isValid()) {
            $em->persist($drug);
            $em->flush();

            $this->addFlash('success', 'Drug ' . $drug->getName() . 'added with success !');

            return $this->redirectToRoute('page_strains');
        }
        return $this->render('drug/create.html.twig', compact('drugForm'));
    }

    #[Route('strains/drug/edit/{id}', name: 'edit_drug')]
    public function edit(
        DrugResistance $drug,
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): Response
    {
        if (!$security->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to edit a drug.');
            return $this->redirectToRoute('page_drugs');
        }

        $drugForm = $this->createForm(DrugFormType::class, $drug);
        $drugForm->handleRequest($request);

        if ($drugForm->isSubmitted() && $drugForm->isValid()) {
            $em->persist($drug);
            $em->flush();

            $this->addFlash('success', 'drug ' . $drug->getName() . ' modified with succes !');

            return $this->redirectToRoute('page_drugs');
        }
        return $this->render('drug/edit.html.twig', compact('drugForm'));
    }

    #[Route('drugs/duplicate/{id}', name: 'duplicate_drug')]
    public function duplicateDrug(DrugResistance $drug, EntityManagerInterface $em, Security $security): Response
    {
        if (!$security->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to duplicate a drug.');
            return $this->redirectToRoute('page_drugs');
        }

        try {
            // Créer une nouvelle instance Drug
            $clone = new DrugResistance();

            // Copier les champs simples (adapte si d'autres champs)
            $clone->setName($drug->getName());
            $clone->setType($drug->getType());
            $clone->setDescription($drug->getDescription());
            $clone->setComment($drug->getComment());

            // Persist
            $em->persist($clone);
            $em->flush();

            $this->addFlash('success', 'Drug "' . $clone->getName() . '" duplicated successfully!');

            return $this->redirectToRoute('page_drugs');
        } catch (\Throwable $e) {
            $this->addFlash('error', 'An error occurred while duplicating the drug.');
            return $this->redirectToRoute('page_drugs');
        }
    }

    #[Route('strains/drug/delete/{id}', name: 'delete_drug')]
    public function delete(DrugResistance $drug, EntityManagerInterface $em, Security $security): Response
    {
        if (!$security->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to delete a drug.');
            return $this->redirectToRoute('page_drugs');
        }

        $strainIds = $drug->getDrugResistanceOnStrains()
            ->map(fn($rel) => $rel->getStrain()->getId())
            ->toArray();

        if (count($strainIds) > 0) {
            $this->addFlash(
                'error',
                sprintf(
                    'Cannot delete Drug (ID: %d, Name: "%s") because it is associated with the following strain IDs: %s.',
                    $drug->getId(),
                    $drug->getName(),
                    implode(', ', $strainIds)
                )
            );
        } else {
            // Supprime si aucune association
            $em->remove($drug);
            $em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    'Drug (ID: %d, Name: "%s") has been successfully deleted!',
                    $drug->getId(),
                    $drug->getName()
                )
            );
        }

        return $this->redirectToRoute('page_drugs');
    }

    #[Route('/drugs/delete-multiple', name: 'delete_multiple_drugs', methods: ['POST'])]
    public function deleteMultipleDrugs(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to perform this action.');
            return $this->redirectToRoute('page_drugs'); // ou vers le referer
        }
        // Récupérer les IDs sélectionnés depuis la requête POST
        $ids = $request->request->all('selected_drugs');

        if (!is_array($ids) || empty($ids)) {
            $this->addFlash('error', 'No drug selected.');
            return $this->redirectToRoute('page_drugs');
        }

        // Chercher tous les drugs correspondants
        $drugs = $em->getRepository(DrugResistance::class)->findBy(['id' => $ids]);

        if (!$drugs) {
            $this->addFlash('error', 'No drugs found for deletion.');
            return $this->redirectToRoute('page_drugs');
        }

        $detailsDeleted = [];
        $detailsBlocked = [];

        foreach ($drugs as $drug) {
            $strainIds = $drug->getDrugResistanceOnStrains()
                ->map(fn($rel) => $rel->getStrain()->getId())
                ->toArray();

            if (!empty($strainIds)) {
                $detailsBlocked[] = sprintf(
                    '[ID: %d - Name: "%s" → Linked Strains: %s]',
                    $drug->getId(),
                    $drug->getName(),
                    implode(', ', $strainIds)
                );
                continue;
            }

            $detailsDeleted[] = sprintf('[ID: %d - Name: "%s"]', $drug->getId(), $drug->getName());
            $em->remove($drug);
        }

        // Exécuter la suppression si des drugs sont valides
        if (!empty($detailsDeleted)) {
            $em->flush();
            $this->addFlash('success', sprintf(
                '%d drug(s) successfully deleted: %s',
                count($detailsDeleted),
                implode(', ', $detailsDeleted)
            ));
        }

        // Message erreur si des suppressions ont été bloquées
        if (!empty($detailsBlocked)) {
            $this->addFlash(
                'error',
                'Unable to delete some drugs because they are linked to strains: ' . implode(', ', $detailsBlocked)
            );
        }

        // Redirection finale
        return $this->redirectToRoute('page_drugs');
    }

}