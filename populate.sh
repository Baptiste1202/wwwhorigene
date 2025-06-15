#!/bin/bash

# Aller dans le dossier du projet (ajuste le chemin si besoin)
docker exec -it claranet2-app-1 bash

# Lancer la commande fos:elastica:populate
php bin/console fos:elastica:populate