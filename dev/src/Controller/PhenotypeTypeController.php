<?php

namespace App\Controller;

use App\Entity\PhenotypeType;
use App\Entity\Phenotype;
use App\Form\PhenotypeTypeFormType;
use App\Repository\PhenotypeTypeRepository;
use App\Repository\PhenotypeTypeRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PhenotypeTypeController extends AbstractController
{
    public function __construct(
        #[Autowire(service: PhenotypeTypeRepository::class)]
        private PhenotypeTypeRepositoryInterface $phenotypetypeRepository,
        private PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder
    ) {
    }

    #[Route(path: 'strains/page_phenotypetypes', name: 'page_phenotypetypes')]
    #[IsGranted('ROLE_ADMIN')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        // même pattern que ProjectController::showPage
        $phenotypeTypeAdd = $this->createForm(PhenotypeTypeFormType::class);

        // traitement du submit via addForm()
        $phenotypeTypeAdd = $this->addForm($request, $em);

        $allTypes = $this->phenotypetypeRepository->findAll();

        return $this->render('phenotypeType/main.html.twig', [
            // IMPORTANT : clé attendue par tes templates + FormView
            'phenotypeTypeForm' => $phenotypeTypeAdd->createView(),
            'types' => $allTypes,
        ]);
    }

    #[Route(path: 'strains/phenotypetype/ajout', name: 'add_phenotypetype')]
    #[IsGranted('ROLE_ADMIN')]
    public function addForm(Request $request, EntityManagerInterface $em): Form
    {
        $type = new PhenotypeType();

        $form = $this->createForm(PhenotypeTypeFormType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($type);
            $em->flush();
            // on retourne le Form (même pattern que ProjectController)
            return $form;
        }

        return $form;
    }

    #[Route(path: 'strains/phenotypetype/ajout/response', name: 'add_phenotypetype_response')]
    #[IsGranted('ROLE_ADMIN')]
    public function addResponse(Request $request, EntityManagerInterface $em): Response
    {
        $type = new PhenotypeType();

        $form = $this->createForm(PhenotypeTypeFormType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($type);
            $em->flush();
            return $this->redirectToRoute('page_phenotypetypes');
        }

        // IMPORTANT : passer phenotypeTypeForm => FormView
        return $this->render('phenotypeType/create.html.twig', [
            'phenotypeTypeForm' => $form->createView(),
        ]);
    }

    #[Route('strains/phenotypetype/edit/{id}', name: 'edit_phenotypetype')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        PhenotypeType $phenotypetype,
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): Response
    {
        if (!$security->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'You do not have permission to edit a phenotype type.');
            return $this->redirectToRoute('page_phenotypetypes');
        }

        $form = $this->createForm(PhenotypeTypeFormType::class, $phenotypetype);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($phenotypetype);
            $em->flush();

            $this->addFlash('success', 'Phenotype type "' . $phenotypetype->getType() . '" modified with success!');
            return $this->redirectToRoute('page_phenotypetypes');
        }

        // IMPORTANT : passer phenotypeTypeForm => FormView
        return $this->render('phenotypeType/edit.html.twig', [
            'phenotypeTypeForm' => $form->createView(),
        ]);
    }

    #[Route('phenotypetypes/duplicate/{id}', name: 'duplicate_phenotypetype')]
    #[IsGranted('ROLE_ADMIN')]
    public function duplicate(PhenotypeType $phenotypetype, EntityManagerInterface $em, Security $security): Response
    {
        try {
            if (!$security->isGranted('ROLE_ADMIN')) {
                $this->addFlash('error', 'You do not have permission to duplicate a phenotype type.');
                return $this->redirectToRoute('page_phenotypetypes');
            }

            $clone = new PhenotypeType();
            $clone->setType($phenotypetype->getType());

            $em->persist($clone);
            $em->flush();

            $this->addFlash('success', 'Phenotype type "' . $clone->getType() . '" duplicated successfully!');
            return $this->redirectToRoute('page_phenotypetypes');
        } catch (\Throwable $e) {
            $this->addFlash('error', 'Error occurred while duplicating the phenotype type.');
            return $this->redirectToRoute('page_phenotypetypes');
        }
    }

    #[Route('/strains/phenotypetype/delete/{id}', name: 'delete_phenotypetype', methods: ['POST','GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(PhenotypeType $phenotypetype, EntityManagerInterface $em): Response
    {
        // Get IDs of strains whose phenotype is linked to this phenotype type
        $rows = $em->createQuery(
            'SELECT s.id
            FROM App\Entity\Strain s
            JOIN s.phenotype p
            WHERE p.phenotypeType = :pt'
        )
        ->setParameter('pt', $phenotypetype)
        ->getScalarResult(); // => [ ['id' => 123], ['id' => 456], ...]

        $strainIds = array_map(fn(array $r) => (string) $r['id'], $rows);

        if (!empty($strainIds)) {
            $this->addFlash(
                'error',
                sprintf(
                    'Cannot delete PhenotypeType (ID: %d, Type: "%s") because it is associated with the following strain IDs: %s.',
                    $phenotypetype->getId(),
                    method_exists($phenotypetype, 'getType') ? $phenotypetype->getType() : (string) $phenotypetype->getId(),
                    implode(', ', $strainIds)
                )
            );
            return $this->redirectToRoute('page_phenotypetypes');
        }

        $em->remove($phenotypetype);
        $em->flush();

        $this->addFlash(
            'success',
            sprintf(
                'PhenotypeType (ID: %d, Type: "%s") has been successfully deleted.',
                $phenotypetype->getId(),
                method_exists($phenotypetype, 'getType') ? $phenotypetype->getType() : (string) $phenotypetype->getId()
            )
        );

        return $this->redirectToRoute('page_phenotypetypes');
    }

    #[Route('/phenotypetypes/delete-multiple', name: 'delete_multiple_phenotypetypes', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteMultiple(Request $request, EntityManagerInterface $em): Response
    {
        $ids = $request->request->all('selected_types');

        if (!is_array($ids) || empty($ids)) {
            $this->addFlash('error', 'No phenotype type selected.');
            return $this->redirectToRoute('page_phenotypetypes');
        }

        $types = $em->getRepository(PhenotypeType::class)->findBy(['id' => $ids]);

        if (!$types) {
            $this->addFlash('error', 'No phenotype type found for deletion.');
            return $this->redirectToRoute('page_phenotypetypes');
        }

        $detailsDeleted = [];
        $detailsBlocked = [];

        foreach ($types as $type) {
            // Check if this type is linked to at least one strain
            $rows = $em->createQuery(
                'SELECT s.id
                FROM App\Entity\Strain s
                JOIN s.phenotype p
                WHERE p.phenotypeType = :pt'
            )
            ->setParameter('pt', $type)
            ->getScalarResult();

            $strainIds = array_map(fn(array $r) => (string) $r['id'], $rows);

            if (!empty($strainIds)) {
                $detailsBlocked[] = sprintf(
                    '[PhenotypeType ID: %d - Type: %s → Linked Strains: %s]',
                    $type->getId(),
                    $type->getType(),
                    implode(', ', $strainIds)
                );
                continue;
            }

            $detailsDeleted[] = sprintf('[PhenotypeType ID: %d - Type: %s]', $type->getId(), $type->getType());
            $em->remove($type);
        }

        if (!empty($detailsDeleted)) {
            $em->flush();
            $this->addFlash('success', sprintf(
                '%d phenotype type(s) successfully deleted: %s',
                count($detailsDeleted),
                implode(', ', $detailsDeleted)
            ));
        }

        if (!empty($detailsBlocked)) {
            $this->addFlash(
                'error',
                'Unable to delete the following phenotype type(s) because they are linked to strains: ' 
                . implode(', ', $detailsBlocked)
            );
        }

        return $this->redirectToRoute('page_phenotypetypes');
    }


}
