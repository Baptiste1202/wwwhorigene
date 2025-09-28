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
    #[IsGranted('ROLE_INTERN')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {   
        $projectAdd = $this->createForm(ProjectFormType::class);

        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')) {
            $projectAdd = $this->addForm($request, $em);
        }

       $allProjects = $this->projectRepository->findBy([], ['id' => 'DESC'], 10000);

        return $this->render('project/main.html.twig', [
            'projectForm' => $projectAdd,
            'projects' => $allProjects
        ]);
    }

    #[Route(path: 'strains/projects/ajout', name: 'add_project')]
    #[IsGranted('ROLE_SEARCH')]
    public function addForm(Request $request, EntityManagerInterface $em): Form
    {
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
    public function edit(
        Project $project,
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): Response
    {
        if (!$security->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to edit a project.');
            return $this->redirectToRoute('page_projects');
        }

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

    #[Route('projects/duplicate/{id}', name: 'duplicate_project')]
    public function duplicateProject(Project $project, EntityManagerInterface $em, Security $security): Response
    {
        try {
            if (!$security->isGranted('ROLE_SEARCH')) {
                $this->addFlash('error', 'You do not have permission to duplicate a project.');
                return $this->redirectToRoute('page_projects');
            }

            // Créer une nouvelle instance de Project (la copie)
            $clone = new Project();

            // Copier les champs simples de l'entité originale
            $clone->setName($project->getName());
            $clone->setDescription($project->getDescription());
            $clone->setComment($project->getComment());

            // Enregistrement en base de la copie
            $em->persist($clone);
            $em->flush();

            // Message flash
            $this->addFlash('success', 'Project "' . $clone->getName() . '" duplicated successfully!');

            return $this->redirectToRoute('page_projects');

        } catch (\Throwable $e) {
            $this->addFlash('error', 'Error occurred while duplicating the project.');
            return $this->redirectToRoute('page_projects');
        }
    }

   #[Route('strains/project/delete/{id}', name: 'delete_project')]
    public function delete(Project $project, EntityManagerInterface $em, Security $security): Response
    {
        if (!$security->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to delete a project.');
            return $this->redirectToRoute('page_projects');
        }

        // Get IDs of strains associated with the project
        $strainIds = $project->getStrain()->map(fn($strain) => $strain->getId())->toArray();

        if (count($strainIds) > 0) {
            $this->addFlash(
                'error',
                sprintf(
                    'Cannot delete Project (ID: %d, Name: "%s") because it is associated with the following strain IDs: %s.',
                    $project->getId(),
                    $project->getName(),
                    implode(', ', $strainIds)
                )
            );
        } else {
            // Directly delete
            $em->remove($project);
            $em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    'Project (ID: %d, Name: "%s") has been successfully deleted!',
                    $project->getId(),
                    $project->getName()
                )
            );
        }

        return $this->redirectToRoute('page_projects');
    }

    #[Route('/projects/delete-multiple', name: 'delete_multiple_projects', methods: ['POST'])]
    public function deleteMultiple(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_SEARCH')) {
            $this->addFlash('error', 'You do not have permission to perform this action.');
            return $this->redirectToRoute('page_projects'); // ou vers le referer
        }
        // Récupérer les ids sélectionnés via la requête POST
        $ids = $request->request->all('selected_projects');

        // Vérifier que ce soit un tableau non vide
        if (!is_array($ids) || empty($ids)) {
            $this->addFlash('error', 'Aucun projet sélectionné.');
            return $this->redirectToRoute('page_projects');
        }

        // Chercher tous les projets correspondants
        $projects = $em->getRepository(Project::class)->findBy(['id' => $ids]);

        if (!$projects) {
            $this->addFlash('error', 'No project found for deletion.');
            return $this->redirectToRoute('page_projects');
        }

        $detailsDeleted = [];
        $detailsBlocked = [];

        foreach ($projects as $project) {
            $strainIds = $project->getStrain()->map(fn($strain) => $strain->getId())->toArray();

            if (!empty($strainIds)) {
                $detailsBlocked[] = sprintf(
                    '[ID: %d - Name: "%s" → Linked Strains: %s]',
                    $project->getId(),
                    $project->getName(),
                    implode(', ', $strainIds)
                );
                continue;
            }

            // Préparer la suppression et stocker les détails pour message
            $detailsDeleted[] = sprintf('[ID: %d - Name: "%s"]', $project->getId(), $project->getName());
            $em->remove($project);
        }

        // Valider la suppression en base (pour les projets sans souches)
        if (!empty($detailsDeleted)) {
            $em->flush();
            $this->addFlash('success', sprintf(
                '%d project(s) successfully deleted: %s',
                count($detailsDeleted),
                implode(', ', $detailsDeleted)
            ));
        }

        // Message d’erreur pour les projets bloqués
        if (!empty($detailsBlocked)) {
            $this->addFlash(
                'error',
                'Unable to delete some projects because they are linked to strains: ' . implode(', ', $detailsBlocked)
            );
        }

        // Rediriger vers la page des projets
        return $this->redirectToRoute('page_projects');
    }


}