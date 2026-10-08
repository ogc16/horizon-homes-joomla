#!/usr/bin/env bash
#
# Horizon Homes entrypoint.
#
# 1. Populate /var/www/html with the Joomla core (from /usr/src/joomla).
# 2. Sync the custom component / template / assets from /usr/src/custom.
# 3. Drop the web installer (we restore a pre-built database instead).
# 4. Generate configuration.php from env vars (only on first boot).
# 5. Wait for the database, fix permissions and start Apache.
#
# Step 2 runs on every boot because the base image mounts /var/www/html as a
# volume, which would otherwise hide (and outlive) the files baked into layers.
#
set -euo pipefail

WEBROOT="/var/www/html"
CUSTOM="/usr/src/custom"
cd "$WEBROOT"

DB_HOST="${JOOMLA_DB_HOST:-db}"
DB_USER="${JOOMLA_DB_USER:-joomla}"
DB_PASSWORD="${JOOMLA_DB_PASSWORD:-}"
DB_NAME="${JOOMLA_DB_NAME:-joomla}"

# --- 1. Joomla core ---------------------------------------------------------
if [ ! -e index.php ] && [ ! -e libraries/src/Version.php ]; then
    echo "[entrypoint] Copying Joomla core into ${WEBROOT} ..."
    tar --create --file - --directory /usr/src/joomla --one-file-system . \
        | tar --extract --file -
fi

# --- 2. Custom files --------------------------------------------------------
if [ -d "$CUSTOM" ]; then
    echo "[entrypoint] Syncing custom component, template and assets ..."

    # Component — replace wholesale so stale layouts from older images vanish.
    rm -rf "$WEBROOT/components/com_estate" \
           "$WEBROOT/administrator/components/com_estate"
    mkdir -p "$WEBROOT/components/com_estate" \
             "$WEBROOT/administrator/components/com_estate"
    cp -a "$CUSTOM/components/com_estate/."                "$WEBROOT/components/com_estate/"
    cp -a "$CUSTOM/administrator/components/com_estate/."  "$WEBROOT/administrator/components/com_estate/"

    # Template — replace wholesale.
    rm -rf "$WEBROOT/templates/hornbill"
    cp -a "$CUSTOM/templates/hornbill" "$WEBROOT/templates/hornbill"

    # Images / media — merge, keeping anything the core contributed.
    mkdir -p "$WEBROOT/images" "$WEBROOT/media"
    cp -a "$CUSTOM/images/." "$WEBROOT/images/"
    cp -a "$CUSTOM/media/."  "$WEBROOT/media/"
fi

# The PSR-4 namespace map is a cache derived from the on-disk manifests. Drop
# it so it is rebuilt against the files we just synced (otherwise a map cached
# by an earlier/older layout persists in the webroot volume and the component
# namespace is never registered).
rm -f "$WEBROOT/administrator/cache/autoload_psr4.php" \
      "$WEBROOT/cache/autoload_psr4.php"

# --- 3. No web installer ----------------------------------------------------
rm -rf "${WEBROOT}/installation"

# --- 4. configuration.php ---------------------------------------------------
if [ ! -f "${WEBROOT}/configuration.php" ]; then
    echo "[entrypoint] Generating configuration.php ..."
    php -r '
        $tpl    = file_get_contents("/usr/local/share/joomla/configuration.php.template");
        $secret = bin2hex(random_bytes(16));
        $map    = [
            "__DB_HOST__"     => getenv("JOOMLA_DB_HOST") ?: "db",
            "__DB_USER__"     => getenv("JOOMLA_DB_USER") ?: "joomla",
            "__DB_PASSWORD__" => getenv("JOOMLA_DB_PASSWORD") ?: "",
            "__DB_NAME__"     => getenv("JOOMLA_DB_NAME") ?: "joomla",
            "__SECRET__"      => $secret,
        ];
        file_put_contents(
            "/var/www/html/configuration.php",
            str_replace(array_keys($map), array_values($map), $tpl)
        );
    '
fi

# --- 5. Wait for the database ----------------------------------------------
echo "[entrypoint] Waiting for database at ${DB_HOST} ..."
db_ready=0
for _ in $(seq 1 60); do
    if php -r '
        exit(@mysqli_connect(
            getenv("JOOMLA_DB_HOST") ?: "db",
            getenv("JOOMLA_DB_USER") ?: "joomla",
            getenv("JOOMLA_DB_PASSWORD") ?: "",
            getenv("JOOMLA_DB_NAME") ?: "joomla"
        ) ? 0 : 1);
    '; then
        db_ready=1
        break
    fi
    sleep 2
done
if [ "$db_ready" -ne 1 ]; then
    echo "[entrypoint] WARNING: database not reachable after 120s; starting anyway."
fi

# --- 6. Permissions + launch ------------------------------------------------
mkdir -p "$WEBROOT/tmp" "$WEBROOT/cache" \
         "$WEBROOT/administrator/cache" "$WEBROOT/administrator/logs"
chown -R www-data:www-data "$WEBROOT/tmp" "$WEBROOT/cache" \
        "$WEBROOT/administrator/cache" "$WEBROOT/administrator/logs" \
        "$WEBROOT/configuration.php"

echo "[entrypoint] Starting Apache ..."
exec "$@"