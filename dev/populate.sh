#!/bin/bash
set -e

echo "Démarrage de l'application..."

# Vérifier si Elasticsearch est accessible
echo "Vérification d'Elasticsearch..."
php bin/console fos:elastica:populate --quiet || echo "Erreur lors de la population Elasticsearch"
