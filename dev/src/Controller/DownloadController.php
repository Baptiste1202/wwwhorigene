<?php

namespace App\Controller;

use App\Storage\S3VichStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Storage\StorageInterface;

#[Route('/documents')]
class DownloadController extends AbstractController
{
    private S3VichStorage $s3Storage;

    public function __construct(StorageInterface $storage)
    {
        if (!$storage instanceof S3VichStorage) {
            throw new \RuntimeException('Le storage Vich doit être S3VichStorage');
        }
        
        $this->s3Storage = $storage;
    }

    #[Route('/download/{fileType}/{fileName}', name: 'document_download')]
    #[IsGranted('ROLE_INTERN')]
    public function download(string $fileType, string $fileName): RedirectResponse
    {
        $fileName = basename($fileName); // Sécurité
        $s3Key = sprintf('docs/%s/%s', $fileType, $fileName);

        try {
            // Génère un lien pré-signé valide 20 minutes
            $cmd = $this->s3Storage->getS3Client()->getCommand('GetObject', [
                'Bucket' => $this->s3Storage->getBucket(),
                'Key'    => $s3Key,
                'ResponseContentDisposition' => sprintf('attachment; filename="%s"', $fileName),
            ]);

            $request = $this->s3Storage->getS3Client()->createPresignedRequest($cmd, '+20 minutes');
            return $this->redirect((string) $request->getUri());
            
        } catch (\Exception $e) {
            throw $this->createNotFoundException('Fichier non trouvé');
        }
    }
}