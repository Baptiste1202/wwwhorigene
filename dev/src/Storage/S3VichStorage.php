<?php

namespace App\Storage;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Storage\AbstractStorage;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;

/**
 * Adaptateur personnalisé pour stocker les fichiers sur S3
 * tout en conservant la gestion des métadonnées par Vich
 */
class S3VichStorage extends AbstractStorage
{
    private S3Client $s3Client;
    private string $bucket;
    private ?LoggerInterface $logger;

    public function __construct(
        PropertyMappingFactory $factory,
        string $awsKey, 
        string $awsSecret, 
        string $awsRegion, 
        string $awsBucket,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($factory);
        
        $this->bucket = $awsBucket;
        $this->logger = $logger;
        
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => $awsRegion,
            'credentials' => [
                'key'    => $awsKey,
                'secret' => $awsSecret,
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function doUpload(PropertyMapping $mapping, File $file, ?string $dir, string $name): void
    {
        $key = $this->buildS3Key($mapping, $dir, $name);

        try {
            $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key'    => $key,
                'Body'   => fopen($file->getPathname(), 'rb'),
                'ContentType' => $file->getMimeType(),
                'ACL'    => 'private',
            ]);
            
            $this->logger?->info('Fichier uploadé sur S3', [
                'bucket' => $this->bucket,
                'key' => $key,
                'file' => $file->getFilename()
            ]);
        } catch (\Exception $e) {
            $this->logger?->error('Erreur upload S3', [
                'bucket' => $this->bucket,
                'key' => $key,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \RuntimeException('Impossible d\'uploader le fichier sur S3: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doRemove(PropertyMapping $mapping, ?string $dir, string $name): bool
    {
        try {
            $key = $this->buildS3Key($mapping, $dir, $name);
            
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $key,
            ]);
            
            $this->logger?->info('Fichier supprimé de S3', [
                'bucket' => $this->bucket,
                'key' => $key
            ]);
            
            return true;
        } catch (\Exception $e) {
            $this->logger?->error('Erreur suppression S3', [
                'bucket' => $this->bucket,
                'key' => $key ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * {@inheritdoc}
     * * Cette méthode est ajoutée pour permettre à Vich Uploader de récupérer le contenu
     * du fichier stocké sur S3 sous forme de stream PHP.
     * 
     * @return resource|null
     */
    protected function doResolveStream(PropertyMapping $mapping, ?string $dir, string $name)
    {
        $key = $this->buildS3Key($mapping, $dir, $name);

        try {
            // Utilise getObject pour récupérer le fichier de S3
            $result = $this->s3Client->getObject([
                'Bucket' => $this->bucket,
                'Key'    => $key,
            ]);

            $this->logger?->info('Récupération du flux S3 réussie', [
                'bucket' => $this->bucket,
                'key' => $key
            ]);

            // Le Body est une Psr\Http\Message\StreamInterface (de Guzzle), 
            // la méthode detach() retourne la ressource stream PHP native.
            $stream = $result['Body']->detach();

            return $stream ?: null;

        } catch (S3Exception $e) {
            // Gérer l'erreur 404 (fichier non trouvé) en retournant null, comme attendu par Vich
            if ($e->getStatusCode() === 404) {
                $this->logger?->warning('Fichier S3 non trouvé lors de la résolution du flux', [
                    'bucket' => $this->bucket,
                    'key' => $key,
                ]);
                return null;
            }

            $this->logger?->error('Erreur lors de la résolution du flux S3', [
                'bucket' => $this->bucket,
                'key' => $key,
                'error' => $e->getMessage()
            ]);

            throw new \RuntimeException('Impossible de récupérer le flux S3: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
             $this->logger?->error('Erreur générale lors de la résolution du flux S3', [
                'bucket' => $this->bucket,
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            throw new \RuntimeException('Erreur inattendue lors de la résolution du flux S3: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doResolvePath(PropertyMapping $mapping, ?string $dir, string $name, ?bool $relative = false): string
    {
        return $this->buildS3Key($mapping, $dir, $name);
    }

    private function buildS3Key(PropertyMapping $mapping, ?string $dir, string $name): string
    {
        $prefix = trim($mapping->getUriPrefix(), '/');
        
        if ($dir) {
            return sprintf('%s/%s/%s', $prefix, trim($dir, '/'), $name);
        }
        
        return sprintf('%s/%s', $prefix, $name);
    }

    public function getS3Client(): S3Client
    {
        return $this->s3Client;
    }

    public function getBucket(): string
    {
        return $this->bucket;
    }
}