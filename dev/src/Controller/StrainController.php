<?php 

namespace App\Controller;

use App\Entity\Strain;
use App\Form\ParentFormType;
use App\Form\SearchFormType;
use App\Repository\StrainRepositoryInterface;
use App\Form\StrainFormType;
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
use Symfony\Component\Form\Form;
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
        private readonly PaginatedFinderInterface $finder
    ) {
        $this->paginator = $paginator; 
    }

    #[Route(path: 'strains/page', name: 'page_strains')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {

        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')){
            $responseAdd = $this->add($request, $em, $security);  
        }

        $elasticResponse = $this->elasticForm($request);
        $formElastic = $elasticResponse['form'];
        $strains = $elasticResponse['pagination'];

        if (!$strains) {
            $query = new Query ();
            $matchAll = new MatchAll();

            $query->setQuery($matchAll);
            $query->setSort(['id' => ['order' => 'desc']]);

            // Créer l'adaptateur de pagination
            $paginatorAdapter = $this->finder->createPaginatorAdapter($query);

            // Paginer pour récupérer 30 résultats max
            $strains = $this->paginator->paginate($paginatorAdapter, $request->query->getInt('page', 1), 15);
        }

        return $this->render('strain/main.html.twig', [
            'strainForm' => $responseAdd, 
            'form' => $formElastic->createView(),
            'strains' => $strains
        ]);

    }

    #[Route(path: 'strains/add', name: 'add_strain')]
    #[IsGranted('ROLE_SEARCH')]
    public function add(Request $request, EntityManagerInterface $em, Security $security): Form
    {
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
                $filename = $sequencing->getFile()->getClientOriginalName();
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $sequencing->setTypeFile($extension);
            }

            //stock data
            $em->persist($strain);
            $em->flush();

            // redirect
            return $strainForm;
        }

        return $strainForm;
    }

    #[Route('vehicules/edit/{id}', name: 'edit_strain')]
    #[IsGranted('ROLE_SEARCH')]
    public function edit(
        Strain $strain,
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        if ($strain) {
            $this->denyAccessUnlessGranted('strain.is_creator', $strain);
        }

        //Create the form
        $strainForm = $this->createForm(StrainFormType::class, $strain);

        //treat the request
        $strainForm->handleRequest($request);

        if ($strainForm->isSubmitted() && $strainForm->isValid()) {
            //generate the slug

            //stock data
            $em->persist($strain);
            $em->flush();

            $this->addFlash('success', 'Strain ' . $strain->getNameStrain() . ' modified with succes !');

            return $this->redirectToRoute('page_strains');
        }
        return $this->render('strain/edit.html.twig', compact('strainForm'));
    }

    #[Route('strains/delete/{id}', name: 'delete_strain')]
    #[IsGranted('ROLE_SEARCH')]
    public function delete(Strain $strain, EntityManagerInterface $em): Response
    {
        if ($strain) {
            $this->denyAccessUnlessGranted('strain.is_creator', $strain);
        }

        foreach ($strain->getMethodSequencing() as $sequencing){
            $em->remove($sequencing);
        }
        foreach ($strain->getTransformability() as $transfo){
            $em->remove($transfo);
        }
        foreach ($strain->getPublication() as $publi){
            $em->remove($publi);
        }
        foreach($strain->getPlasmyd() as $plasmyd){
            $em->remove($plasmyd);
        }
        foreach($strain->getDrugResistanceOnStrain() as $drug){
            $em->remove($drug);
        }

        $em->remove($strain);
        $em->flush();

        $this->addFlash('success', 'Strain ' . $strain->getNameStrain() . ' delete with success !');

        return $this->redirectToRoute('page_strains');
    }

    #[Route('strains/duplicate/{id}', name: 'duplicate_strain')]
    #[IsGranted('ROLE_SEARCH')]
    public function duplicate(
        Strain $strain,
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): Response {

        $user = $security->getUser(); 

        $strainCloned = clone $strain; 

        $strainCloned->setId(null);
        $strainCloned->setCreatedBy($user);
        $strainCloned->setDate(new \DateTime());
      
        $em->persist($strainCloned);
        $em->flush();


        $this->addFlash('success', 'Strain ' . $strainCloned->getNameStrain() . ' duplicate with succes !');

        return $this->redirectToRoute('page_strains');

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
    public function elasticForm(Request $request): Array
    {
        $form = $this -> createForm(SearchFormType::class); 
        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()){
            /** @var SearchModel $data */
            $data = $form->getData(); 
            $page = $request->query->getInt('page',1);
            
            $boolQuery = new BoolQuery();

            if ($data->query){
                $boolQuery->addMust(new Wildcard('nameStrain', '*'.$data->query.'*')); 
            }

            if ($data->plasmyd){
                $boolQuery->addFilter(new MatchQuery('plasmyd.id', $data->plasmyd->getId()));
            }

            if ($data->sequencing) {
                $boolQuery->addFilter(new MatchQuery('methodSequencing.typeFile', $data->sequencing));
            }

            // if ($data->drug || $data->resistant !== null ){
            //     $nestedBool = new BoolQuery();
                
            //     if ($data->drug) {
            //         $nestedBool->addFilter(new MatchQuery('drugResistanceOnStrain.drugResistance.id', $data->drug->getId()));
            //     }
            
            //     if ($data->resistant !== null) {
            //         $nestedBool->addFilter(new MatchQuery('drugResistanceOnStrain.resistant', $data->resistant));
            //     }

            //     $nested = new Nested();
            //     $nested->setPath('drugResistanceOnStrain');
            //     $nested->setQuery($nestedBool);

            //     $boolQuery->addFilter($nested);
            // }

            if ($data->genotype){
                $boolQuery->addFilter(new MatchQuery('genotype.id', $data->genotype->getId()));
            }

            if ($data->project){
                $boolQuery->addFilter(new MatchQuery('project.id', $data->project->getId()));
            }

            if ($data->sample){
                $boolQuery->addFilter(new MatchQuery('sample.id', $data->sample->getId()));
            }

            if ($data->user){
                $boolQuery->addFilter(new MatchQuery('createdBy.id', $data->user->getId()));
            }

            if ($data->specie) {
                $boolQuery->addFilter(new Wildcard('specie', '*'.$data->specie.'*'));
            }

            if ($data->gender) {
                $boolQuery->addFilter(new Wildcard('gender', '*'.$data->gender.'*'));
            }

            $query = new Query ($boolQuery);
            $query->setSort(['id' => ['order' => 'desc']]);

            // Crée le paginator à partir de la query complète
            $results = $this->finder->createPaginatorAdapter($query);
            $pagination = $this->paginator->paginate($results, $page, 15);
        } 

        return [
            'form' => $form,
            'pagination' => $pagination ?? []
        ];
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

}