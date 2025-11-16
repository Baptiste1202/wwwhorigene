<?php

namespace App\Controller;

use App\Entity\Sample;
use App\Form\SampleFormType;
use App\Repository\SampleRepository;
use App\Repository\SampleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;    
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SampleController extends AbstractController
{
    public function __construct(
        #[Autowire(service: SampleRepository::class)]
        private SampleRepositoryInterface $sampleRepository,
    ) {
    }

    #[Route(path: 'samples/page', name: 'page_samples')]
    #[IsGranted('ROLE_INTERN')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $sampleAdd = $this->createForm(SampleFormType::class);

        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')) {
            $sampleAdd = $this->addForm($request, $em);
        }

        // Ici, il faut bien que findAll accepte un paramètre de limit !
        $samples = $this->sampleRepository->findAll(10000);

        return $this->render('sample/main.html.twig', [
            'sampleForm' => $sampleAdd,
            'samples'    => $samples
        ]);
    }

    #[Route(path: '/sample', name: 'list_samples')]
    #[IsGranted('ROLE_INTERN')]
    public function listAll(): Response
    {
        $samples = $this->sampleRepository->findAll();

        return $this->render('sample/list.html.twig', ['samples' => $samples]);
    }

    #[Route(path: 'samples/add', name: 'add_sample')]
    #[IsGranted('ROLE_ADMIN')]
    public function addForm(Request $request, EntityManagerInterface $em): Form
    {
        $sample = new Sample();

        $sampleForm = $this->createForm(SampleFormType::class, $sample);

        $sampleForm->handleRequest($request);

        if ($sampleForm->isSubmitted() && $sampleForm->isValid()) {

            $sample->setUser($this->getUser());
            $em->persist($sample);
            $em->flush();

            return $sampleForm;
        }

        return $sampleForm;
    }

    #[Route(path: 'samples/add/response', name: 'add_sample_response')]
    #[IsGranted('ROLE_ADMIN')]
    public function addResponse(Request $request, EntityManagerInterface $em): Response
    {
        $sample = new Sample();
        $sampleForm = $this->createForm(SampleFormType::class, $sample);

        $sampleForm->handleRequest($request);

        if ($sampleForm->isSubmitted() && $sampleForm->isValid()) {
            $em->persist($sample);
            $em->flush();

            return $this->redirectToRoute('page_samples');
        }
        return $this->render('sample/create.html.twig', compact('sampleForm'));
    }

    #[Route('strains/sample/edit/{id}', name: 'edit_sample')]
    public function edit(
        Sample $sample,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        
        if (!$this->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to perform this action.');
            return $this->redirectToRoute('page_samples'); // ou vers le referer
        }

        $sampleForm = $this->createForm(SampleFormType::class, $sample);
        $sampleForm->handleRequest($request);

        if ($sampleForm->isSubmitted() && $sampleForm->isValid()) {
            $em->persist($sample);
            $em->flush();

            $this->addFlash('success', 'Sample "' . $sample->getName() . '" modified with success!');

            return $this->redirectToRoute('page_samples');
        }
        return $this->render('sample/edit.html.twig', compact('sampleForm'));
    }

    #[Route('strains/sample/delete/{id}', name: 'delete_sample')]
    public function delete(Sample $sample, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to perform this action.');
            return $this->redirectToRoute('page_samples'); // ou vers le referer
        }

        // Get IDs of strains associated with the sample
        $strainIds = $sample->getStrain()->map(fn($strain) => $strain->getId())->toArray();

        if (count($strainIds) > 0) {
            $this->addFlash('error', 'Cannot delete this sample because it is associated with the following strain IDs: ' . implode(', ', $strainIds) . '.');
        } else {
            // Directly delete
            $em->remove($sample);
            $em->flush();

            $this->addFlash('success', 'Sample "' . $sample->getName() . '" has been successfully deleted!');
        }

        return $this->redirectToRoute('page_samples');
    }

    #[Route('samples/duplicate/{id}', name: 'duplicate_sample')]
    public function duplicate(
        Sample $sample,
        EntityManagerInterface $em,
        Security $security
    ): Response {
        
        if (!$this->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to perform this action.');
            return $this->redirectToRoute('page_samples'); // ou vers le referer
        }

        try {
            $user = $security->getUser();

            dd($user);

            $clone = new Sample();
            $clone->setName($sample->getName());
            $clone->setType($sample->getType());
            $clone->setDate($sample->getDate());
            $clone->setCountry($sample->getCountry());
            $clone->setCity($sample->getCity());
            $clone->setLocalisation($sample->getLocalisation());
            $clone->setUnderLocalisation($sample->getUnderLocalisation());
            $clone->setGps($sample->getGps());
            $clone->setEnvironment($sample->getEnvironment());
            $clone->setOther($sample->getOther());
            $clone->setDescription($sample->getDescription());
            $clone->setComment($sample->getComment());
            $clone->setUser($user);

            $em->persist($clone);
            $em->flush();

            $this->addFlash('success', 'Sample "' . $clone->getName() . '" duplicated successfully!');
            return $this->redirectToRoute('page_samples');
        } catch (\Throwable $e) {
            $this->addFlash('error', 'An error occurred while duplicating the sample.');
            return $this->redirectToRoute('page_samples');
        }
    }

    #[Route('/samples/delete-multiple', name: 'delete_multiple_samples', methods: ['POST'])]
    public function deleteMultipleSamples(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to perform this action.');
            return $this->redirectToRoute('page_samples'); // ou vers le referer
        }
        // Récupère les IDs sélectionnés
        $ids = $request->request->all('selected_samples');

        if (!is_array($ids) || empty($ids)) {
            $this->addFlash('error', 'No sample selected.');
            return $this->redirectToRoute('page_samples');
        }

        // Trouve tous les samples concernés
        $samples = $em->getRepository(Sample::class)->findBy(['id' => $ids]);

        if (!$samples) {
            $this->addFlash('error', 'No samples found for deletion.');
            return $this->redirectToRoute('page_samples');
        }

        $detailsDeleted = [];
        foreach ($samples as $sample) {
            $detailsDeleted[] = sprintf('[ID: %d - Name: %s]', $sample->getId(), $sample->getName());
            $em->remove($sample);
        }

        if (!empty($detailsDeleted)) {
            $em->flush();
            $this->addFlash('success', sprintf(
                '%d sample(s) successfully deleted: %s',
                count($detailsDeleted),
                implode(', ', $detailsDeleted)
            ));
        }

        return $this->redirectToRoute('page_samples');
    }
}
