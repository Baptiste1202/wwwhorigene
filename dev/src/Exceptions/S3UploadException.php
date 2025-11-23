<?php

namespace App\Exceptions;

/**
 * Exception spécifique levée lorsqu'une erreur survient
 * lors de l'interaction avec le service de stockage S3.
 */
class S3UploadException extends \RuntimeException
{
    /**
     * Crée une nouvelle S3UploadException.
     *
     * @param string $message Le message de l'erreur.
     * @param \Throwable|null $previous L'exception précédente (ex: S3Exception).
     */
    public function __construct(string $message = "Erreur lors de l'opération S3.", ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}