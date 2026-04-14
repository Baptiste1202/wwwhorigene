<?php

namespace App\Controller;

use App\Storage\S3VichStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Storage\StorageInterface;

#[Route('/documents')]
class DownloadController extends AbstractController
{
    public function __construct(
        private readonly StorageInterface $storage
    ) {
    }

    #[Route('/download/{fileType}/{fileName}', name: 'document_download')]
    #[IsGranted('ROLE_INTERN')]
    public function download(string $fileType, string $fileName): RedirectResponse|BinaryFileResponse
    {
        $fileName = basename($fileName);
        $fileType = basename($fileType);

        $allowedTypes = ['phenotype', 'sequencing', 'drugs'];
        if (!in_array($fileType, $allowedTypes, true)) {
            throw $this->createNotFoundException('Type de fichier invalide');
        }

        // =========================
        // CAS S3
        // =========================
        if ($this->storage instanceof S3VichStorage) {
            $s3Key = sprintf('docs/%s/%s', $fileType, $fileName);

            try {
                $cmd = $this->storage->getS3Client()->getCommand('GetObject', [
                    'Bucket' => $this->storage->getBucket(),
                    'Key' => $s3Key,
                    'ResponseContentDisposition' => sprintf(
                        'attachment; filename="%s"',
                        $fileName
                    ),
                ]);

                $request = $this->storage
                    ->getS3Client()
                    ->createPresignedRequest($cmd, '+20 minutes');

                return $this->redirect((string) $request->getUri());
            } catch (\Throwable $e) {
                throw $this->createNotFoundException('Fichier non trouvé');
            }
        }

        // =========================
        // CAS LOCAL
        // =========================
        $filePath = $this->getParameter('kernel.project_dir') . sprintf(
            '/public/docs/%s/%s',
            $fileType,
            $fileName
        );

        if (!is_file($filePath)) {
            throw $this->createNotFoundException('Fichier non trouvé');
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        return $response;
    }
}