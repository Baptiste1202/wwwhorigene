<?php
// src/Controller/DownloadMultipleController.php

namespace App\Controller;

use App\Repository\PhenotypeRepository;
use App\Repository\StrainRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DownloadMultipleController extends AbstractController
{
    #[Route('/download-multiple', name: 'download_multiple', methods: ['POST'])]
    #[IsGranted('ROLE_INTERN')]
    public function downloadMultiple(
        Request $request,
        LoggerInterface $logger,
        PhenotypeRepository $phenotypeRepo,
        StrainRepository $strainRepo, // utile si besoin de contrôles/validations
    ): Response {
        $data             = json_decode($request->getContent(), true);
        $entries          = $data['entries']          ?? [];  // sequencing/drugs depuis le front
        $extensionFilter  = trim((string)($data['extension'] ?? ''));
        $types            = $data['types']            ?? [];  // ex: ["sequencing","phenotype","drugs"]
        $strainIds        = $data['strainIds']        ?? [];  // ex: ["12","34", ...]
        $phenotypeTypeIds = $data['phenotypeTypeIds'] ?? [];  // ex: [1,5,7]

        $logger->info('Début downloadMultiple', [
            'entries_count'    => is_array($entries) ? count($entries) : 0,
            'extension_filter' => $extensionFilter,
            'types'            => $types,
            'strainIds'        => $strainIds,
            'phenotypeTypeIds' => $phenotypeTypeIds,
        ]);

        if ((!is_array($entries) || empty($entries)) && !in_array('phenotype', $types, true)) {
            $logger->warning('Aucune entrée reçue et phenotype non demandé');
            return new Response('No files specified for download.', 400);
        }

        // Dossiers physiques
        $projectDir = $this->getParameter('kernel.project_dir');
        $baseDir    = $projectDir . '/public/docs';

        // Map dossiers (ajuste si nécessaire)
        $folderMap = [
            'sequencing' => 'sequencing',
            'drugs'      => 'drugs',
            'phenotype'  => 'phenotype', // <-- adapte si besoin
        ];

        // Répertoire temporaire pour fabriquer le ZIP
        $tmpDir = sys_get_temp_dir() . '/download_zip_' . uniqid();
        if (!is_dir($tmpDir) && !mkdir($tmpDir, 0700, true) && !is_dir($tmpDir)) {
            $logger->error('Impossible de créer le dossier temporaire', ['tmpDir' => $tmpDir]);
            return new Response('Temp dir creation failed.', 500);
        }

        $tmpFiles = [];

        // --- 1) Traiter sequencing / drugs depuis $entries ---
        foreach ($entries as $e) {
            if (!is_array($e) || !isset($e['id'], $e['type'], $e['name'])) {
                $logger->warning('Entrée mal formée (sequencing/drugs)', ['entry' => $e]);
                continue;
            }

            $strainId = preg_replace('/\D+/', '', (string)$e['id']);
            $type     = (string)$e['type'];
            $fileName = basename((string)$e['name']);

            if (!isset($folderMap[$type]) || $fileName === '') {
                $logger->warning('Type ou filename invalide (sequencing/drugs)', compact('strainId','type','fileName'));
                continue;
            }

            $source = realpath(sprintf('%s/%s/%s', $baseDir, $folderMap[$type], $fileName));
            if (!$source || !is_file($source)) {
                $logger->warning('Fichier introuvable (sequencing/drugs)', [
                    'path' => sprintf('%s/%s/%s', $baseDir, $folderMap[$type], $fileName)
                ]);
                continue;
            }

            if ($extensionFilter !== '') {
                $ext = '.' . strtolower(pathinfo($source, PATHINFO_EXTENSION));
                if ($ext !== strtolower($extensionFilter)) {
                    $logger->info('Filtré par extension (sequencing/drugs)', compact('fileName','ext','extensionFilter'));
                    continue;
                }
            }

            $newName = sprintf('%s_%s_%s', $strainId, $type, $fileName); // nom dans le ZIP
            $tmpPath = $tmpDir . '/' . $newName;

            if (!copy($source, $tmpPath)) {
                $logger->warning('Échec copie temporaire (sequencing/drugs)', compact('source','tmpPath'));
                continue;
            }

            $logger->info('Ajout sequencing/drugs', ['name' => $newName]);
            $tmpFiles[] = $tmpPath;
        }

        // --- 2) Traiter phenotype via BDD (filtre strainIds + phenotypeTypeIds) ---
        if (in_array('phenotype', $types, true)) {
            $strainIdsInt = array_values(array_filter(array_map('intval', (array)$strainIds), fn($v) => $v > 0));
            if (empty($strainIdsInt)) {
                $logger->warning('phenotype demandé mais aucun strainId reçu');
            } else {
                $ptIdsInt = array_values(array_filter(array_map('intval', (array)$phenotypeTypeIds), fn($v) => $v > 0));

                $qb = $phenotypeRepo->createQueryBuilder('p')
                    ->join('p.strain', 's')
                    ->join('p.phenotypeType', 'pt')
                    ->addSelect('s', 'pt')
                    ->andWhere('s.id IN (:sids)')->setParameter('sids', $strainIdsInt)
                    ->orderBy('s.id', 'ASC')
                    ->addOrderBy('pt.type', 'ASC');

                // ✅ NE filtrer/binder :ptids que si la liste n'est pas vide
                if (!empty($ptIdsInt)) {
                    $qb->andWhere('pt.id IN (:ptids)')
                       ->setParameter('ptids', $ptIdsInt);
                }

                $phenotypes = $qb->getQuery()->getResult();
                $logger->info('Phenotypes trouvés', ['count' => count($phenotypes)]);

                foreach ($phenotypes as $p) {
                    // Adapte ces getters à ton entité Phenotype
                    $strainId = (string)$p->getStrain()->getId();
                    $typeLbl  = (string)$p->getPhenotypeType()->getType(); // ex: "transformability"
                    $fileName = (string)$p->getFileName();

                    if ($fileName === '' || $fileName === '--') {
                        $logger->info('Phenotype sans fichier', ['strain' => $strainId, 'type' => $typeLbl]);
                        continue;
                    }

                    $source = realpath(sprintf('%s/%s/%s', $baseDir, $folderMap['phenotype'], $fileName));
                    if (!$source || !is_file($source)) {
                        $logger->warning('Fichier phenotype introuvable', [
                            'path' => sprintf('%s/%s/%s', $baseDir, $folderMap['phenotype'], $fileName),
                            'strain' => $strainId, 'type' => $typeLbl
                        ]);
                        continue;
                    }

                    if ($extensionFilter !== '') {
                        $ext = '.' . strtolower(pathinfo($source, PATHINFO_EXTENSION));
                        if ($ext !== strtolower($extensionFilter)) {
                            $logger->info('Filtré par extension (phenotype)', compact('fileName','ext','extensionFilter'));
                            continue;
                        }
                    }

                    // Nom dans le ZIP : id_phenotype_[type]_filename
                    $safeType = preg_replace('/[^A-Za-z0-9_\-]+/', '_', strtolower($typeLbl));
                    $newName  = sprintf('%s_phenotype_[%s]_%s', $strainId, $safeType, $fileName);
                    $tmpPath  = $tmpDir . '/' . $newName;

                    if (!copy($source, $tmpPath)) {
                        $logger->warning('Échec copie temporaire (phenotype)', compact('source','tmpPath'));
                        continue;
                    }

                    $logger->info('Ajout phenotype', ['name' => $newName]);
                    $tmpFiles[] = $tmpPath;
                }
            }
        }

        if (empty($tmpFiles)) {
            $logger->warning('Aucun fichier valide après filtrage (global)');
            return new Response('No valid files found for the given selection.', 400);
        }

        // --- 3) Créer le ZIP (zip binaire)
        $zipName    = 'files_' . date('Ymd_His') . '.zip';
        $tmpZipPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipName;

        $zipArgs   = array_map('escapeshellarg', $tmpFiles);
        $cmd       = sprintf('zip -j -q %s %s', escapeshellarg($tmpZipPath), implode(' ', $zipArgs));
        $output    = [];
        $returnVar = 0;
        $logger->debug('Commande ZIP', ['cmd' => $cmd]);
        exec($cmd, $output, $returnVar);

        // Nettoyage des fichiers temporaires (hors ZIP)
        foreach ($tmpFiles as $f) @unlink($f);
        @rmdir($tmpDir);

        if ($returnVar !== 0 || !file_exists($tmpZipPath)) {
            $logger->error('Échec création ZIP', ['returnVar' => $returnVar, 'output' => $output]);
            return new Response('Error creating ZIP archive.', 500);
        }

        $logger->info('ZIP créé avec succès', ['zip' => $tmpZipPath, 'count' => count($tmpFiles)]);

        return $this->file($tmpZipPath, $zipName)->deleteFileAfterSend(true);
    }
}
