<?php 

namespace App\Controller;

use Psr\Log\LoggerInterface;
use App\Entity\Strain;
use App\Form\ParentFormType;
use App\Form\SearchFormType;
use App\Repository\StrainRepositoryInterface;
use App\Form\StrainFormType;
use App\Service\StrainIndexer;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;
use Elastica\Query;
use Elastica\Query\MatchAll;
use Elastica\Query\Nested;
use Elastica\Query\Wildcard;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StrainController extends AbstractController
{
    public function __construct(
        private StrainRepositoryInterface $strainRepository,
        private PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder,
        private LoggerInterface $logger 
    ) {
        $this->paginator = $paginator; 
         $this->logger = $logger; 
    }

    #[Route(path: 'strains/page', name: 'page_strains')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $cmd = 'docker exec -it claranet2-app-1 bash bin/console fos:elastica:populate';
        exec($cmd, $output, $returnCode);

        $user = $security->getUser(); 

        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')){
            $form = $this->createForm(StrainFormType::class);
        }

        $elasticResponse = $this->elasticForm($request);
        $formElastic = $elasticResponse['form'];
        $strains = $elasticResponse['pagination'];

        if (!$strains) {
            $query = new Query();
            $matchAll = new MatchAll();

            $query->setQuery($matchAll);
            $query->setSort(['id' => ['order' => 'desc']]);

            $strains = $this->finder->find($query, 10000);
        }

        return $this->render('strain/main.html.twig', [
            'strainForm' => $form->createView(), 
            'form' => $formElastic->createView(),
            'user' => $user,
            'strains' => $strains
        ]);

    }

    #[Route(path: 'strain/add', name: 'add_strain')]
    #[IsGranted('ROLE_SEARCH')]
    public function add(Request $request, EntityManagerInterface $em, Security $security, StrainIndexer $indexer): Response
    {
        try {

            
            // $this->denyAccessUnlessGranted('ROLE_RENTER');

            //Create a new vehicule
            $strain = new Strain();

            $strain->setDate(new \DateTime());
            
            //Create the form
            $strainForm = $this->createForm(StrainFormType::class, $strain);

            //
            $strainForm->handleRequest($request);

            if ($strainForm->isSubmitted() && $strainForm->isValid()) {

                //get the user
                $user = $security->getUser(); 
                $strain->setCreatedBy($user);

                $strain->setDate(new \DateTime());

                foreach($strain->getMethodSequencing() as $sequencing){
                    $file = $sequencing->getFile();
                    if ($file !== null) {
                        $filename = $file->getClientOriginalName();
                        $extension = pathinfo($filename, PATHINFO_EXTENSION);
                        $sequencing->setTypeFile($extension);
                    }
                }

                //stock data
                $em->persist($strain);
                $em->flush();

                $indexer->index($strain);
                sleep(1);

                $this->addFlash('success', 
                    'Strain ' . 
                    $strain->getNameStrain().
                    ' (ID: '. $strain->getId() . ') '.
                    ' created with succes !'
                );

                // redirect
                return $this->redirectToRoute('page_strains');
            }

            return $this->render('strain/main.html.twig', [
                'strainForm' => $strainForm,
            ]);
        }
        catch (\Throwable $e) {
            $this->addFlash('error', 'An error occurred while creating the strain. Please try again.');

            return $this->redirectToRoute('page_strains');
        }
    }

    #[Route('strain/edit/{id}', name: 'edit_strain')]
    #[IsGranted('ROLE_SEARCH')]
    public function edit(
        Strain $strain,
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        try{
            if ($strain) {
                if (!$this->isGranted('strain.is_creator', $strain)) {
                    throw new AccessDeniedException('');
                }
            }
            //Create the form
            $strainForm = $this->createForm(StrainFormType::class, $strain);

            //treat the request
            $strainForm->handleRequest($request);

            if ($strainForm->isSubmitted() && $strainForm->isValid()) {

                $em->flush();

                $this->addFlash('success', 'Strain ' . $strain->getNameStrain() . ' modified with succes !');

                return $this->redirectToRoute('page_strains');
            }

            return $this->render('strain/edit.html.twig', [
                'strainForm' => $strainForm->createView(),
                'strain' => $strain, 
                'is_update' => true,
            ]);
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', 'You do not have permission to edit this strain.');
            return $this->redirectToRoute('page_strains');
        } catch (\Throwable $e) {
            $this->addFlash('error', 'An error occurred while updating the strain. Please try again.');
            return $this->redirectToRoute('page_strains');
        }

    }

    #[Route('strain/delete/{id}', name: 'delete_strain')]
    #[IsGranted('ROLE_SEARCH')]
    public function delete(?Strain $strain, EntityManagerInterface $em, StrainIndexer $indexer): Response
    {
        try {
            if (!$strain) {
            $this->addFlash('error', 'Souche introuvable.');
            return $this->redirectToRoute('page_strains');
            }

            if ($strain) {
                if (!$this->isGranted('strain.is_creator', $strain)) {
                    throw new AccessDeniedException('');
                }
            }

            foreach ($strain->getMethodSequencing() as $sequencing){
                $em->remove($sequencing);
            }
            foreach ($strain->getTransformability() as $transfo){
                $em->remove($transfo);
            }
            foreach($strain->getDrugResistanceOnStrain() as $drug){
                $em->remove($drug);
            }

            $collecs = $strain->getCollec()->toArray();
            foreach($collecs as $collec) {
                $strain->removeCollec($collec);
            }

            $plasmyds = $strain->getPlasmyd()->toArray();
            foreach($plasmyds as $plasmyd) {
                $strain->removePlasmyd($plasmyd);
            }

            $publis = $strain->getPublication()->toArray();
            foreach($publis as $publi) {
                $strain->removePublication($publi);
            }

            $projects = $strain->getProject()->toArray();
            foreach($projects as $project) {
                $strain->removeProject($project);
            }

            // Flush les changements des relations avant de supprimer l'entité
            $em->flush();

            $em->remove($strain);

            $em->flush();

            $this->addFlash('success', 'Strain ' . $strain->getNameStrain() . ' delete with success !');
            sleep(1);

            return $this->redirectToRoute('page_strains');
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', 'You do not have permission to delete this strain.');
            return $this->redirectToRoute('page_strains');
        } catch (\Throwable $e) {
            dump($e); // n'affiche que si mode debug activé
            $this->addFlash('error', 'An error occurred while deleting the strain. Please try again.');

            return $this->redirectToRoute('page_strains');
        }
    }

    #[Route('strains/duplicate/{id}', name: 'duplicate_strain')]
    #[IsGranted('ROLE_SEARCH')]
    public function duplicate(
        Strain $strain,
        EntityManagerInterface $em,
        Security $security,
        StrainIndexer $indexer
    ): Response {

        try {
            $user = $security->getUser(); 

            $clone = new Strain();

            // Champs simples
            $clone->setNameStrain($strain->getNameStrain());
            $clone->setSpecie($strain->getSpecie());
            $clone->setGender($strain->getGender());
            $clone->setComment($strain->getComment());
            $clone->setDescription($strain->getDescription());
            $clone->setGenotype($strain->getGenotype());
            $clone->setDescriptionGenotype($strain->getDescriptionGenotype());
            //$clone->setDepot($$strain->getDepot());
            $clone->setPrelevement($strain->getPrelevement());
            $clone->setCreatedBy($user);
            $clone->setDate($strain->getDate());
            
            // Relation vers le parent (on ne copie pas l'arbre entier)
            $clone->setParentStrain($strain->getParentStrain());

            // Transformability (OneToMany)
            foreach ($strain->getTransformability() as $transfo) {
                $newTransfo = clone $transfo;
                $newTransfo->setStrain($clone);
                $clone->addTransformability($newTransfo);
            }

            // DrugResistanceOnStrain (OneToMany)
            foreach ($strain->getDrugResistanceOnStrain() as $drug) {
                $newDrug = clone $drug;
                $newDrug->setStrain($clone);
                $clone->addDrugResistanceOnStrain($newDrug);
            }

            // MethodSequencing (OneToMany)
            foreach ($strain->getMethodSequencing() as $method) {
                $newMethod = clone $method;
                $newMethod->setStrain($clone);
                $clone->addMethodSequencing($newMethod);
            }

            // Storage (OneToMany)
            foreach ($strain->getStorage() as $storage) {
                $newStorage = clone $storage;
                $newStorage->setStrain($clone);
                $clone->getStorage()->add($newStorage);
            }

            // Plasmyd (ManyToMany) – liaison seulement, pas de clonage
            foreach ($strain->getPlasmyd() as $plasmyd) {
                $clone->addPlasmyd($plasmyd);
            }

            // Publication (ManyToMany)
            foreach ($strain->getPublication() as $pub) {
                $clone->addPublication($pub);
            }

            // Project (ManyToMany)
            foreach ($strain->getProject() as $proj) {
                $clone->addProject($proj);
            }

            // Collec (ManyToMany)
            foreach ($strain->getCollec() as $collec) {
                $clone->addCollec($collec);
            }
            
            $em->persist($clone);
            $em->flush();

            $indexer->index($strain);

            $this->addFlash('success', 'Strain ' . $clone->getNameStrain() . ' duplicate with succes !');

            sleep(1);

            return $this->redirectToRoute('page_strains');
        } catch (\Throwable $e) {
            $this->addFlash('error', 'An error occurred while duplicating the strain. Please try again.');

            return $this->redirectToRoute('edit_strain');
        }
    }

    #[Route('/strains/search', name: 'strain_search')]
    public function search(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ParentFormType::class);
        $form->handleRequest($request);

        $results = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $strainId = $data['id'];
            $searchType = $data['searchType'];

            // Rechercher la Strain par son ID
            $strain = $em->getRepository(Strain::class)->find($strainId);

            if (!$strain) {
                $this->addFlash('error', 'Strain not found');
            } else {
                if ($searchType === 'parents') { // search for parents
                    $results = [];
                    $current = $strain->getParentStrain();
                    while ($current) {
                        $results[] = $current;
                        $current = $current->getParentStrain();
                    }
                } elseif ($searchType === 'children') { // search for children
                    $results = $strain->getChildrenStrain();
                }
            }
        }

        return $this->render('strain/search.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
        ]);
    }

    #[Route('/search', name: 'app_search')]
    #[IsGranted('ROLE_INTERN')]
    public function elasticForm(Request $request): array
    {
        $form = $this->createForm(SearchFormType::class); 
        $form->handleRequest($request); 

        $this->logger->info('Form initialized for elastic search');

        $pagination = [];
        if ($form->isSubmitted() && $form->isValid()){
            /** @var SearchModel $data */
            $data = $form->getData(); 
            $this->logger->info('Form submitted', ['data' => (array) $data]);
            $page = $request->query->getInt('page',1);
            $this->logger->info('Page requested', ['page' => $page]);
            
            $boolQuery = new BoolQuery();

            if ($data->query){
                $this->logger->info('Add query', ['query' => $data->query]);
                $boolQuery->addMust(new Wildcard('nameStrain', '*'.$data->query.'*')); 
            }

            if ($data->plasmyd){
                $this->logger->info('Add plasmyd', ['plasmyd_id' => $data->plasmyd->getId()]);
                $boolQuery->addFilter(new MatchQuery('plasmyd.id', $data->plasmyd->getId()));
            }

            if ($data->sequencing) {
                $this->logger->info('Add sequencing', ['sequencing' => $data->sequencing]);
                $boolQuery->addFilter(new MatchQuery('methodSequencing.typeFile', $data->sequencing));
            }

            if ($data->drug ){
                $this->logger->info('Add drug', ['drug_id' => $data->drug->getId()]);
                $nestedBool = new BoolQuery();
                
                if ($data->drug) {
                    $nestedBool->addFilter(new MatchQuery('drugResistanceOnStrain.drugResistance.id', $data->drug->getId()));
                }
            
                if ($data->resistant !== null) {
                    $this->logger->info('Add resistant', ['resistant' => $data->resistant]);
                    $nestedBool->addFilter(new MatchQuery('drugResistanceOnStrain.resistant', $data->resistant));
                }

                $nested = new Nested();
                $nested->setPath('drugResistanceOnStrain');
                $nested->setQuery($nestedBool);

                $boolQuery->addFilter($nested);
            }

            if ($data->genotype){
                $this->logger->info('Add genotype', ['genotype_id' => $data->genotype->getId()]);
                $boolQuery->addFilter(new MatchQuery('genotype.id', $data->genotype->getId()));
            }

            if ($data->project){
                $this->logger->info('Add project', ['project_id' => $data->project->getId()]);
                $boolQuery->addFilter(new MatchQuery('project.id', $data->project->getId()));
            }

            if ($data->sample){
                $this->logger->info('Add sample', ['sample_id' => $data->sample->getId()]);
                $boolQuery->addFilter(new MatchQuery('sample.id', $data->sample->getId()));
            }

            if ($data->user){
                $this->logger->info('Add user', ['user_id' => $data->user->getId()]);
                $boolQuery->addFilter(new MatchQuery('createdBy.id', $data->user->getId()));
            }

            if ($data->specie) {
                $this->logger->info('Add specie', ['specie' => $data->specie]);
                $boolQuery->addFilter(new Wildcard('specie', '*'.$data->specie.'*'));
            }

            if ($data->gender) {
                $this->logger->info('Add gender', ['gender' => $data->gender]);
                $boolQuery->addFilter(new Wildcard('gender', '*'.$data->gender.'*'));
            }

            $query = new Query($boolQuery);
            $query->setSort(['id' => ['order' => 'desc']]);

            $this->logger->info('Final ES Query ready');

            // Crée le paginator à partir de la query complète
            $results = $this->finder->createPaginatorAdapter($query);
            $this->logger->info('Elastic finder returned', [
                'class' => is_object($results) ? get_class($results) : gettype($results)
            ]);
            $pagination = $this->paginator->paginate($results, $page, 1000);

            // Nombre d’éléments renvoyés :
            if (is_countable($pagination)) {
                $this->logger->info('Pagination result count', ['count' => count($pagination)]);
            } else {
                $this->logger->info('Pagination not countable');
            }
        } else {
            $this->logger->info('Form not submitted or invalid');
        }

        return [
            'form' => $form,
            'pagination' => $pagination ?? []
        ];
    }

    #[Route('/api/strain/{id}', name: 'api_strain_get', methods: ['GET'])]
    #[IsGranted('ROLE_SEARCH')]
    public function getStrain(Strain $strain): JsonResponse
    {
        return $this->json([
            'name' => $strain->getNameStrain() ?: null,
            'specie' => $strain->getSpecie() ?: null,
            'gender' => $strain->getGender() ?: null,
            'comment' => $strain->getComment() ?: null,
            'description' => $strain->getDescription() ?: null,
            'genotype' => $strain->getGenotype() ? $strain->getGenotype()->getId() : null,
            // 'sample' => $strain->getPrelevement() ? $strain->getPrelevement()->getId() : null,
        ]);
    }

}