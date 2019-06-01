#!/usr/bin/env sh

# Wait for database
/scraper/docker/scripts/wait-for-it.sh ${MYSQL_HOST}:${MYSQL_PORT} -t 0

# Waiting for migrations #TODO: Better solution
echo "Waiting for migrations..."
sleep 5

# Run the scraper
/scraper/bin/scraper scrape
