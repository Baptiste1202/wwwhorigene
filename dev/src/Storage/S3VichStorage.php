<?php

namespace App\Storage;

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