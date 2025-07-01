<?php 

namespace App\Controller;

use App\Entity\Publication;
use App\Form\PublicationFormType;
use App\Repository\PublicationRepository;
use App\Repository\PublicationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PublicationController extends AbstractController
{
    public function __construct(
        #[Autowire(service: PublicationRepository::class)]
        private PublicationRepositoryInterface $publicationRepository,
    ) {
    }

    #[Route(path: 'page_publications', name: 'page_publications')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {

        $publicationAdd = $this->createForm(PublicationFormType::class); 

        if ($security->isGranted('ROLE_SEARCH') || $security->isGranted('ROLE_ADMIN')){
            $publicationAdd = $this->addForm($request, $em); 
        } 

        $publications = $this->publicationRepository->findAll();

        return $this->render('publication/main.html.twig', [
            'publicationForm' => $publicationAdd, 
            'publications' => $publications
        ]);
    }

    #[Route(path: '/publication', name: 'list_publications')]
    #[IsGranted('ROLE_INTERN')]
    public function showAll(): Response
    {
        $publications = $this->publicationRepository->findAll();

        return $this->render('publication/list.html.twig', ['publications' => $publications]);
    }

    #[Route(path: 'strains/publications/ajout', name: 'add_publication')]
    #[IsGranted('ROLE_SEARCH')]
    public function addForm(Request $request, EntityManagerInterface $em): Form
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $publication = new Publication();

        //Create the form
        $publicationForm = $this->createForm(PublicationFormType::class, $publication);

        //
        $publicationForm->handleRequest($request);

        if ($publicationForm->isSubmitted() && $publicationForm->isValid()) {
            //generate the slug -- not done yet
            $slug = $publication->getTitle(). ' - ' .$publication->getAutor(). ' - ' .$publication->getYear();
            $publication->setSlug($slug);

            //get the user -- not done yet

            //stock data
            $em->persist($publication);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $publicationForm;
        }
        return $publicationForm;
    }

    #[Route(path: 'strains/publications/ajout/response', name: 'add_publication_response')]
    #[IsGranted('ROLE_SEARCH')]
    public function addResponse(Request $request, EntityManagerInterface $em): Response
    {

        // $this->denyAccessUnlessGranted('ROLE_RENTER');

        //Create a new vehicule
        $publication = new Publication();

        //Create the form
        $publicationForm = $this->createForm(PublicationFormType::class, $publication);

        //
        $publicationForm->handleRequest($request);

        if ($publicationForm->isSubmitted() && $publicationForm->isValid()) {
            //generate the slug -- not done yet

            //get the user -- not done yet

            //stock data
            $em->persist($publication);
            $em->flush();

            // $this->addFlash('success', 'Vehicule ' . $vehicule->getSlug() . 'ajouté avec succés !');


            // redirect
            return $this->redirectToRoute('page_strains');
        }
        return $this->render('publication/create.html.twig', compact('publicationForm'));
    }

    #[Route('strains/publication/duplicate/{id}', name: 'duplicate_publication')]
    #[IsGranted('ROLE_SEARCH')]
    public function duplicatePublication(Publication $publication, EntityManagerInterface $em, Security $security): Response
    {
        try {
            // Récupérer l'utilisateur connecté (optionnel si besoin)
            $user = $security->getUser();

            // Créer une nouvelle instance de Publication (la copie)
            $clone = new Publication();

            // Copier les champs simples de l'entité originale
            $clone->setTitle($publication->getTitle());
            $clone->setArticleUrl($publication->getArticleUrl());
            $clone->setAutor($publication->getAutor());
            $clone->setYear($publication->getYear());
            $clone->setDoi($publication->getDoi());
            $clone->setDescription($publication->getDescription());

            // Persister et flush
            $em->persist($clone);
            $em->flush();

            // Message flash
            $this->addFlash('success', 'Publication "' . $clone->getTitle() . '" dupliquée avec succès !');

            return $this->redirectToRoute('page_publications');

        } catch (\Throwable $e) {
            $this->addFlash('error', 'Erreur lors de la duplication de la publication.');
            return $this->redirectToRoute('page_publications');
        }
    }

    #[Route('strains/publication/edit/{id}', name: 'edit_publication')]
    #[IsGranted('ROLE_SEARCH')]
    public function edit(
        Publication $publication,
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        /*
        if ($vehicule) {
            $this->denyAccessUnlessGranted('vehicule.is_creator', $vehicule);
        }
        */

        //Create the form
        $publicationForm = $this->createForm(PublicationFormType::class, $publication);

        //treat the request
        $publicationForm->handleRequest($request);

        if ($publicationForm->isSubmitted() && $publicationForm->isValid()) {
            //generate the slug
            $slug = $publication->getTitle(). ' - ' .$publication->getAutor(). ' - ' .$publication->getYear();
            $publication->setSlug($slug);

            //stock data
            $em->persist($publication);
            $em->flush();

            $this->addFlash('success', 'publication ' . $publication->getTitle() . ' modified with succes !');

            return $this->redirectToRoute('page_publications');
        }
        return $this->render('publication/edit.html.twig', compact('publicationForm'));
    }

    #[Route('strains/publication/delete/{id}', name: 'delete_publication')]
    #[IsGranted('ROLE_SEARCH')]
    public function delete(Publication $publication, EntityManagerInterface $em): Response
    {
        // Vérifie les souches associées
        $soucheIds = $publication->getStrain()->map(fn($strain) => $strain->getId())->toArray();

        if (count($soucheIds) > 0) {
            $this->addFlash('error', 'Impossible de supprimer cette publication car elle est associée aux souches d\'ID suivants : ' . implode(', ', $soucheIds) . '.');
        } else {
            // Supprime directement
            $em->remove($publication);
            $em->flush();

            $this->addFlash('success', 'Publication "' . $publication->getTitle() . '" supprimée avec succès !');
        }

        return $this->redirectToRoute('page_publications');
    }


    #[Route('strains/publications/delete-multiple', name: 'delete_multiple_publications', methods: ['POST'])]
    #[IsGranted('ROLE_SEARCH')]
    public function deleteMultiple(Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer les ids sélectionnés via la requête POST
        $ids = $request->request->all('selected_publications');

        // Vérifier que ce soit un tableau non vide
        if (!is_array($ids) || empty($ids)) {
            $this->addFlash('error', 'Aucune publication sélectionnée.');
            return $this->redirectToRoute('page_publications');
        }

        // Chercher toutes les publications correspondantes
        $publications = $em->getRepository(Publication::class)->findBy(['id' => $ids]);

        if (!$publications) {
            $this->addFlash('error', 'Aucune publication trouvée pour suppression.');
            return $this->redirectToRoute('page_publications');
        }

        $detailsDeleted = [];
        $detailsBlocked = [];

        foreach ($publications as $publication) {
            // Blocage si la publication a des souches associées
            if (count($publication->getStrain()) > 0) {
                $detailsBlocked[] = sprintf('[ID: %d - Titre: %s]', $publication->getId(), $publication->getTitle());
                continue;
            }

            // Sinon on supprime
            $detailsDeleted[] = sprintf('[ID: %d - Titre: %s]', $publication->getId(), $publication->getTitle());
            $em->remove($publication);
        }

        // Valider la suppression en base (pour les publications sans souches)
        if (!empty($detailsDeleted)) {
            $em->flush();
        }

        // Message succès pour les publications supprimées
        if (!empty($detailsDeleted)) {
            $this->addFlash('success', sprintf(
                '%d publication(s) supprimée(s) avec succès : %s',
                count($detailsDeleted),
                implode(', ', $detailsDeleted)
            ));
        }

        // Message d’erreur pour les publications bloquées
        if (!empty($detailsBlocked)) {
            $this->addFlash('error', 'Impossible de supprimer certaines publications car elles sont associées à des souches : ' . implode(', ', $detailsBlocked));
        }

        // Rediriger vers la page des publications
        return $this->redirectToRoute('page_publications');
    }



}