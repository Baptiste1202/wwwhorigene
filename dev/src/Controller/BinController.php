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

    #[Route(path: 'strains_bin/delete-multiple', name: 'bin_delete_multiple_strains', methods: ['POST'])]
    #[IsGranted('ROLE_SEARCH')]
    public function deleteMultipleStrains(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        // 1) Récupérer les IDs sélectionnés
        $ids = $request->request->all('selected_strain');
        if (!is_array($ids) || empty($ids)) {
            $this->addFlash('error', 'No strain selected.');
            return $this->redirectToRoute('page_strains_bin');
        }

        /** @var Strain[] $strains */
        $strains = $em->getRepository(Strain::class)->findBy(['id' => $ids]);
        if (!$strains) {
            $this->addFlash('error', 'No strains found for deletion.');
            return $this->redirectToRoute('page_strains_bin');
        }

        $deleted = [];
        $blocked = [];

        // IDs demandés mais introuvables
        $foundIds = array_map(fn(Strain $s) => (int) $s->getId(), $strains);
        $missing  = array_diff(array_map('intval', $ids), $foundIds);
        foreach ($missing as $miss) {
            $blocked[] = sprintf('ID: %d (not found)', $miss);
        }

        foreach ($strains as $strain) {
            $id   = (int) $strain->getId();
            $name = (string) ($strain->getNameStrain() ?? '');

            try {
                // Vérifier les droits (même voter que pour delete unitaire)
                if (!$this->isGranted('strain.is_creator', $strain)) {
                    $blocked[] = sprintf(
                        'ID: %d - Name: "%s" (insufficient rights)',
                        $id,
                        $name !== '' ? $name : '--'
                    );
                    continue;
                }

                // 🔥 Suppression définitive (copie ta logique du delete)
                $strain->getCollec()->clear();
                $strain->getPlasmyd()->clear();
                $strain->getPublication()->clear();
                $strain->getProject()->clear();

                // Les OneToMany sont gérés par cascade
                $em->remove($strain);

                $deleted[] = sprintf(
                    'ID: %d - Name: "%s"',
                    $id,
                    $name !== '' ? $name : '--'
                );

            } catch (\Throwable $e) {
                $blocked[] = sprintf(
                    'ID: %d - Name: "%s" (error: %s)',
                    $id,
                    $name !== '' ? $name : '--',
                    $e->getMessage()
                );
            }
        }

        try {
            $em->flush();
        } catch (\Throwable $e) {
            $this->addFlash('error', 'Global error while deleting strains: ' . $e->getMessage());
            return $this->redirectToRoute('page_strains_bin');
        }

        if (!empty($deleted)) {
            $this->addFlash('success', sprintf(
                'Deleted %d strain(s): %s',
                count($deleted),
                implode(', ', array_slice($deleted, 0, 10)) . (count($deleted) > 10 ? '…' : '')
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

        return $this->redirectToRoute('page_strains_bin');
    }
}