#!/usr/bin/env sh

# Wait for database
/migrator/.docker/scripts/wait-for-it.sh ${MYSQL_HOST}:${MYSQL_PORT} -t 0

# Run migrations
/migrator/vendor/bin/phinx migrate
