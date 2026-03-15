#!/bin/bash
mkdir -p ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout ssl/nginx.key -out ssl/nginx.crt \
  -subj "/C=ES/ST=Barcelona/L=Barcelona/O=Pokegame/OU=IT/CN=localhost"

echo "Certificados generados en la carpeta ./ssl"
