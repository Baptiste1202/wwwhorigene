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
use Elastica\Query\Exists;
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
use Symfony\Component\Validator\Constraints\DateTime as ConstraintsDateTime;

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
    #[IsGranted('ROLE_INTERN')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $cmd = 'docker exec -it claranet2-app-1 bash bin/console fos:elastica:populate';
        exec($cmd, $output, $returnCode);

        $user = $security->getUser(); 

        $strainFormView = null;

        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(StrainFormType::class);
            $strainFormView = $form->createView();
        }

        $elasticResponse = $this->elasticForm($request);
        $formElastic = $elasticResponse['form'];
        $strains = $elasticResponse['pagination'];

        if (!$strains) {
            $query = new Query();

            $boolQuery = new BoolQuery();
            $boolQuery->addMustNot(new Exists('dateArchive'));

            $query->setQuery($boolQuery);
            $query->setSort(['id' => ['order' => 'desc']]);

            $strains = $this->finder->find($query, 10000);
        }

        return $this->render('strain/main.html.twig', [
            'strainForm' => $strainFormView, 
            'form' => $formElastic->createView(),
            'user' => $user,
            'strains' => $strains,
            'is_bin' => false
        ]);

    }

    #[Route(path: 'strain/add', name: 'add_strain')]
    #[IsGranted('ROLE_SEARCH')]
    public function add(Request $request, EntityManagerInterface $em, Security $security, StrainIndexer $indexer): Response
    {
        try {
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
            $this->logger->error('Error creating strain', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            $this->addFlash('error', 'An unexpected error occurred. Please try again later.');

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
        try {
            if ($strain && !$this->isGranted('strain.is_creator', $strain)) {
                throw new AccessDeniedException('');
            }

            // 1) Essaye GET/POST
            $filter = $request->get('filter');

            // 2) Sinon, récupère depuis le Referer (URL de la liste)
            if ($filter === null || $filter === '') {
                $referer = $request->headers->get('referer'); // ex: /strains/page?...&filter=BERTRAND+Baptiste
                if ($referer) {
                    $queryString = parse_url($referer, PHP_URL_QUERY);
                    if ($queryString) {
                        parse_str($queryString, $params);
                        $filter = $params['filter'] ?? null;
                    }
                }
            }

            // 3) Valeur par défaut
            if ($filter === null || $filter === '') {
                $filter = 'all';
            }

            $strainForm = $this->createForm(StrainFormType::class, $strain);
            $strainForm->handleRequest($request);

            if ($strainForm->isSubmitted() && $strainForm->isValid()) {
                $em->flush();

                $this->addFlash('success', 'Strain ' . $strain->getNameStrain() . ' modified with succes !');

                // Renvoie en conservant le filtre
                return $this->redirectToRoute('page_strains', [
                    'highlight' => $strain->getId(),
                    'filter'    => $filter,
                ]);
            }

            return $this->render('strain/edit.html.twig', [
                'strainForm' => $strainForm->createView(),
                'strain'     => $strain,
                'is_update'  => true,
                'filter'     => $filter,
            ]);

        } catch (AccessDeniedException $e) {
            $this->addFlash('error', 'You do not have permission to edit this strain.');
            // même logique de fallback au besoin
            $filter = $request->get('filter', 'all');
            return $this->redirectToRoute('page_strains', ['filter' => $filter]);
        } catch (\Throwable $e) {
            if ($this->getParameter('kernel.debug')) { throw $e; }
            $this->addFlash('error', 'An error occurred while updating the strain. Please try again.');
            $filter = $request->get('filter', 'all');
            return $this->redirectToRoute('page_strains', ['filter' => $filter]);
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
            foreach ($strain->getPhenotype() as $transfo){
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

            return $this->redirectToRoute('page_strains_bin');
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', 'You do not have permission to delete this strain.');
            return $this->redirectToRoute('page_strains_bin');
        } catch (\Throwable $e) {
            dump($e); // n'affiche que si mode debug activé
            $this->addFlash('error', 'An error occurred while deleting the strain. Please try again.');

            return $this->redirectToRoute('page_strains_bin');
        }
    }

     #[Route('strain/archive/{id}', name: 'archive_strain')]
    #[IsGranted('ROLE_SEARCH')]
    public function archive(?Strain $strain, EntityManagerInterface $em, StrainIndexer $indexer): Response
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

            $strain->setDateArchive(new \DateTime());

        // Flush les changements des relations avant de supprimer l'entité
            $em->flush();

            $this->addFlash('success', 'Strain ' . $strain->getNameStrain() . ' delete with success !');
            sleep(1);

            return $this->redirectToRoute('page_strains');
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', 'You do not have permission to delete this strain.');
            return $this->redirectToRoute('page_strains');
        } catch (\Throwable $e) {
            $this->addFlash('error', 'An error occurred while deleting the strain. Please try again.');

            return $this->redirectToRoute('page_strains');
        }

    }

    #[Route('strain/restore/{id}', name: 'restore_strain')]
    #[IsGranted('ROLE_SEARCH')]
    public function restore(?Strain $strain, EntityManagerInterface $em, StrainIndexer $indexer): Response
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

            $strain->setDateArchive(null);

            $em->flush();

            $this->addFlash('success', 'Strain ' . $strain->getNameStrain() . ' restore with success !');
            sleep(1);

            return $this->redirectToRoute('page_bin_strains');
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', 'You do not have permission to restore this strain.');
            return $this->redirectToRoute('page_strains_bin');
        } catch (\Throwable $e) {
            $this->addFlash('error', 'An error occurred while restoring the strain. Please try again.');

            return $this->redirectToRoute('page_strains_bin');
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
            $clone->setInfoGenotype($strain->getInfoGenotype());
            $clone->setPrelevement($strain->getPrelevement());
            $clone->setCreatedBy($user);
            $clone->setDate($strain->getDate());
            
            // Relation vers le parent (on ne copie pas l'arbre entier)
            $clone->setParentStrain($strain->getParentStrain());

            // Phenotype (OneToMany)
            foreach ($strain->getPhenotype() as $transfo) {
                $newTransfo = clone $transfo;
                $newTransfo->setStrain($clone);
                $clone->addPhenotype($newTransfo);
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

            // ADD THIS: Exclude archived strains (those with dateArchive set)
            $boolQuery->addMustNot(new Exists('dateArchive'));

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

            // --- PhenotypeType (nested, path: phenotype) ---
            if ($data->phenotypeType && $data->phenotypeType->getId()) {
                $ptId = (int) $data->phenotypeType->getId();

                $this->logger->info('Add phenotypeType', ['phenotype_type_id' => $ptId]);

                $nestedBool = new \Elastica\Query\BoolQuery();

                // IDs ⇒ égalité stricte
                $nestedBool->addFilter(new \Elastica\Query\Term(['phenotype.phenotype_type_id' => $ptId]));

                $nested = new \Elastica\Query\Nested();
                $nested->setPath('phenotype'); // ← sans “s”, comme le mapping
                $nested->setQuery($nestedBool);

                $boolQuery->addFilter($nested);
            }

            // --- Phenotype measure (nested, indépendant de phenotypeType) ---
            if ($data->phenotypeMeasure) {
                $measure = (string) $data->phenotypeMeasure;
                $this->logger->info('Add phenotype measure', ['mesure' => $measure]);

                $nestedBool = new \Elastica\Query\BoolQuery();
                // champ indexé : phenotype.mesure (keyword)
                $nestedBool->addFilter(new \Elastica\Query\Term(['phenotype.mesure' => $measure]));

                $nested = new \Elastica\Query\Nested();
                $nested->setPath('phenotype'); // même path que pour phenotypeType
                $nested->setQuery($nestedBool);

                $boolQuery->addFilter($nested);
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

    #[Route('/strains/delete-multiple', name: 'delete_multiple_strains', methods: ['POST'])]
    #[IsGranted('ROLE_SEARCH')]
    public function deleteMultipleStrains(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        // 0) Action attendue depuis ton bouton
        $action = strtolower((string) $request->request->get('action', ''));
        if ($action !== 'delete') {
            $this->addFlash('error', 'Invalid bulk action.');
            return $this->redirectToRoute('page_strains');
        }

        // 1) IDs depuis name="selected_strain[]"
        $ids = $request->request->all('selected_strain');
        if (!is_array($ids) || empty($ids)) {
            $this->addFlash('error', 'No strain selected.');
            return $this->redirectToRoute('page_strains');
        }

        /** @var Strain[] $strains */
        $strains = $em->getRepository(Strain::class)->findBy(['id' => $ids]);
        if (!$strains) {
            $this->addFlash('error', 'No strains found for deletion.');
            return $this->redirectToRoute('page_strains');
        }

        $deleted = [];
        $blocked = [];

        // IDs demandés mais introuvables
        $foundIds = array_map(fn(Strain $s) => (int) $s->getId(), $strains);
        $missing  = array_diff(array_map('intval', $ids), $foundIds);
        foreach ($missing as $miss) {
            $blocked[] = sprintf('ID: %d (not found)', $miss);
        }

        // 2) TRAITER CHAQUE SOUCHE COMME TON DELETE UNITAIRE
        foreach ($strains as $strain) {
            // Sauvegarder l'info AVANT suppression
            $id   = (int) $strain->getId();
            $name = (string) ($strain->getNameStrain() ?? '');

            try {
                // droit auteur
                if (!$this->isGranted('strain.is_creator', $strain)) {
                    $blocked[] = sprintf('ID: %d - Name: "%s" (insufficient rights)', $id, $name !== '' ? $name : '--');
                    continue;
                }

                // a) supprimer relations
                foreach ($strain->getMethodSequencing() as $sequencing) { $em->remove($sequencing); }
                foreach ($strain->getPhenotype() as $transfo)           { $em->remove($transfo); }
                foreach ($strain->getDrugResistanceOnStrain() as $drug) { $em->remove($drug); }

                foreach ($strain->getCollec()->toArray() as $collec)    { $strain->removeCollec($collec); }
                foreach ($strain->getPlasmyd()->toArray() as $plasmyd)  { $strain->removePlasmyd($plasmyd); }
                foreach ($strain->getPublication()->toArray() as $publi){ $strain->removePublication($publi); }
                foreach ($strain->getProject()->toArray() as $project)  { $strain->removeProject($project); }

                // b) flush des ruptures
                $em->flush();

                // c) suppression de la souche
                $em->remove($strain);

                // d) flush final pour CETTE souche
                $em->flush();

                $deleted[] = sprintf('ID: %d - Name: "%s"', $id, $name !== '' ? $name : '--');

            } catch (\Throwable $e) {
                $blocked[] = sprintf(
                    'ID: %d - Name: "%s" (error: %s)',
                    $id,
                    $name !== '' ? $name : '--',
                    $e->getMessage()
                );
            }
        }

        // 3) petite pause comme ton delete unitaire
        sleep(1);

        // 4) messages
        if (!empty($deleted)) {
            $this->addFlash('success', sprintf('%d strain(s) successfully deleted: %s', count($deleted), implode(', ', $deleted)));
        }
        if (!empty($blocked)) {
            $this->addFlash('error', 'Some strains could not be deleted: ' . implode(', ', $blocked));
        }

        return $this->redirectToRoute('page_strains');
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
            'descGenotype' => $strain->getDescriptionGenotype() ?: null,
            'infoGenotype' => $strain->getInfoGenotype() ?: null,
            'sample' => $strain->getPrelevement() ? $strain->getPrelevement()->getId() : null,
        ]);
    }

}