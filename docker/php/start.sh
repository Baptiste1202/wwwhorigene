#!/bin/bash

# Lancer Elasticsearch en background
/opt/elasticsearch-7.17.16/bin/elasticsearch -d -p /var/run/elasticsearch.pid

# Lancer Apache en foreground
apache2-foreground