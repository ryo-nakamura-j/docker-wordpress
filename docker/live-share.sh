#!/bin/bash

# Define variables
SCRIPT=$(readlink -f $0)
SCRIPT_DIRECTORY=`dirname $SCRIPT`

# Load environment variables
source ${SCRIPT_DIRECTORY}/.env

# Set path to ngrok config file
if [ "$OSTYPE" == 'linux-gnu' ]; then
	# Linux
    NGROK_CONFIG_PATH=~/.config/ngrok/ngrok.yml
else
	# macOS
	NGROK_CONFIG_PATH=~/Library/Application\ Support/ngrok/ngrok.yml
fi

# Extract ngrok auth token from config file
NGROK_AUTH_TOKEN=`awk '$1=="authtoken:"{print $2}' "${NGROK_CONFIG_PATH}"`

# Run ngrok
docker run --net=host -it -e NGROK_AUTHTOKEN=${NGROK_AUTH_TOKEN} ngrok/ngrok:latest http ${LOCAL_DOMAIN}:443
