<?php 

namespace App\Controller;

use App\Entity\Sample;
use App\Repository\SampleRepository;
use App\Repository\SampleRepositoryInterface;
use App\Form\SampleFormType;
use Doctrine\ORM\EntityManagerInterface;
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
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $role = $security->getUser()->getRoles(); 

        $responseAdd = $this->createForm(SampleFormType::class); 

        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')){
            $responseAdd = $this->add($request, $em, $security);  
        } 

        $samples = $this->sampleRepository->findAll();

        return $this->render('sample/main.html.twig', [
            'sampleForm' => $responseAdd, 
            'samples' => $samples
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
    public function add(Request $request, EntityManagerInterface $em): Form
    {
        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $sample = new sample();

        //Create the form
        $sampleForm = $this->createForm(SampleFormType::class, $sample);

        //
        $sampleForm->handleRequest($request);

        if ($sampleForm->isSubmitted() && $sampleForm->isValid()) {
            //generate the slug -- not done yet
            $newDate = $sample->getDate()->format('d/m/y'); 
            //get the user -- not done yet

            //stock data
            $em->persist($sample);
            $em->flush();

            // redirect
            return $sampleForm;
        }

        return $sampleForm;
    }

    #[Route('strains/sample/edit/{id}', name: 'edit_sample')]
    #[IsGranted('ROLE_SEARCH')]
    public function edit(
        Sample $sample,
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        /*
        if ($vehicule) {
            $this->denyAccessUnlessGranted('vehicule.is_creator', $vehicule);
        }
        */

        //Create the form
        $sampleForm = $this->createForm(SampleFormType::class, $sample);

        //treat the request
        $sampleForm->handleRequest($request);

        if ($sampleForm->isSubmitted() && $sampleForm->isValid()) {
            //generate the slug

            //stock data
            $em->persist($sample);
            $em->flush();

            $this->addFlash('success', 'sample ' . $sample->getName() . ' modified with succes !');

            return $this->redirectToRoute('page_samples');
        }
        return $this->render('sample/edit.html.twig', compact('sampleForm'));
    }

    #[Route('strains/sample/delete/{id}', name: 'delete_sample')]
    #[IsGranted('ROLE_SEARCH')]
    public function delete(Sample $sample, EntityManagerInterface $em): Response
    {
        /*
        if ($vehicule) {
            $this->denyAccessUnlessGranted('vehicule.is_creator', $vehicule);
        }
        */

        $em->remove($sample);
        $em->flush();

        $this->addFlash('success', 'sample ' . $sample->getName() . ' delete with success !');

        return $this->redirectToRoute('page_samples');
    }

    #[Route('samples/duplicate/{id}', name: 'duplicate_sample')]
    #[IsGranted('ROLE_SEARCH')]
    public function duplicate(
        Sample $sample,
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): Response {

        $user = $security->getUser(); 

        $sampleCloned = clone $sample; 

        $sampleCloned->setId(null);
        $sampleCloned->setDate(new \DateTime());
      
        $em->persist($sampleCloned);
        $em->flush();


        $this->addFlash('success', 'Strain ' . $sampleCloned->getName() . ' duplicate with succes !');

        return $this->redirectToRoute('page_samples');

    }

}