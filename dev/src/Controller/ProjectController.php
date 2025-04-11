<?php 

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectFormType;
use App\Repository\ProjectRepository;
use App\Repository\ProjectRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;
use Elastica\Query;
use Elastica\Query\MatchAll;
use Elastica\Query\Match;
use Elastica\Query\Wildcard;
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

class ProjectController extends AbstractController
{
    public function __construct(
        #[Autowire(service: ProjectRepository::class)]
        private ProjectRepositoryInterface $projectRepository,
        private PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder
    ) {
    }

    #[Route(path: 'strains/page_projects', name: 'page_projects')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')){
            $projectAdd = $this->addForm($request, $em);  
        } 

        $allProjects = $this->projectRepository->findAll();

        // $query->setQuery($matchAll);
        // $query->setSort(['date' => ['order' => 'desc']]);
        // $query->setSize(30);

        // // Créer l'adaptateur de pagination
        // $paginatorAdapter = $this->finder->createPaginatorAdapter($query, ['project']);

        // // Paginer pour récupérer 30 résultats max
        $projects = $this->paginator->paginate($allProjects, $request->query->getInt('page', 1), 15);

        return $this->render('project/main.html.twig', [
            'projectForm' => $projectAdd, 
            'projects' => $projects
        ]);
    }

    #[Route(path: '/project', name: 'list_projects')]
    #[IsGranted('ROLE_INTERN')]
    public function showAll(): Response
    {
        $projects = $this->projectRepository->findAll();

        return $this->render('project/list.html.twig', ['projects' => $projects]);
    }

    #[Route(path: 'strains/projects/ajout', name: 'add_project')]
    #[IsGranted('ROLE_SEARCH')]
    public function addForm(Request $request, EntityManagerInterface $em): Form
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $project = new Project();

        //Create the form
        $projectForm = $this->createForm(ProjectFormType::class, $project);

        //
        $projectForm->handleRequest($request);

        if ($projectForm->isSubmitted() && $projectForm->isValid()) {

            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($project);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $projectForm;
        }
        return $projectForm;
    }

    #[Route(path: 'strains/projects/ajout/response', name: 'add_project_response')]
    #[IsGranted('ROLE_SEARCH')]
    public function addResponse(Request $request, EntityManagerInterface $em): Response
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $project = new Project();

        //Create the form
        $projectForm = $this->createForm(ProjectFormType::class, $project);

        //
        $projectForm->handleRequest($request);

        if ($projectForm->isSubmitted() && $projectForm->isValid()) {

            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($project);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $this->redirectToRoute('page_strains');
        }
        return $this->render('project/create.html.twig', compact('projectForm'));
    }

    #[Route('strains/project/edit/{id}', name: 'edit_project')]
    #[IsGranted('ROLE_SEARCH')]
    public function edit(
        Project $project,
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        /*
        if ($vehicule) {
            $this->denyAccessUnlessGranted('vehicule.is_creator', $vehicule);
        }
        */

        //Create the form
        $projectForm = $this->createForm(ProjectFormType::class, $project);

        //treat the request
        $projectForm->handleRequest($request);

        if ($projectForm->isSubmitted() && $projectForm->isValid()) {
            //generate the slug

            //stock data
            $em->persist($project);
            $em->flush();

            $this->addFlash('success', 'project ' . $project->getName() . ' modified with succes !');

            return $this->redirectToRoute('page_projects');
        }
        return $this->render('project/edit.html.twig', compact('projectForm'));
    }

    #[Route('strains/project/delete/{id}', name: 'delete_project')]
    #[IsGranted('ROLE_SEARCH')]
    public function delete(Request $request, Project $project, EntityManagerInterface $em): Response
    {
        if ($request->query->get('confirm') === 'yes') {
            $em->remove($project);
            $em->flush();

            $this->addFlash('success', 'Project ' . $project->getName() . ' deleted successfully!');
            return $this->redirectToRoute('page_projects');
        }

        $this->addFlash('warning', 'Are you sure you want to delete this Project ' . $project->getName(). ' ? (Be aware. This action cannot be undone !)');

        return $this->redirectToRoute('page_projects');
    }

}