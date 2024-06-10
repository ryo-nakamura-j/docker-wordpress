#!/bin/bash

# Define variables
SCRIPT=$(readlink -f $0)
SCRIPT_DIRECTORY=`dirname $SCRIPT`
ENV_FILE="${SCRIPT_DIRECTORY}/.env"
ENTRY="${SCRIPT_DIRECTORY}/.."
DOCKER_FILE="${SCRIPT_DIRECTORY}/docker-compose.yaml"
TEMP_DIRECTORY="${SCRIPT_DIRECTORY}/temp"

# Load environment variables
source ${SCRIPT_DIRECTORY}/.env

if [ -f ${TEMP_DIRECTORY}/dump.sql ]; then
    docker compose -f $DOCKER_FILE up -d

    echo "Insert database dump ... "
    cat ${TEMP_DIRECTORY}/dump.sql | docker-compose -f $DOCKER_FILE exec -T db mysql -u root -proot wordpress
    rm ${TEMP_DIRECTORY}/dump.sql

    # Replace domains in database e.g. mywebiste.com -> mywebsite.local
    docker-compose -f $DOCKER_FILE run --rm wp search-replace ${REMOTE_DOMAIN} ${LOCAL_DOMAIN} --all-tables --quiet --skip-themes --skip-plugins

    # Deactivate Plugins (if necessary)
    # docker-compose -f $DOCKER_FILE run --rm wp plugin dectivate autoptimize borlabs-cookie link-whisper-premium

    # Flush caches by using the WP-CLI
    echo "Delete Transients ..."
    docker-compose -f $DOCKER_FILE run wp transient delete --all --quiet --skip-themes --skip-plugins
    echo "Flush Cache ..."
    docker-compose -f $DOCKER_FILE run wp cache flush --quiet --skip-themes --skip-plugins

fi

# Finish
echo "Update finished. Your environment is now up to date with ${LOCAL_DOMAIN}"
