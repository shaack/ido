#!/bin/bash

# Create certificate directory if it doesn't exist
mkdir -p certificate || { echo "Failed to create certificate directory"; exit 1; }

# Change to certificate directory
cd certificate || { echo "Failed to change to certificate directory"; exit 1; }

# Remove existing certificates if they exist
rm -rf *

# Generate new certificate
openssl genrsa -out private.key 2048
openssl req -x509 -newkey rsa:2048 -keyout private.key -out certificate.crt -days 365 -nodes \
-subj "/C=DE/ST=Niedersachsen/L=Celle/O=shaack.com/OU=IT/CN=localhost"
