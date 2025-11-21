<?php
// src/Controller/DownloadMultipleController.php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\ZipStream;
use ZipStream\Option\Archive as ZipArchiveOptions;

class DownloadMultipleController extends AbstractController
{

    public function downloadMultiple(
        Request $request,
        \Vich\UploaderBundle\Storage\StorageInterface $storage,
        EntityManagerInterface $em
    ): StreamedResponse {

        $entries = $request->request->get('entries', []);

        if (empty($entries)) {
            throw new \Exception("Aucun fichier demandé.");
        }

        return new StreamedResponse(function () use ($entries, $storage, $em) {

            // Options ZIP pour ZipStream v3
            $options = new ZipArchiveOptions();
            $options->setSendHttpHeaders(true);

            // Nom du zip (envoyé automatiquement dans les headers)
            $zip = new ZipStream('export.zip', $options);

            foreach ($entries as $entry) {

                $id   = (int) $entry['id'];
                $type = $entry['type']; // sequencing, drugs, phenotype
                $name = $entry['name']; // ex: durg-xxxx.txt

                // 1) Récupérer l'entité
                $entity = $this->resolveEntity($em, $type, $id);
                if (!$entity) {
                    continue;
                }

                // 2) Récupérer le flux S3
                $stream = $storage->resolveStream($entity, 'file'); 
                // ⚠️ remplace 'file' par le nom réel du champ Vich dans ton Entity

                if (!$stream) {
                    continue;
                }

                // 3) Ajouter au ZIP dans un dossier selon le type
                $zipPath = $type . '/' . $name;

                $zip->addFileFromStream($zipPath, $stream);

                fclose($stream);
            }

            // 4) Fin du ZIP
            $zip->finish();
        }, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="export.zip"',
        ]);
    }

    private function resolveEntity(EntityManagerInterface $em, string $type, int $id)
    {
        return match ($type) {
            'sequencing' => $em->getRepository(Sequencing::class)->find($id),
            'drugs'      => $em->getRepository(Drugs::class)->find($id),
            'phenotype'  => $em->getRepository(Phenotype::class)->find($id),
            default      => null,
        };
    }
}