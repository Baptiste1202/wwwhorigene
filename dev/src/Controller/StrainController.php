<?php 

namespace App\Controller;

use App\Entity\DrugResistanceOnStrain;
use App\Entity\MethodSequencing;
use App\Entity\Phenotype;
use Psr\Log\LoggerInterface;
use App\Entity\Strain;
use App\Form\ParentFormType;
use App\Form\SearchFormType;
use App\Repository\StrainRepositoryInterface;
use App\Form\StrainFormType;
use App\Service\FileCloner;
use App\Service\S3FileCloner;
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
        //$cmd = 'docker exec -it claranet2-app-1 bash bin/console fos:elastica:populate';
        //exec($cmd, $output, $returnCode);

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

            $strainForm = $this->createForm(StrainFormType::class, $strain, [
                'is_update' => true,
            ]);
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
    public function delete(?Strain $strain, EntityManagerInterface $em): Response
    {
        try {
            // Arrêter ici volontairement

            if (!$strain) {
                $this->addFlash('error', 'Strain not found.');
                return $this->redirectToRoute('page_strains');
            }
            
            if (!$this->isGranted('strain.is_creator', $strain)) {
                throw new AccessDeniedException('');
            }

            // Suppression des relations ManyToMany
            $strain->getCollec()->clear();
            $strain->getPlasmyd()->clear();
            $strain->getPublication()->clear();
            $strain->getProject()->clear();

            // La suppression des OneToMany se fera automatiquement avec cascade
            $em->remove($strain);
            $em->flush();

            $this->addFlash('success', 'Strain ' . $strain->getNameStrain() . ' deleted with success!');
            sleep(1);
            return $this->redirectToRoute('page_strains_bin');
            
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', 'You do not have permission to delete this strain.');
            return $this->redirectToRoute('page_strains_bin');
        } catch (\Throwable $e) {
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
            $this->addFlash('error', 'Strain not found.');
            return $this->redirectToRoute('page_strains');
            }

            if ($strain) {
                if (!$this->isGranted('strain.is_creator', $strain)) {
                    throw new AccessDeniedException('');
                }
            }

            $strain->setDateArchive(new \DateTime());

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
            $this->addFlash('error', 'Strain not found.');
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

            return $this->redirectToRoute('page_strains_bin');
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
        StrainIndexer $indexer,
        S3FileCloner $fileCloner
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
            $clone->setDate(new \DateTime());
            
            // Relation vers le parent
            $clone->setParentStrain($strain->getParentStrain());
            
            // Phenotype (OneToMany) avec duplication des fichiers S3
            foreach ($strain->getPhenotype() as $phenotype) {
                $newPhenotype = new Phenotype();
                $newPhenotype->setTechnique($phenotype->getTechnique());
                $newPhenotype->setDate($phenotype->getDate());
                $newPhenotype->setPhenotypeType($phenotype->getPhenotypeType());
                $newPhenotype->setMesure($phenotype->getMesure());
                $newPhenotype->setDescription($phenotype->getDescription());
                $newPhenotype->setComment($phenotype->getComment());
                $newPhenotype->setStrain($clone);
                
                // Dupliquer le fichier sur S3
                if ($phenotype->getFileName()) {
                    $newFileName = $fileCloner->cloneFile(
                        $phenotype->getFileName(),
                        '/docs/phenotype'
                    );
                    if ($newFileName) {
                        $newPhenotype->setFileName($newFileName);
                    }
                }
                
                $clone->addPhenotype($newPhenotype);
            }
            
            // DrugResistanceOnStrain (OneToMany) avec duplication des fichiers S3
            foreach ($strain->getDrugResistanceOnStrain() as $drug) {
                $newDrug = new DrugResistanceOnStrain();
                $newDrug->setDrugResistance($drug->getDrugResistance());
                $newDrug->setConcentration($drug->getConcentration());
                $newDrug->setDescription($drug->getDescription());
                $newDrug->setComment($drug->getComment());
                $newDrug->setResistant($drug->isResistant());
                $newDrug->setDate($drug->getDate());
                $newDrug->setStrain($clone);
                
                // Dupliquer le fichier sur S3
                if ($drug->getNameFile()) {
                    $newFileName = $fileCloner->cloneFile(
                        $drug->getNameFile(),
                        '/docs/drugs' // URI prefix du mapping
                    );
                    if ($newFileName) {
                        $newDrug->setNameFile($newFileName);
                    }
                }
                
                $clone->addDrugResistanceOnStrain($newDrug);
            }
            
            // MethodSequencing (OneToMany) avec duplication des fichiers S3
            foreach ($strain->getMethodSequencing() as $method) {
                $newMethod = new MethodSequencing();
                $newMethod->setName($method->getName());
                $newMethod->setDate($method->getDate());
                $newMethod->setTypeFile($method->getTypeFile());
                $newMethod->setDescription($method->getDescription());
                $newMethod->setComment($method->getComment());
                $newMethod->setStrain($clone);
                
                // Dupliquer le fichier sur S3
                if ($method->getNameFile()) {
                    $newFileName = $fileCloner->cloneFile(
                        $method->getNameFile(),
                        '/docs/sequencing' // URI prefix du mapping
                    );
                    if ($newFileName) {
                        $newMethod->setNameFile($newFileName);
                    }
                }
                
                $clone->addMethodSequencing($newMethod);
            }
            
            // Plasmyd (ManyToMany)
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
            $indexer->index($clone);
            
            $this->addFlash('success', 'Strain ' . $clone->getNameStrain() . ' duplicated with success!');
            sleep(1);
            
            return $this->redirectToRoute('page_strains');
            
        } catch (\Throwable $e) {
            if ($this->getParameter('kernel.debug')) {
                throw $e;
            }
            $this->logger?->error('Erreur lors de la duplication de la souche', [
                'strain_id' => $strain->getId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->addFlash('error', 'An error occurred while duplicating the strain. Please try again.');
            return $this->redirectToRoute('page_strains');
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
            //if ($this->getParameter('kernel.debug')) dump($form->getData());
            $page = $request->query->getInt('page',1);
            $this->logger->info('Page requested', ['page' => $page]);

            $boolQuery = new BoolQuery();

            // ADD THIS: Exclude archived strains (those with dateArchive set)
            $boolQuery->addMustNot(new Exists('dateArchive'));
            
            if (!empty($data->id)) {
                $this->logger->info('Add ID filter', ['id' => $data->id]);
                $boolQuery->addFilter(new \Elastica\Query\Term(['id' => (int) $data->id]));
            }

            if (!empty($data->query)) {
                $term = trim($data->query);

                $qs = new \Elastica\Query\QueryString();
                $qs->setQuery('*' . $term . '*');    // contient
                $qs->setFields(['nameStrain']);      // champ visé
                $qs->setAnalyzeWildcard(true);       // insensible à la casse via analyzer
                $boolQuery->addFilter($qs);
            }


            if ($data->plasmyd){
                $this->logger->info('Add plasmyd', ['plasmyd_id' => $data->plasmyd->getId()]);
                $boolQuery->addFilter(new MatchQuery('plasmyd.id', $data->plasmyd->getId()));
            }

            if ($data->sequencing) {
                $this->logger->info('Add sequencing', ['sequencing' => $data->sequencing]);
                $boolQuery->addFilter(new MatchQuery('methodSequencing.typeFile', $data->sequencing));
            }

            // On entre ici si on a soit un drug, soit un état résistant/sensible
            if ($data->drug || $data->resistant !== null) {
                $nestedBool = new BoolQuery();

                // 1) Filtre par antibiotique (optionnel)
                if ($data->drug) {
                    $this->logger->info('Add drug', ['drug_id' => $data->drug->getId()]);
                    $nestedBool->addFilter(
                        new MatchQuery('drugResistanceOnStrain.drugResistance.id', $data->drug->getId())
                    );
                }

                // 2) Filtre par état résistant / sensible (optionnel)
                if ($data->resistant !== null) {
                    $this->logger->info('Add resistant', ['resistant' => $data->resistant]);
                    $nestedBool->addFilter(
                        new MatchQuery('drugResistanceOnStrain.resistant', $data->resistant)
                    );
                }

                // 3) Nested query sur drugResistanceOnStrain
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

            if (!empty($data->specie)) {
                $term = trim($data->specie);

                $qs = new \Elastica\Query\QueryString();
                $qs->setQuery('*' . $term . '*');   // contient
                $qs->setFields(['specie']);         // champ visé
                $qs->setAnalyzeWildcard(true);      // => insensible à la casse via analyzer
                $boolQuery->addFilter($qs);
            }

            if (!empty($data->gender)) {
                $term = trim($data->gender);

                $qs = new \Elastica\Query\QueryString();
                $qs->setQuery('*' . $term . '*');   // contient
                $qs->setFields(['gender']);         // champ ciblé
                $qs->setAnalyzeWildcard(true);      // ignore la casse via l'analyzer
                $boolQuery->addFilter($qs);
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

    #[Route('/strains/archive-multiple', name: 'archive_multiple_strains', methods: ['POST'])]
    #[IsGranted('ROLE_SEARCH')]
    public function archiveMultipleStrains(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        // 0) Action attendue depuis le bouton
        $action = strtolower((string) $request->request->get('action', ''));
        if ($action !== 'archive') {
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
            $this->addFlash('error', 'No strains found for archiving.');
            return $this->redirectToRoute('page_strains');
        }

        $archived = [];
        $blocked  = [];

        // IDs demandés mais introuvables
        $foundIds = array_map(fn(Strain $s) => (int) $s->getId(), $strains);
        $missing  = array_diff(array_map('intval', $ids), $foundIds);
        foreach ($missing as $miss) {
            $blocked[] = sprintf('ID: %d (not found)', $miss);
        }

        // 2) Traiter chaque souche comme ton delete unitaire, mais en "archive"
        foreach ($strains as $strain) {
            $id   = (int) $strain->getId();
            $name = (string) ($strain->getNameStrain() ?? '');

            try {
                // droit auteur
                if (!$this->isGranted('strain.is_creator', $strain)) {
                    $blocked[] = sprintf('ID: %d - Name: "%s" (insufficient rights)', $id, $name !== '' ? $name : '--');
                    continue;
                }

                // Poser la date d’archive
                $strain->setDateArchive(new \DateTime());

                // Flush par entité pour isoler les erreurs
                $em->flush();

                $archived[] = sprintf('ID: %d - Name: "%s"', $id, $name !== '' ? $name : '--');

            } catch (\Throwable $e) {
                $blocked[] = sprintf(
                    'ID: %d - Name: "%s" (error: %s)',
                    $id,
                    $name !== '' ? $name : '--',
                    $e->getMessage()
                );
                // Annuler les changements en erreur pour cette entité
                $em->clear(); // optionnel si tu préfères isoler, sinon commente-le
            }
        }

        // Résumés
        if (!empty($archived)) {
            $this->addFlash('success', sprintf(
                'Archived %d strain(s): %s',
                count($archived),
                implode(', ', array_slice($archived, 0, 10)) . (count($archived) > 10 ? '…' : '')
            ));
        }
        if (!empty($blocked)) {
            $this->addFlash('error', sprintf(
                'Skipped %d strain(s): %s',
                count($blocked),
                implode(', ', array_slice($blocked, 0, 10)) . (count($blocked) > 10 ? '…' : '')
            ));
        }
        
        usleep(800000); // 0.5s

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