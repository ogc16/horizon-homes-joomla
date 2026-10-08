# syntax=docker/dockerfile:1
#
# Horizon Homes — Joomla 6 real-estate site
#
# Built on the official Joomla image (PHP 8.3 + Apache + all required PHP
# extensions). The base image declares /var/www/html as a VOLUME, so custom
# files are staged under /usr/src/custom and synced into the webroot by the
# entrypoint on every boot (this makes image rebuilds deterministic even when
# Docker reuses the webroot volume).
#
# The database is restored from a pre-built dump (docker/mysql/initdb) so the
# site boots with its full working state: menus, template assignment,
# #__estate_* tables and seed data.
FROM joomla:6.1-php8.3-apache

LABEL org.opencontainers.image.title="Horizon Homes" \
      org.opencontainers.image.description="Joomla 6 real-estate site (com_estate + hornbill template)" \
      org.opencontainers.image.licenses="MIT"

# --- Custom component (staged) ---------------------------------------------
# git repo mirrors the manifest layout (site/ + admin/). Joomla installs the
# component split across two trees with site files flattened to the root of
# components/com_estate, and admin files under administrator/components.
COPY components/com_estate/site/      /usr/src/custom/components/com_estate/
COPY components/com_estate/sql/       /usr/src/custom/components/com_estate/sql/
COPY components/com_estate/estate.xml /usr/src/custom/components/com_estate/estate.xml

COPY components/com_estate/admin/     /usr/src/custom/administrator/components/com_estate/
COPY components/com_estate/estate.xml /usr/src/custom/administrator/components/com_estate/estate.xml

# --- Custom template + assets (staged) -------------------------------------
COPY templates/hornbill/ /usr/src/custom/templates/hornbill/
COPY images/             /usr/src/custom/images/
COPY media/              /usr/src/custom/media/

# --- Boot configuration -----------------------------------------------------
COPY docker/configuration.php.template /usr/local/share/joomla/configuration.php.template
COPY docker/entrypoint.sh              /usr/local/bin/joomla-entrypoint.sh
RUN chmod +x /usr/local/bin/joomla-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/joomla-entrypoint.sh"]
CMD ["apache2-foreground"]

EXPOSE 80