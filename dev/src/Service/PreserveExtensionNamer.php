<?php

namespace App\Service;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Naming\Polyfill\FileExtensionTrait;

/**
 * Namer personnalisé qui garantit la préservation de l'extension originale du fichier
 */
class PreserveExtensionNamer implements NamerInterface
{
    use FileExtensionTrait;

    public function name($object, PropertyMapping $mapping): string
    {
        $file = $mapping->getFile($object);
        
        // Récupère le nom original du fichier
        $originalName = $file->getClientOriginalName();
        
        // Extrait le nom sans l'extension
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Extrait l'extension originale
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        
        // Génère un identifiant unique
        $uniqueId = uniqid('', true);
        
        // Nettoie le nom de base (supprime les caractères spéciaux)
        $baseName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $baseName);
        $baseName = preg_replace('/_+/', '_', $baseName); // Remplace les underscores multiples par un seul
        $baseName = trim($baseName, '_'); // Supprime les underscores au début/fin
        
        // Retourne le nom avec la structure : baseName_uniqueId.extension
        if ($extension) {
            return sprintf('%s_%s.%s', $baseName, $uniqueId, $extension);
        }
        
        return sprintf('%s_%s', $baseName, $uniqueId);
    }
}
