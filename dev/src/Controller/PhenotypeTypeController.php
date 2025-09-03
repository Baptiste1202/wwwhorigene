<?php

namespace App\Controller;

use App\Entity\PhenotypeType;
use App\Form\PhenotypeTypeFormType;
use App\Repository\PhenotypeTypeRepository;
use App\Repository\PhenotypeTypeRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class PhenotypeTypeController extends AbstractController
{
    public function __construct(
        #[Autowire(service: PhenotypeTypeRepository::class)]
        private PhenotypeTypeRepositoryInterface $phenotypetypeRepository,
        private PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder
    ) {
    }

    #[Route(path: 'page_phenotypetypes', name: 'page_phenotypetypes')]
    #[IsGranted('ROLE_ADMIN')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $phenotypetype = new PhenotypeType();
        $phenotypetypeForm = $this->createForm(PhenotypeTypeFormType::class, $phenotypetype);
        $phenotypetypeForm->handleRequest($request);

        if ($phenotypetypeForm->isSubmitted() && $phenotypetypeForm->isValid()) {
            $em->persist($phenotypetype);
            $em->flush();

            $this->addFlash('success', 'Phenotype type ' . $phenotypetype->getType() . ' added with success!');
            return $this->redirectToRoute('page_phenotypetypes');
        }

        $types = $this->phenotypetypeRepository->findAll();

        return $this->render('phenotypeType/main.html.twig', [
            'phenotypetypeForm' => $phenotypetypeForm->createView(),
            'types' => $types
        ]);
    }

    #[Route('phenotypetype/edit/{id}', name: 'edit_phenotypetype')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        PhenotypeType $phenotypetype,
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        $phenotypetypeForm = $this->createForm(PhenotypeTypeFormType::class, $phenotypetype);

        $phenotypetypeForm->handleRequest($request);

        if ($phenotypetypeForm->isSubmitted() && $phenotypetypeForm->isValid()) {

            $em->persist($phenotypetype);
            $em->flush();

            $this->addFlash('success', 'Phenotype type ' . $phenotypetype->getType() . ' modified with success!');

            return $this->redirectToRoute('page_phenotypetypes');
        }
        return $this->render('phenotypetype/edit.html.twig', compact('phenotypetypeForm'));
    }

    #[Route('strains/phenotypetype/delete/{id}', name: 'delete_phenotypetype')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(phenotypetype $phenotypetype, EntityManagerInterface $em): Response
    {
        $em->remove($phenotypetype);
        $em->flush();

        $this->addFlash('success', 'Phenotype type ' . $phenotypetype->getType() . ' deleted with success!');

        return $this->redirectToRoute('page_phenotypetypes');
    }
}
