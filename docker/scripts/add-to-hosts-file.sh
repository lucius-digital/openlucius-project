#!/usr/bin/env bash
# This file must be run as sudo.

# Load environment variables
. .env > /dev/null

# Set host file location.
hosts=/etc/hosts

echo "# ${DOCKER_DOMAIN}" >> $hosts
echo "${HOST_IP}      ${DOCKER_DOMAIN}" >> $hosts
