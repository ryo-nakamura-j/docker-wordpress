#!/bin/bash

# Define variables
SCRIPT=$(readlink -f $0)
SCRIPT_DIRECTORY=`dirname $SCRIPT`
ENV_FILE=${SCRIPT_DIRECTORY}/.env
DOCKER_FILE=${SCRIPT_DIRECTORY}/docker-compose.yaml

# Load environment variables
source ${SCRIPT_DIRECTORY}/.env

# Create certificates
echo "Creating certificates ... "
mkcert -ecdsa -cert-file ${SCRIPT_DIRECTORY}/nginx/certs/${LOCAL_DOMAIN}.pem -key-file ${SCRIPT_DIRECTORY}/nginx/certs/${LOCAL_DOMAIN}-key.pem ${LOCAL_DOMAIN} 127.0.0.1 ::1

# Add entry to /etc/hosts if it doesn't exist yet
echo "Write domain into /etc/hosts ..."
if ! grep -q "127.0.0.1 ${LOCAL_DOMAIN}" /etc/hosts; then
    echo "Please provide your sudo password :)"
    echo "127.0.0.1 ${LOCAL_DOMAIN}" | sudo tee -a /etc/hosts > /dev/null
else
    echo "Already set"
fi

# Purge all docker containers
echo "Purge all docker containers"
docker rm -fv $(docker ps -aq)

# Run docker compose
echo "Start Docker Containers"
docker compose -f $DOCKER_FILE up --no-recreate --remove-orphans --build

# ! Specific step for linux systems
if [ "$OSTYPE" == 'linux-gnu' ]; then
	cd ${SCRIPT_DIRECTORY}/../
	var=$(pwd)
	mydir="$(basename $PWD)"
	
	sudo chmod 777 -R ./../${mydir}/
	
	echo "Set trusted ownership"
	git config --global --add safe.directory ${SCRIPT_DIRECTORY}/..

	git reset HEAD --hard
fi
