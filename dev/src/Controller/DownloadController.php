<?php

namespace App\Controller; 

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/documents')]
class DownloadController extends AbstractController
{
    #[Route('/download/{fileName}', name: 'document_download')]
    public function download(string $fileName): BinaryFileResponse
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/docs/sequencing/' . $fileName;

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