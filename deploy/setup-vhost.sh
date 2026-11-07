#!/usr/bin/env bash
#
# Registers the ppd.local Nginx vhost and /etc/hosts entry.
# Run with: sudo bash deploy/setup-vhost.sh
#
set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DOMAIN="ppd.local"
AVAILABLE="/etc/nginx/sites-available/${DOMAIN}"
ENABLED="/etc/nginx/sites-enabled/${DOMAIN}"

if [[ $EUID -ne 0 ]]; then
    echo "Please run as root: sudo bash deploy/setup-vhost.sh" >&2
    exit 1
fi

cp "${PROJECT_DIR}/deploy/${DOMAIN}.conf" "${AVAILABLE}"
ln -sfn "${AVAILABLE}" "${ENABLED}"

if ! grep -qE "^[^#]*\s${DOMAIN}(\s|$)" /etc/hosts; then
    echo "127.0.0.1 ${DOMAIN}" >> /etc/hosts
fi

nginx -t
nginx -s reload

echo "Vhost ready: http://${DOMAIN}"
