<?php 

// src/Service/S3FileCloner.php
namespace App\Service;

use App\Storage\S3VichStorage;
use Aws\S3\Exception\S3Exception;
use Psr\Log\LoggerInterface;

class S3FileCloner
{
    public function __construct(
        private S3VichStorage $s3Storage,
        private ?LoggerInterface $logger = null
    ) {
    }

    /**
     * Clone un fichier sur S3 et retourne le nouveau nom de fichier
     * 
     * @param string|null $originalFileName Le nom du fichier original
     * @param string $uriPrefix Le préfixe URI du mapping (ex: '/docs/phenotype')
     */
    public function cloneFile(?string $originalFileName, string $uriPrefix): ?string
    {
        if (!$originalFileName) {
            return null;
        }

        try {
            $s3Client = $this->s3Storage->getS3Client();
            $bucket = $this->s3Storage->getBucket();
            
            // Nettoyer le préfixe
            $prefix = trim($uriPrefix, '/');
            
            // Clé source (fichier original)
            $sourceKey = sprintf('%s/%s', $prefix, $originalFileName);
            
            // Extraire le nom de base et l'indice existant
            $pathInfo = pathinfo($originalFileName);
            $fullBaseName = $pathInfo['filename']; // ex: drug-69204118c117d304471563-2
            $extension = $pathInfo['extension'] ?? '';
            
            // Extraire le nom de base sans l'indice et l'indice actuel
            $baseNameWithoutIndex = $fullBaseName;
            $currentIndex = 0;
            
            // Vérifier si le nom se termine par "-chiffre"
            if (preg_match('/^(.+)-(\d+)$/', $fullBaseName, $matches)) {
                $baseNameWithoutIndex = $matches[1]; // ex: drug-69204118c117d304471563
                $currentIndex = (int)$matches[2];    // ex: 2
            }
            
            // Trouver le prochain indice disponible
            $counter = $currentIndex + 1;
            $newFileName = sprintf('%s-%d.%s', $baseNameWithoutIndex, $counter, $extension);
            $destinationKey = sprintf('%s/%s', $prefix, $newFileName);
            
            // Incrémenter jusqu'à trouver un nom disponible
            while ($this->fileExistsOnS3($bucket, $destinationKey)) {
                $counter++;
                $newFileName = sprintf('%s-%d.%s', $baseNameWithoutIndex, $counter, $extension);
                $destinationKey = sprintf('%s/%s', $prefix, $newFileName);
                
                // Sécurité : éviter une boucle infinie
                if ($counter > 1000) {
                    $this->logger?->error('Impossible de trouver un nom de fichier unique après 1000 tentatives', [
                        'original' => $originalFileName,
                        'prefix' => $prefix
                    ]);
                    return null;
                }
            }
            
            // Vérifier que le fichier source existe
            if (!$this->fileExistsOnS3($bucket, $sourceKey)) {
                $this->logger?->warning('Fichier source introuvable sur S3', [
                    'bucket' => $bucket,
                    'key' => $sourceKey
                ]);
                return null;
            }
            
            // Copier le fichier sur S3
            $s3Client->copyObject([
                'Bucket' => $bucket,
                'CopySource' => "{$bucket}/{$sourceKey}",
                'Key' => $destinationKey,
                'ACL' => 'private',
            ]);
            
            $this->logger?->info('Fichier dupliqué sur S3', [
                'bucket' => $bucket,
                'source' => $sourceKey,
                'destination' => $destinationKey,
                'base_name' => $baseNameWithoutIndex,
                'previous_index' => $currentIndex,
                'new_index' => $counter
            ]);
            
            return $newFileName;
            
        } catch (S3Exception $e) {
            $this->logger?->error('Erreur lors de la duplication du fichier S3', [
                'original' => $originalFileName,
                'uriPrefix' => $uriPrefix,
                'error' => $e->getMessage()
            ]);
            return null;
        } catch (\Exception $e) {
            $this->logger?->error('Erreur inattendue lors de la duplication', [
                'original' => $originalFileName,
                'uriPrefix' => $uriPrefix,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Vérifie si un fichier existe sur S3
     */
    private function fileExistsOnS3(string $bucket, string $key): bool
    {
        try {
            $this->s3Storage->getS3Client()->headObject([
                'Bucket' => $bucket,
                'Key' => $key,
            ]);
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }
    
    /**
     * Clone plusieurs fichiers en batch
     */
    public function cloneMultipleFiles(array $files, string $uriPrefix): array
    {
        $clonedFiles = [];
        
        foreach ($files as $originalFileName) {
            $newFileName = $this->cloneFile($originalFileName, $uriPrefix);
            if ($newFileName) {
                $clonedFiles[$originalFileName] = $newFileName;
            }
        }
        
        return $clonedFiles;
    }
}