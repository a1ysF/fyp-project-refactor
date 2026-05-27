#!/bin/bash
set -e

PORT="${PORT:-8080}"

sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Runtime guard: only mpm_prefork may be loaded (AH00534)
for mpm in mpm_event mpm_worker mpm_itk; do
    a2dismod -f "${mpm}" 2>/dev/null || true
    rm -f "/etc/apache2/mods-enabled/${mpm}.load" "/etc/apache2/mods-enabled/${mpm}.conf"
done
if [ ! -e /etc/apache2/mods-enabled/mpm_prefork.load ]; then
    a2enmod mpm_prefork
fi

exec apache2-foreground
