<?php
// src/Controller/DownloadMultipleController.php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DownloadMultipleController extends AbstractController
{
    #[Route('/download-multiple', name: 'download_multiple', methods: ['POST'])]
    public function downloadMultiple(Request $request, LoggerInterface $logger): Response
    {
        $data      = json_decode($request->getContent(), true);
        $entries   = $data['entries']   ?? [];
        $extension = trim($data['extension'] ?? '');

        $logger->info('Début downloadMultiple', [
            'raw_payload' => $data,
            'entries_count' => count($entries),
            'extension' => $extension,
        ]);

        if (!is_array($entries) || empty($entries)) {
            $logger->warning('Aucune entrée reçue pour le ZIP');
            return new Response('No files specified for download.', 400);
        }

        $folderMap = [
            'sequencing'       => 'sequencing',
            'transformability' => 'transformability',
            'drugs'            => 'drugs',
        ];

        $projectDir = $this->getParameter('kernel.project_dir');
        $baseDir    = $projectDir . '/public/docs';

        $tmpDir = sys_get_temp_dir() . '/download_zip_' . uniqid();
        if (!is_dir($tmpDir)) mkdir($tmpDir, 0700, true);

        $tmpFiles = [];

        // 1. Copier chaque fichier avec le nom formaté dans $tmpDir
        foreach ($entries as $i => $e) {
            if (!is_array($e) || !isset($e['id'], $e['type'], $e['name'])) {
                $logger->warning("Entrée mal formée ou champs manquants", ['entry' => $e]);
                continue;
            }

            $strainId = preg_replace('/\D+/', '', (string)$e['id']);
            $type     = $e['type'];
            $fileName = basename($e['name']);

            if (!array_key_exists($type, $folderMap) || $fileName === '') {
                $logger->warning("Type ou nom de fichier manquant/non valide", [
                    'id' => $strainId,
                    'type' => $type,
                    'fileName' => $fileName
                ]);
                continue;
            }

            $folder    = $folderMap[$type];
            $source    = realpath("$baseDir/$folder/$fileName");
            if (!$source || !is_file($source)) {
                $logger->warning("Fichier manquant ou non trouvé", [
                    'id' => $strainId,
                    'type' => $type,
                    'fileName' => $fileName,
                    'fullpath' => "$baseDir/$folder/$fileName"
                ]);
                continue;
            }
            if ($extension && !str_ends_with(strtolower($source), strtolower($extension))) {
                $logger->info("Fichier filtré par extension", [
                    'id' => $strainId,
                    'type' => $type,
                    'fileName' => $fileName,
                    'extension' => $extension
                ]);
                continue;
            }

            // Nom voulu dans le zip
            $newName = "{$strainId}_{$type}_{$fileName}";
            $tmpPath = $tmpDir . '/' . $newName;

            // Copier le fichier
            if (!copy($source, $tmpPath)) {
                $logger->warning("Echec de copie du fichier temporaire", [
                    'id' => $strainId,
                    'type' => $type,
                    'fileName' => $fileName,
                    'source' => $source,
                    'tmpPath' => $tmpPath
                ]);
                continue;
            }

            $logger->info("Fichier trouvé et copié", [
                'id' => $strainId,
                'type' => $type,
                'fileName' => $fileName,
                'newName' => $newName
            ]);
            $tmpFiles[] = $tmpPath;
        }

        if (empty($tmpFiles)) {
            $logger->warning('Aucun fichier valide après filtrage');
            return new Response('No valid files found in docs subfolders.', 400);
        }

        // 2. Créer le zip avec les fichiers temporaires
        $zipName    = 'files_' . date('Ymd_His') . '.zip';
        $tmpZipPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipName;

        $zipArgs = array_map('escapeshellarg', $tmpFiles);
        $cmd     = sprintf('zip -j -q %s %s', escapeshellarg($tmpZipPath), implode(' ', $zipArgs));
        $logger->debug('Commande ZIP', ['cmd' => $cmd]);
        exec($cmd, $output, $returnVar);

        // Nettoyer les fichiers temporaires (sauf le zip)
        foreach ($tmpFiles as $f) @unlink($f);
        @rmdir($tmpDir);

        if ($returnVar !== 0 || !file_exists($tmpZipPath)) {
            $logger->error('Échec création ZIP', ['returnVar' => $returnVar, 'output' => $output]);
            return new Response('Error creating ZIP archive.', 500);
        }

        $logger->info('ZIP créé avec succès', ['zip' => $tmpZipPath]);

        return $this
            ->file($tmpZipPath, $zipName)
            ->deleteFileAfterSend(true);
    }
}
