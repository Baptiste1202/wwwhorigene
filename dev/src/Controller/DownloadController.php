<?php

namespace App\Controller; 

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/documents')]
class DownloadController extends AbstractController
{
    #[Route('/download/{fileType}/{fileName}', name: 'document_download')]
    #[IsGranted('ROLE_INTERN')]
    public function download(string $fileType, string $fileName): BinaryFileResponse
    {
        $filePath = $this->getParameter('kernel.project_dir') . "/public/docs/{$fileType}/{$fileName}";

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Le fichier n\'existe pas.');
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        return $response;
    }
}