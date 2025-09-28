<?php 

namespace App\Controller;

use Psr\Log\LoggerInterface;
use App\Entity\Strain;
use App\Entity\User;
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
use Elastica\Query\Term;
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

class BinController extends AbstractController
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

    #[Route(path: 'strains_bin/page', name: 'page_strains_bin')]
    #[IsGranted('ROLE_INTERN')]
    public function showPage(Security $security): Response
    {
        $cmd = 'docker exec -it claranet2-app-1 bash bin/console fos:elastica:populate';
        exec($cmd, $output, $returnCode);

        $user = $security->getUser(); 

        $query = new Query();

        $boolQuery = new BoolQuery();
        $boolQuery->addMust(new Exists('dateArchive'));

        if ($user instanceof User) {
            $boolQuery->addMust(new Term(['createdBy.id' => $user->getId()]));
        }

        $query->setQuery($boolQuery);
        $query->setSort(['id' => ['order' => 'desc']]);

        $strains = $this->finder->find($query, 10000);

        return $this->render('bin/main.html.twig', [
            'user' => $user,
            'strains' => $strains,
            'is_bin' => true,
        ]);

    }
}