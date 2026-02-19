<?php

namespace App\Controller;

use App\Entity\MethodSequencingType;
use App\Form\MethodSequencingTypeFormType;
use App\Repository\MethodSequencingTypeRepository;
use App\Repository\MethodSequencingTypeRepositoryInterface;
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

class MethodSequencingTypeController extends AbstractController
{
    public function __construct(
        #[Autowire(service: MethodSequencingTypeRepository::class)]
        private MethodSequencingTypeRepositoryInterface $methodSequencingTypeRepository,
        private PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder
    ) {
    }

    #[Route(path: 'strains/page_methodsequencingtypes', name: 'page_methodsequencingtypes')]
    #[IsGranted('ROLE_ADMIN')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $methodSequencingTypeAdd = $this->createForm(MethodSequencingTypeFormType::class);

        // même logique que ton controller : addForm() retourne un Form (soumis ou non)
        $methodSequencingTypeAdd = $this->addForm($request, $em);

        $allTypes = $this->methodSequencingTypeRepository->findBy([], ['id' => 'DESC'], 10000);

        return $this->render('methodSequencingType/main.html.twig', [
            'methodSequencingTypeForm' => $methodSequencingTypeAdd->createView(),
            'methodSequencingTypes' => $allTypes,
        ]);
    }

    #[Route(path: 'strains/method_sequencing_type/ajout', name: 'add_method_sequencing_type')]
    #[IsGranted('ROLE_ADMIN')]
    public function addForm(Request $request, EntityManagerInterface $em): Form
    {
        $type = new MethodSequencingType();

        $form = $this->createForm(MethodSequencingTypeFormType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($type);
            $em->flush();
            return $form;
        }

        return $form;
    }

    #[Route(path: 'strains/method_sequencing_type/ajout/response', name: 'add_method_sequencing_type_response')]
    #[IsGranted('ROLE_ADMIN')]
    public function addResponse(Request $request, EntityManagerInterface $em): Response
    {
        $type = new MethodSequencingType();

        $form = $this->createForm(MethodSequencingTypeFormType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($type);
            $em->flush();
            return $this->redirectToRoute('page_methodsequencingtypes');
        }

        return $this->render('methodSequencingType/create.html.twig', [
            'methodSequencingTypeForm' => $form->createView(),
        ]);
    }

    #[Route('strains/method_sequencing_type/edit/{id}', name: 'edit_method_sequencing_type')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        MethodSequencingType $methodSequencingType,
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): Response
    {
        if (!$security->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'You do not have permission to edit a method sequencing type.');
            return $this->redirectToRoute('page_methodsequencingtypes');
        }

        $form = $this->createForm(MethodSequencingTypeFormType::class, $methodSequencingType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($methodSequencingType);
            $em->flush();

            $this->addFlash('success', 'Method sequencing type "' . $methodSequencingType->getName() . '" modified with success!');
            return $this->redirectToRoute('page_methodsequencingtypes');
        }

        return $this->render('methodSequencingType/edit.html.twig', [
            'methodSequencingTypeForm' => $form->createView(),
        ]);
    }

    #[Route('method-sequencing-types/duplicate/{id}', name: 'duplicate_method_sequencing_type')]
    #[IsGranted('ROLE_ADMIN')]
    public function duplicate(MethodSequencingType $methodSequencingType, EntityManagerInterface $em, Security $security): Response
    {
        try {
            if (!$security->isGranted('ROLE_ADMIN')) {
                $this->addFlash('error', 'You do not have permission to duplicate a method sequencing type.');
                return $this->redirectToRoute('page_methodsequencingtypes');
            }

            $clone = new MethodSequencingType();
            $clone->setName($methodSequencingType->getName());

            $em->persist($clone);
            $em->flush();

            $this->addFlash('success', 'Method sequencing type "' . $clone->getName() . '" duplicated successfully!');
            return $this->redirectToRoute('page_methodsequencingtypes');
        } catch (\Throwable $e) {
            $this->addFlash('error', 'Error occurred while duplicating the method sequencing type.');
            return $this->redirectToRoute('page_methodsequencingtypes');
        }
    }

    #[Route('/strains/method_sequencing_type/delete/{id}', name: 'delete_method_sequencing_type', methods: ['POST','GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(MethodSequencingType $methodSequencingType, EntityManagerInterface $em): Response
    {
        // Bloqué si au moins un Sequencing utilise ce type (relation sequencing.name -> MethodSequencingType)
        $rows = $em->createQuery(
            'SELECT ms.id
            FROM App\Entity\MethodSequencing ms
            WHERE ms.name = :mst'
        )
        ->setParameter('mst', $methodSequencingType)
        ->setMaxResults(2000)
        ->getScalarResult();


        $sequencingIds = array_map(fn(array $r) => (string) $r['id'], $rows);

        if (!empty($sequencingIds)) {
            $this->addFlash(
                'error',
                sprintf(
                    'Cannot delete MethodSequencingType (ID: %d, Name: "%s") because it is associated with the following sequencing IDs: %s.',
                    $methodSequencingType->getId(),
                    $methodSequencingType->getName(),
                    implode(', ', $sequencingIds)
                )
            );
            return $this->redirectToRoute('page_methodsequencingtypes');
        }

        $em->remove($methodSequencingType);
        $em->flush();

        $this->addFlash(
            'success',
            sprintf(
                'MethodSequencingType (ID: %d, Name: "%s") has been successfully deleted.',
                $methodSequencingType->getId(),
                $methodSequencingType->getName()
            )
        );

        return $this->redirectToRoute('page_methodsequencingtypes');
    }

    #[Route('/method-sequencing-types/delete-multiple', name: 'delete_multiple_method_sequencing_types', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteMultiple(Request $request, EntityManagerInterface $em): Response
    {
        $ids = $request->request->all('selected_method_sequencing_types');

        if (!is_array($ids) || empty($ids)) {
            $this->addFlash('error', 'No method sequencing type selected.');
            return $this->redirectToRoute('page_methodsequencingtypes');
        }

        $types = $em->getRepository(MethodSequencingType::class)->findBy(['id' => $ids]);

        if (!$types) {
            $this->addFlash('error', 'No method sequencing type found for deletion.');
            return $this->redirectToRoute('page_methodsequencingtypes');
        }

        $detailsDeleted = [];
        $detailsBlocked = [];

        foreach ($types as $type) {
            // ✅ Bloqué si lié à MethodSequencing (table "sequencing")
            $rows = $em->createQuery(
                'SELECT ms.id
                FROM App\Entity\MethodSequencing ms
                WHERE ms.name = :mst'
            )
                ->setParameter('mst', $type)
                ->setMaxResults(2000)
                ->getScalarResult();

            $sequencingIds = array_map(fn(array $r) => (string) $r['id'], $rows);

            if (!empty($sequencingIds)) {
                $detailsBlocked[] = sprintf(
                    '[MethodSequencingType ID: %d - Name: %s → Linked Sequencing IDs: %s]',
                    $type->getId(),
                    $type->getName(),
                    implode(', ', $sequencingIds)
                );
                continue;
            }

            $detailsDeleted[] = sprintf('[MethodSequencingType ID: %d - Name: %s]', $type->getId(), $type->getName());
            $em->remove($type);
        }

        if (!empty($detailsDeleted)) {
            $em->flush();
            $this->addFlash('success', sprintf(
                '%d method sequencing type(s) successfully deleted: %s',
                count($detailsDeleted),
                implode(', ', $detailsDeleted)
            ));
        }

        if (!empty($detailsBlocked)) {
            $this->addFlash(
                'error',
                'Unable to delete the following method sequencing type(s) because they are linked to sequencing records: '
                . implode(', ', $detailsBlocked)
            );
        }

        return $this->redirectToRoute('page_methodsequencingtypes');
    }
}
