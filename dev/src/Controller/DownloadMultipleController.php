<?php
// src/Controller/DownloadMultipleController.php
namespace App\Controller;

use App\Entity\MethodSequencing;
use App\Entity\DrugResistanceOnStrain;
use App\Entity\Phenotype;
use App\Storage\S3VichStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use ZipStream\ZipStream;

class DownloadMultipleController extends AbstractController
{
    private EntityManagerInterface $em;
    private \Vich\UploaderBundle\Storage\StorageInterface $storage;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        \Vich\UploaderBundle\Storage\StorageInterface $storage,
        LoggerInterface $logger
    ) {
        if (!$storage instanceof S3VichStorage) {
            throw new \RuntimeException('Le storage Vich doit être S3VichStorage');
        }
        
        $this->em = $em;
        $this->storage = $storage;
        $this->s3Storage = $storage; // ⚠️ AJOUT : Initialiser s3Storage
        $this->logger = $logger;
    }

    #[Route('/download-multiple', name: 'download_multiple', methods: ['POST'])]
    public function downloadMultiple(Request $request): StreamedResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new BadRequestHttpException('Invalid JSON: ' . json_last_error_msg());
            }

            // Valider et nettoyer les entrées
            $validated = $this->validateAndSanitizeInput($data);
            
            $this->logger->info('Download request received', [
                'strainIds' => $validated['strainIds'],
                'types' => $validated['types'],
                'phenotypeTypeIds' => $validated['phenotypeTypeIds'],
                'extension' => $validated['extension'],
            ]);

            if (empty($validated['strainIds'])) {
                throw new BadRequestHttpException('No valid strain IDs provided');
            }

            if (empty($validated['types'])) {
                throw new BadRequestHttpException('No valid file types selected');
            }

            $zipFilename = 'export_' . date('Ymd_His') . '.zip';

            $response = new StreamedResponse(
                function () use ($validated) {
                    $this->generateZip(
                        $validated['strainIds'],
                        $validated['types'],
                        $validated['phenotypeTypeIds'],
                        $validated['extension']
                    );
                }
            );

            $response->headers->set('Content-Type', 'application/zip');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $zipFilename . '"');
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');

            return $response;

        } catch (BadRequestHttpException $e) {
            $this->logger->warning('Bad request in download', ['error' => $e->getMessage()]);
            return new StreamedResponse(
                fn() => print($e->getMessage()),
                400,
                ['Content-Type' => 'text/plain']
            );
        } catch (\Exception $e) {
            $this->logger->error('Error in download', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return new StreamedResponse(
                fn() => print('Server error: ' . $e->getMessage()),
                500,
                ['Content-Type' => 'text/plain']
            );
        }
    }

    private function generateZip(
        array $strainIds,
        array $types,
        array $phenotypeTypeIds,
        string $extension
    ): void {
        // ZipStream v3 avec arguments nommés
        // IMPORTANT: sendHttpHeaders DOIT être FALSE car StreamedResponse gère les headers
        $zip = new ZipStream(
            sendHttpHeaders: false,
            defaultDeflateLevel: 6,
            comment: 'Export from Strain Database',
        );
        
        $addedFiles = [];
        $totalFiles = 0;

        try {
            // 1. Traiter les fichiers de séquençage
            if (in_array('sequencing', $types, true)) {
                $count = $this->addSequencingFiles($zip, $strainIds, $addedFiles);
                $totalFiles += $count;
                $this->logger->info("Added $count sequencing files");
            }

            // 2. Traiter les fichiers de résistance aux drogues
            if (in_array('drugs', $types, true)) {
                $count = $this->addDrugFiles($zip, $strainIds, $addedFiles);
                $totalFiles += $count;
                $this->logger->info("Added $count drug resistance files");
            }

            // 3. Traiter les fichiers phenotype
            if (in_array('phenotype', $types, true)) {
                $count = $this->addPhenotypeFiles($zip, $strainIds, $phenotypeTypeIds, $extension, $addedFiles);
                $totalFiles += $count;
                $this->logger->info("Added $count phenotype files");
            }

            if ($totalFiles === 0) {
                $this->logger->warning('No files were added to the ZIP');
                // Ajouter un fichier README pour éviter un ZIP vide
                $zip->addFile('README.txt', 'No files matched your selection criteria.');
            }

        } catch (\Exception $e) {
            $this->logger->error('Error during ZIP generation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Ajouter un fichier d'erreur dans le ZIP
            $zip->addFile('ERROR.txt', 'An error occurred during export: ' . $e->getMessage());
        } finally {
            $zip->finish();
            $this->logger->info('ZIP generation completed', ['totalFiles' => $totalFiles]);
        }
    }

    private function addSequencingFiles(
        ZipStream $zip,
        array $strainIds,
        array &$addedFiles
    ): int {
        $count = 0;
        
        try {
            $this->logger->info('[DEBUG] Starting sequencing file search', [
                'strainIds' => $strainIds
            ]);

            // Récupérer tous les fichiers de séquençage pour ces souches
            $sequencings = $this->em->getRepository(MethodSequencing::class)
                ->createQueryBuilder('s')
                ->innerJoin('s.strain', 'st')
                ->where('st.id IN (:strainIds)')
                ->andWhere('s.nameFile IS NOT NULL')
                ->andWhere("s.nameFile != ''")
                ->setParameter('strainIds', $strainIds)
                ->getQuery()
                ->getResult();

            $this->logger->info('[DEBUG] Sequencing files found', [
                'count' => count($sequencings),
                'strainIds' => $strainIds
            ]);

            foreach ($sequencings as $sequencing) {
                $this->logger->info('[DEBUG] Processing sequencing', [
                    'id' => $sequencing->getId(),
                    'nameFile' => $sequencing->getNameFile(),
                    'strainId' => $sequencing->getStrain() ? $sequencing->getStrain()->getId() : null
                ]);
                
                if ($this->addEntityFileToZip($zip, $sequencing, 'sequencing', $addedFiles)) {
                    $count++;
                }
            }

        } catch (\Exception $e) {
            $this->logger->error('Error processing sequencing files', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $count;
    }

    private function addDrugFiles(
        ZipStream $zip,
        array $strainIds,
        array &$addedFiles
    ): int {
        $count = 0;
        
        try {
            // Récupérer tous les fichiers de drug resistance pour ces souches
            $drugs = $this->em->getRepository(DrugResistanceOnStrain::class)
                ->createQueryBuilder('d')
                ->innerJoin('d.strain', 's')
                ->where('s.id IN (:strainIds)')
                ->andWhere('d.nameFile IS NOT NULL')
                ->andWhere("d.nameFile != ''")
                ->setParameter('strainIds', $strainIds)
                ->getQuery()
                ->getResult();

            $this->logger->info('Drug resistance files found', ['count' => count($drugs)]);

            foreach ($drugs as $drug) {
                if ($this->addEntityFileToZip($zip, $drug, 'drugs', $addedFiles)) {
                    $count++;
                }
            }

        } catch (\Exception $e) {
            $this->logger->error('Error processing drug files', [
                'error' => $e->getMessage()
            ]);
        }

        return $count;
    }

    private function addPhenotypeFiles(
        ZipStream $zip,
        array $strainIds,
        array $phenotypeTypeIds,
        string $extension,
        array &$addedFiles
    ): int {
        $count = 0;
        
        try {
            $this->logger->info('[DEBUG] Starting phenotype file search', [
                'strainIds' => $strainIds,
                'phenotypeTypeIds' => $phenotypeTypeIds
            ]);

            $qb = $this->em->getRepository(Phenotype::class)->createQueryBuilder('p')
                ->innerJoin('p.strain', 's')
                ->where('s.id IN (:strainIds)')
                ->andWhere('p.fileName IS NOT NULL')
                ->andWhere("p.fileName != ''")
                ->setParameter('strainIds', $strainIds);

            if (!empty($phenotypeTypeIds)) {
                $qb->innerJoin('p.phenotypeType', 't')
                   ->andWhere('t.id IN (:typeIds)')
                   ->setParameter('typeIds', $phenotypeTypeIds);
            }

            $phenotypes = $qb->getQuery()->getResult();

            $this->logger->info('[DEBUG] Phenotype files found', [
                'count' => count($phenotypes),
                'strainIds' => $strainIds,
                'typeIds' => $phenotypeTypeIds
            ]);

            foreach ($phenotypes as $phenotype) {
                try {
                    $this->logger->info('[DEBUG] Processing phenotype', [
                        'id' => $phenotype->getId(),
                        'fileName' => $phenotype->getFileName()
                    ]);

                    // Récupérer le nom du fichier
                    $filename = $phenotype->getFileName();
                    
                    if (!$filename) {
                        $this->logger->warning('[DEBUG] No filename for phenotype', [
                            'phenotypeId' => $phenotype->getId()
                        ]);
                        continue;
                    }

                    // Ajouter l'extension si fournie et si le fichier ne l'a pas déjà
                    if ($extension && !str_ends_with($filename, $extension)) {
                        $filename .= $extension;
                    }

                    // Construire la clé S3 directement
                    $s3Key = sprintf('docs/phenotype/%s', $phenotype->getFileName());
                    
                    $this->logger->info('[DEBUG] Attempting to get S3 phenotype', ['s3Key' => $s3Key]);

                    // Récupérer le stream depuis S3 directement
                    try {
                        $result = $this->s3Storage->getS3Client()->getObject([
                            'Bucket' => $this->s3Storage->getBucket(),
                            'Key'    => $s3Key,
                        ]);
                        
                        $stream = $result['Body']->detach();
                        
                        if (!$stream || !is_resource($stream)) {
                            $this->logger->error('[DEBUG] Phenotype stream is not valid', [
                                'stream_is_null' => $stream === null,
                                'is_resource' => is_resource($stream)
                            ]);
                            continue;
                        }

                        $this->logger->info('[DEBUG] S3 phenotype stream resolved successfully', ['s3Key' => $s3Key]);

                    } catch (\Aws\S3\Exception\S3Exception $e) {
                        $this->logger->error('[DEBUG] S3 Exception for phenotype', [
                            's3Key' => $s3Key,
                            'error' => $e->getMessage(),
                            'code' => $e->getStatusCode()
                        ]);
                        continue;
                    }

                    $zipPath = $this->buildZipPath('phenotype', $filename);

                    if ($this->addEntityFileToZip($zip, $phenotype, 'phenotype', $addedFiles)) {
                        $count++;
                    }

                } catch (\Exception $e) {
                    $this->logger->error('[DEBUG] Error adding phenotype file', [
                        'phenotypeId' => $phenotype->getId(),
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    continue;
                }
            }

        } catch (\Exception $e) {
            $this->logger->error('[DEBUG] Error processing phenotypes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $count;
    }

    /**
     * Ajoute le fichier d'une entité au ZIP
     * Retourne true si le fichier a été ajouté avec succès
     */
    private function addEntityFileToZip(
        ZipStream $zip,
        object $entity,
        string $type,
        array &$addedFiles
    ): bool {
        try {
            // 1️⃣ Récupération du nom original
            $originalFilename = null;
            if (method_exists($entity, 'getNameFile')) {
                $originalFilename = $entity->getNameFile();
            } elseif (method_exists($entity, 'getFileName')) {
                $originalFilename = $entity->getFileName();
            }

            if (!$originalFilename) {
                return false;
            }

            // 2️⃣ Récupérer le fichier S3
            $s3Key = sprintf('docs/%s/%s', $type, $originalFilename);

            try {
                $result = $this->s3Storage->getS3Client()->getObject([
                    'Bucket' => $this->s3Storage->getBucket(),
                    'Key'    => $s3Key,
                ]);

                $stream = $result['Body']->detach();

            } catch (\Exception $e) {
                return false;
            }

            // 3️⃣ ID de souche (si dispo)
            $strainId = null;
            if (method_exists($entity, 'getStrain') && $entity->getStrain()) {
                $strainId = $entity->getStrain()->getId();
            }

            // 4️⃣ TYPE DU PHENOTYPE pour ajout dans le nom de fichier
            $phenotypeTypeText = null;
            if ($type === 'phenotype' && method_exists($entity, 'getPhenotypeType')) {
                $pt = $entity->getPhenotypeType();
                if ($pt && method_exists($pt, 'getType')) {
                    $phenotypeTypeText = $pt->getType();  // ex: transformability
                }
            }

            // 5️⃣ Construction du nom final
            $prefixParts = [];

            if ($strainId) {
                $prefixParts[] = 'ID_' . $strainId;
            }

            if ($type === 'phenotype' && $phenotypeTypeText) {
                // Nettoyage nom (sécurité)
                $safeType = preg_replace('/[^a-zA-Z0-9_-]/', '_', $phenotypeTypeText);
                $prefixParts[] = 'TYPE_' . $safeType;
            }

            $finalFilename = !empty($prefixParts)
                ? implode('_', $prefixParts) . '_' . $originalFilename
                : $originalFilename;

            // 6️⃣ Chemin dans le ZIP
            $zipPath = $this->buildZipPath($type, $finalFilename);

            // Éviter doublons
            if (in_array($zipPath, $addedFiles)) {
                if (is_resource($stream)) fclose($stream);
                return false;
            }

            // 7️⃣ Ajout ZIP
            $zip->addFileFromStream($zipPath, $stream);
            $addedFiles[] = $zipPath;

            if (is_resource($stream)) fclose($stream);

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Construit le chemin du fichier dans le ZIP avec nettoyage sécurisé
     */
    private function buildZipPath(string $type, string $filename): string
    {
        $folderName = match ($type) {
            'sequencing' => 'Sequencing',
            'drugs' => 'Drug_Resistance',
            'phenotype' => 'Phenotype',
            default => 'Other',
        };

        // Nettoyer le nom de fichier de manière sécurisée
        // 1. Supprimer les path traversal
        $filename = basename($filename);
        
        // 2. Remplacer les caractères dangereux
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // 3. Éviter les noms vides ou dangereux
        if (empty($filename) || $filename === '.' || $filename === '..') {
            $filename = 'file_' . uniqid() . '.dat';
        }
        
        // 4. Limiter la longueur
        if (strlen($filename) > 200) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $name = substr(pathinfo($filename, PATHINFO_FILENAME), 0, 190);
            $filename = $ext ? "{$name}.{$ext}" : $name;
        }
        
        return sprintf('%s/%s', $folderName, $filename);
    }

    /**
     * Valide et nettoie les données d'entrée
     */
    private function validateAndSanitizeInput(array $data): array
    {
        // Valider les strain IDs (entiers positifs uniquement)
        $strainIds = array_values(array_filter(
            $data['strainIds'] ?? [],
            fn($id) => is_numeric($id) && $id > 0 && $id < PHP_INT_MAX
        ));
        
        // Whitelist des types autorisés
        $allowedTypes = ['sequencing', 'drugs', 'phenotype'];
        $types = array_values(array_filter(
            $data['types'] ?? [],
            fn($type) => is_string($type) && in_array($type, $allowedTypes, true)
        ));
        
        // Nettoyer l'extension (seulement alphanumériques et point)
        $extension = preg_replace('/[^a-zA-Z0-9.]/', '', $data['extension'] ?? '');
        if ($extension && !str_starts_with($extension, '.')) {
            $extension = '.' . $extension;
        }
        // Limiter la longueur de l'extension
        $extension = substr($extension, 0, 10);
        
        // Valider les phenotype type IDs
        $phenotypeTypeIds = array_values(array_filter(
            $data['phenotypeTypeIds'] ?? [],
            fn($id) => is_numeric($id) && $id > 0 && $id < PHP_INT_MAX
        ));
        
        return [
            'strainIds' => $strainIds,
            'types' => $types,
            'extension' => $extension,
            'phenotypeTypeIds' => $phenotypeTypeIds,
        ];
    }
}