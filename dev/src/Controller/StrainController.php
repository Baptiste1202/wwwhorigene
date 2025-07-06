<?php 

namespace App\Controller;

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
use Elastica\Query\MultiMatch;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StrainController extends AbstractController
{
    public function __construct(
        private StrainRepositoryInterface $strainRepository,
        private PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder,
    ) {
        $this->paginator = $paginator; 
    }

    #[Route(path: 'strains/page', name: 'page_strains')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        // Récupérer le paramètre 'limit' dans l'URL, sinon valeur par défaut 10
        $limit = $request->query->getInt('limit', 10);

        // --- ATTENTION : COMMANDE DOCKER À RETIRER EN PRODUCTION ---
        // Exécuter une commande `docker exec` à chaque chargement de page est TRÈS INEFFICAZ et DANGEREUX en production.
        // C'est une opération d'indexation qui doit être déclenchée manuellement ou via un système de queue/messagerie.
        // Je la commente ici pour éviter des problèmes.
        // $cmd = 'docker exec -it claranet2-app-1 bash bin/console fos:elastica:populate';
        // exec($cmd, $output, $returnCode);

        // Création du formulaire d'ajout (StrainFormType) si l'utilisateur a les rôles requis.
        $strainForm = null; // Initialise à null pour gérer le cas où l'utilisateur n'a pas les droits.
        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')){
            $strainForm = $this->createForm(StrainFormType::class);
        }

        // Création du formulaire de recherche (SearchFormType) pour l'affichage initial de la page.
        // Ce formulaire sera ensuite utilisé par JavaScript/DataTables pour les requêtes AJAX vers /strains/data.
        $formElastic = $this->createForm(SearchFormType::class); // Assurez-vous que SearchFormType existe et est correctement configuré.

        // Initialisation de la pagination : Par défaut, on affiche toutes les souches.
        // La vraie recherche/filtration sera gérée par la méthode `getStrainsData` via AJAX (DataTables).
        $query = new Query();
        $matchAll = new MatchAll();

        $query->setQuery($matchAll);
        $query->setSort(['id' => ['order' => 'desc']]);

        // Créer l'adaptateur de pagination
        $paginatorAdapter = $this->finder->createPaginatorAdapter($query);

        // Paginer en utilisant la limite choisie par l'utilisateur
        $strains = $this->paginator->paginate(
            $paginatorAdapter,
            $request->query->getInt('page', 1),
            $limit
        );

        return $this->render('strain/main.html.twig', [
            'strainForm' => $strainForm ? $strainForm->createView() : null, // Passer le formulaire d'ajout s'il est créé
            'form' => $formElastic->createView(), // Passer le formulaire de recherche
            'strains' => $strains, // Les souches paginées (par défaut ou résultats d'une recherche initiale si implémentée ici)
            'limit' => $limit, // on peut passer la limite à la vue si besoin
        ]);
    }

    #[Route(path: 'strains/add', name: 'add_strain')]
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
            $this->addFlash('error', 'Une erreur est survenue lors de la création de la souche. Veuillez réessayer.');

            return $this->redirectToRoute('page_strains');
        }
    }

    #[Route('vehicules/edit/{id}', name: 'edit_strain')]
    #[IsGranted('ROLE_SEARCH')]
    public function edit(
        Strain $strain,
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        try{
            if ($strain) {
                $this->denyAccessUnlessGranted('strain.is_creator', $strain);
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
        } catch (\Throwable $e) {

            dd($e);
            $this->addFlash('error', 'Une erreur est survenue lors de la modification de la souche. Veuillez réessayer.');

            return $this->redirectToRoute('page_strains');
        }
    }

    #[Route('strains/delete/{id}', name: 'delete_strain')]
    #[IsGranted('ROLE_SEARCH')]
    public function delete(?Strain $strain, EntityManagerInterface $em, StrainIndexer $indexer): Response
    {
        try {
            if (!$strain) {
            $this->addFlash('error', 'Souche introuvable.');
            return $this->redirectToRoute('page_strains');
            }

            if ($strain) {
                $this->denyAccessUnlessGranted('strain.is_creator', $strain);
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

        } catch (\Throwable $e) {
            dump($e); // n'affiche que si mode debug activé
            $this->addFlash('error', 'Une erreur est survenue lors de la suppresion de la souche. Veuillez réessayer.');

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
            $this->addFlash('error', 'Une erreur est survenue lors de la duplication de la souche. Veuillez réessayer.');

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

    #[Route('/api/strain/{id}', name: 'api_strain_get', methods: ['GET'])]
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

    ## L'Endpoint AJAX (Route principale)

    #[Route(path: '/strains/data', name: 'data_strains', methods: ['GET'])]
    public function getStrainsData(Request $request): Response
    {
        $params = $request->query->all();

        if (!is_array($params)) {
            return new JsonResponse(['error' => 'Invalid request parameters format'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $draw = $params['draw'] ?? 1;
        //$start = $params['start'] ?? 0;
        //$length = $params['length'] ?? 10;
        $search = $params['search']['value'] ?? '';
        $order = $params['order'][0] ?? null;
        $columns = $params['columns'] ?? [];

        $query = new Query();
        $boolQuery = new BoolQuery();

        // --- Gestion de l'ordre (tri) ---
        if (!empty($search)) {
            $numericSearch = filter_var($search, FILTER_VALIDATE_INT);

            // Si le terme de recherche est un nombre entier, nous ajoutons une correspondance exacte sur l'ID
            if ($numericSearch !== false) {
                $termQuery = new Term();
                $termQuery->setTerm('id', $numericSearch);
                $boolQuery->addShould($termQuery); 
            }

            $multiMatchQuery = new MultiMatch();
            $multiMatchQuery->setQuery($search);
            $multiMatchQuery->setFields([
                // Excluez 'id' de cette liste car il est géré spécifiquement si c'est un nombre
                'nameStrain',
                'specie',
                'gender',
                'prelevement.name',
                'genotype.type',
                'descriptionGenotype',
                'plasmyd.namePlasmyd',
                'plasmyd.type',
                'storage.room',
                'storage.fridge',
                'storage.shelf',
                'storage.rack',
                'storage.volume',
                'storage.containerType',
                'storage.containerPosition',
                'parentStrain.nameStrain',
                'project.name',
                'collec.name',
                'drugResistanceOnStrain.drugResistance.name',
                'methodSequencing.name',
                'publication.title',
                'publication.autor',
                'createdByName',
                'description',
                'comment'
            ]);
            $boolQuery->addShould($multiMatchQuery);
        } else {
            // Si aucune recherche globale, nous partons d'une requête MatchAll par défaut
            // pour permettre les filtres des formulaires.
            $boolQuery->addMust(new MatchAll());
        }
        // --- 2. GESTION DES FILTRES DU FORMULAIRE DE RECHERCHE AVANCÉE (flottant) ---
        // Les noms des paramètres 'search_query', 'search_plasmyd', etc. doivent correspondre
        // à ceux définis dans la fonction `data` de l'Ajax de DataTables dans votre JS.

        if ($request->query->has('search_query') && !empty($request->query->get('search_query'))) {
            $boolQuery->addMust(new Wildcard('nameStrain', '*' . $request->query->get('search_query') . '*'));
        }
        if ($request->query->has('search_plasmyd') && !empty($request->query->get('search_plasmyd'))) {
            $boolQuery->addMust(new MatchQuery('plasmyd.id', (int)$request->query->get('search_plasmyd')));
        }
        if ($request->query->has('search_sequencing') && !empty($request->query->get('search_sequencing'))) {
            $boolQuery->addMust(new MatchQuery('methodSequencing.typeFile', $request->query->get('search_sequencing')));
        }
        if ($request->query->has('search_drug') && !empty($request->query->get('search_drug'))) {
            $nestedBool = new BoolQuery();
            $nestedBool->addFilter(new MatchQuery('drugResistanceOnStrain.drugResistance.id', (int)$request->query->get('search_drug')));

            if ($request->query->has('search_resistant') && $request->query->get('search_resistant') !== null) {
                $resistant = filter_var($request->query->get('search_resistant'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($resistant !== null) {
                    $nestedBool->addFilter(new MatchQuery('drugResistanceOnStrain.resistant', $resistant));
                }
            }
            $nested = new Nested();
            $nested->setPath('drugResistanceOnStrain');
            $nested->setQuery($nestedBool);
            $boolQuery->addMust($nested);
        }
        if ($request->query->has('search_genotype') && !empty($request->query->get('search_genotype'))) {
            $boolQuery->addMust(new MatchQuery('genotype.id', (int)$request->query->get('search_genotype')));
        }
        // Dans StrainController.php
        if ($request->query->has('search_project') && !empty($request->query->get('search_project'))) {
            // C'est cette ligne qui est à vérifier
            $boolQuery->addMust(new MatchQuery('project.id', (int)$request->query->get('search_project')));
        }
        if ($request->query->has('search_sample') && !empty($request->query->get('search_sample'))) {
            $boolQuery->addMust(new MatchQuery('prelevement.id', (int)$request->query->get('search_sample')));
        }
        if ($request->query->has('search_user') && !empty($request->query->get('search_user'))) {
            $boolQuery->addMust(new MatchQuery('createdBy.id', (int)$request->query->get('search_user')));
        }
        if ($request->query->has('search_specie') && !empty($request->query->get('search_specie'))) {
            $boolQuery->addMust(new Wildcard('specie', '*' . $request->query->get('search_specie') . '*'));
        }
        if ($request->query->has('search_gender') && !empty($request->query->get('search_gender'))) {
            $boolQuery->addMust(new Wildcard('gender', '*' . $request->query->get('search_gender') . '*'));
        }
        
        $query = new Query ($boolQuery);

        // --- Gestion de l'ordre (tri) ---
        if ($order !== null) {
            $columnIndex = $order['column'];
            $columnName = $columns[$columnIndex]['data'];
            $orderDir = $order['dir'];

            $elasticFieldMap = [
                'id' => 'id', 'name' => 'nameStrain.keyword', 'date' => 'date',
                'specie' => 'specie.keyword', 'gender' => 'gender.keyword',
                'parent' => 'parentStrain.nameStrain.keyword', 'creator' => 'createdByName.keyword',
            ];

            if (isset($elasticFieldMap[$columnName])) {
                $query->addSort([$elasticFieldMap[$columnName] => ['order' => $orderDir]]);
            } else {
                $query->addSort(['id' => ['order' => 'desc']]);
            }
        } else {
            $query->addSort(['id' => ['order' => 'desc']]);
        }

        // Récupération de tous les résultats sans pagination via KnpPaginator
        $strainsFromElastic = $this->finder->find($query);

        $totalRecords = count($strainsFromElastic);
        $filteredRecords = $totalRecords;

        $data = [];
        foreach ($strainsFromElastic as $strain) {
            $maxLength = max(
                count($strain->getPlasmyd()),
                count($strain->getStorage()),
                count($strain->getProject()),
                count($strain->getCollec()),
                count($strain->getDrugResistanceOnStrain()),
                count($strain->getTransformability()),
                count($strain->getMethodSequencing()),
                count($strain->getPublication()),
                1
            );

            for ($i = 0; $i < $maxLength; $i++) {
                $rowData = []; // Initialize $rowData for EACH row

                // --- Colonnes 0 à 7 (Checkbox, ID, Name, Date, Specie, Gender, Sample, Genotype) ---
                if ($i === 0) {
                    $rowData[] = '<input type="checkbox" name="selected_strain[]" value="' . $strain->getId() . '" class="select-checkbox" />';
                    $rowData[] = $strain->getId();
                    $rowData[] = $strain->getNameStrain() ?? '--';
                    $rowData[] = $strain->getDate() ? $strain->getDate()->format('d/m/Y') : '--';
                    $rowData[] = $strain->getSpecie() ?? '--';
                    $rowData[] = $strain->getGender() ?? '--';

                    // Sample
                    $sampleInfo = [ /* ... votre logique ... */ ];
                    $rowData[] = '<span class="detail-cell sample" data-info=\'' . json_encode($sampleInfo, JSON_UNESCAPED_UNICODE) . '\'>' . ($strain->getPrelevement() ? $strain->getPrelevement()->getName() : '--') . '</span>';

                    // Genotype
                    $genotypeInfo = [ /* ... votre logique ... */ ];
                    $genotypeDisplay = $strain->getGenotype() ? $strain->getGenotype()->getType() : '--';
                    if ($strain->getDescriptionGenotype()) {
                        $desc = $strain->getDescriptionGenotype();
                        $maxLengthGenotype = 25;
                        $genotypeDisplay .= '<br>' . (mb_strlen($desc) > $maxLengthGenotype ? mb_substr($desc, 0, $maxLengthGenotype - 3) . '...' : $desc);
                    }
                    $rowData[] = '<span class="detail-cell genotype" data-info=\'' . json_encode($genotypeInfo, JSON_UNESCAPED_UNICODE) . '\'>' . $genotypeDisplay . '</span>';
                } else {
                    // Pour les lignes supplémentaires du groupe, les premières colonnes sont vides
                    $rowData[] = ''; // Checkbox
                    $rowData[] = ''; // ID
                    $rowData[] = ''; // Nom
                    $rowData[] = ''; // Date
                    $rowData[] = ''; // Specie
                    $rowData[] = ''; // Gender
                    $rowData[] = ''; // Sample
                    $rowData[] = ''; // Genotype
                }

                // --- Colonne 8 (Plasmyd) ---
                if (isset($strain->getPlasmyd()[$i])) {
                    $plasmyd = $strain->getPlasmyd()[$i];
                    $plasmydInfo = [ /* ... votre logique ... */ ];
                    $rowData[] = '<span class="detail-cell plasmyd" data-info=\'' . json_encode($plasmydInfo, JSON_UNESCAPED_UNICODE) . '\'>' . ($plasmyd->getNamePlasmyd() ?? '--') . '</span>';
                } else {
                    $rowData[] = '--';
                }

                // --- Colonnes 9 (Storage) ---
                if (isset($strain->getStorage()[$i])) {
                    $storage = $strain->getStorage()[$i];
                    $storageInfo = [ /* ... votre logique ... */ ];
                    $storageDisplay = "Room: " . ($storage->getRoom() ?? '--') . "<br>" .
                                    "Fridge: " . ($storage->getFridge() ?? '--') . "<br>" .
                                    "Shelf: " . ($storage->getShelf() ?? '--') . "<br>" .
                                    "Rack: " . ($storage->getRack() ?? '--') . "<br>" .
                                    "Volume: " . ($storage->getVolume() ?? '--') . "<br>" .
                                    "Container Type: " . ($storage->getContainerType() ?? '--') . "<br>" .
                                    "Container Position: " . ($storage->getContainerPosition() ?? '--');
                    $rowData[] = '<span class="detail-cell storage" data-info=\'' . json_encode($storageInfo, JSON_UNESCAPED_UNICODE) . '\'>' . $storageDisplay . '</span>';
                } else {
                    $rowData[] = '--';
                }

                // --- Colonne 10 (Parent) ---
                if ($i === 0) {
                    $rowData[] = $strain->getParentStrain() ? $strain->getParentStrain()->getNameStrain() : '--';
                } else {
                    $rowData[] = '';
                }

                // --- Colonne 11 (Project) ---
                if (isset($strain->getProject()[$i])) {
                    $rowData[] = $strain->getProject()[$i]->getName() ?? '--';
                } else {
                    $rowData[] = '--';
                }

                // --- Colonne 12 (Collec) ---
                if (isset($strain->getCollec()[$i])) {
                    $rowData[] = $strain->getCollec()[$i]->getName() ?? '--';
                } else {
                    $rowData[] = '--';
                }

                // --- Colonnes 13 (Drug Resistance) ---
                if (isset($strain->getDrugResistanceOnStrain()[$i])) {
                    $dr = $strain->getDrugResistanceOnStrain()[$i];
                    $resistantIcon = $dr->isResistant() ? '<a class="resistant-display-green">resistant : &#x2713;</a>' : '<a class="resistant-display-red">resistant : x</a>';
                    $drInfo = [ /* ... votre logique ... */ ];
                    $rowData[] = '<span class="detail-cell drugResistanceOnStrain" data-info=\'' . json_encode($drInfo, JSON_UNESCAPED_UNICODE) . '\' data-file="' . ($dr->getNameFile() ?? '') . '">' .
                                ($dr->getDrugResistance() ? $dr->getDrugResistance()->getName() : '--') . '<br>' .
                                ($dr->getConcentration() ?? '') . ' ' . $resistantIcon . '</span>';
                } else {
                    $rowData[] = '--';
                }

                // --- Colonnes 14 (Transformability) ---
                if (isset($strain->getTransformability()[$i])) {
                    $trans = $strain->getTransformability()[$i];
                    $transInfo = [ /* ... votre logique ... */ ];
                    $rowData[] = '<span class="detail-cell transformability" data-info=\'' . json_encode($transInfo, JSON_UNESCAPED_UNICODE) . '\' data-file="' . ($trans->getNom() ?? '') . '">' .
                                ($trans->getTechnique() ?? '--') . '<br>' . ($trans->getMesure() ?? '') . '</span>';
                } else {
                    $rowData[] = '--';
                }

                // --- Colonnes 15 (Sequencing) ---
                if (isset($strain->getMethodSequencing()[$i])) {
                    $seq = $strain->getMethodSequencing()[$i];
                    $nameFile = $seq->getNameFile();
                    $maxLengthFile = 15;
                    $displayedFileName = '--';
                    if ($nameFile) {
                        $pathInfo = pathinfo($nameFile);
                        $filenameNoExt = $pathInfo['filename'] ?? '';
                        $extension = $pathInfo['extension'] ?? '';
                        if (mb_strlen($filenameNoExt) > $maxLengthFile - 3) {
                            $displayedFileName = mb_substr($filenameNoExt, 0, $maxLengthFile - 6) . '...' . mb_substr($filenameNoExt, -3);
                        } else {
                            $displayedFileName = $filenameNoExt;
                        }
                        if ($extension) {
                            $displayedFileName .= '.' . $extension;
                        }
                    }
                    $seqInfo = [ /* ... votre logique ... */ ];
                    $rowData[] = '<span class="detail-cell sequencing" data-info=\'' . json_encode($seqInfo, JSON_UNESCAPED_UNICODE) . '\' data-file="' . ($seq->getNameFile() ?? '') . '">' .
                                ($seq->getName() ?? '--') . '<br>' . $displayedFileName . '</span>';
                } else {
                    $rowData[] = '--';
                }

                // --- Colonnes 16 (Publication) ---
                if (isset($strain->getPublication()[$i])) {
                    $pub = $strain->getPublication()[$i];
                    $pubInfo = [ /* ... votre logique ... */ ];
                    $rowData[] = '<span class="detail-cell publication" data-info=\'' . json_encode($pubInfo, JSON_UNESCAPED_UNICODE) . '\'>' .
                                ($pub->getTitle() ?? '--') . '<br>' . ($pub->getAutor() ?? '--') . '<br>' . ($pub->getYear() ?? '') . '</span>';
                } else {
                    $rowData[] = '--';
                }

                // --- Colonnes 17, 18, 19 (Creator, Description, Comment) ---
                if ($i === 0) {
                    $rowData[] = $strain->getCreatedByName() ?? '--'; // Index 17

                    $description = $strain->getDescription();
                    $maxLengthDesc = 10;
                    $displayedDescription = $description ? (mb_strlen($description) > $maxLengthDesc ? mb_substr($description, 0, $maxLengthDesc - 3) . '...' : $description) : '--';
                    $rowData[] = '<span class="detail-cell description" data-info="' . htmlspecialchars($strain->getDescription() ?? '--') . '">' . $displayedDescription . '</span>'; // Index 18

                    $comment = $strain->getComment();
                    $maxLengthComment = 10;
                    $displayedComment = $comment ? (mb_strlen($comment) > $maxLengthComment ? mb_substr($comment, 0, $maxLengthComment - 3) . '...' : $comment) : '--';
                    $rowData[] = '<span class="detail-cell comment" data-info="' . htmlspecialchars($strain->getComment() ?? '--') . '">' . $displayedComment . '</span>'; // Index 19
                } else {
                    $rowData[] = ''; // Creator
                    $rowData[] = ''; // Description
                    $rowData[] = ''; // Comment
                }

                if ($i === 0) {
                    // Boutons visibles seulement sur la première ligne du groupe
                    $rowData[] = '<button type="button" class="btn btn-sm btn-secondary duplicate-button" data-id="' . $strain->getId() . '">Dupliquer</button>';
                    $rowData[] = '<button type="button" class="btn btn-sm btn-primary edit-button" data-id="' . $strain->getId() . '">Modifier</button>';
                    $rowData[] = '<button type="button" class="btn btn-sm btn-danger delete-button" data-id="' . $strain->getId() . '">Supprimer</button>';
                } else {
                    // Lignes supplémentaires : colonnes vides
                    $rowData[] = '';
                    $rowData[] = '';
                    $rowData[] = '';
                }

                $data[] = $rowData;
            } 
        } 

        // 5. Retourne la réponse JSON attendue par DataTables
        return $this->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }
}