#!/usr/bin/env bash
#
# deploy.sh — lädt den ido-Code per SFTP zu shaack.com (Plesk-Hosting).
#
#   ./deploy.sh             Code hochladen
#   ./deploy.sh --dry-run   nichts übertragen, nur anzeigen
#
# Übertragen wird der Inhalt von web/ nach /httpdocs/ido.shaack.com.
# Mit hochgeladen werden vendor/ (CakePHP-Framework) und
# webroot/node_modules (Frontend-Assets), da beide zur Laufzeit
# gebraucht werden.
#
# Vor dem Upload wird vendor/ ohne Dev-Abhängigkeiten neu gebaut
# (composer install --no-dev), damit DebugKit, PHPUnit, Bake und
# CodeSniffer nicht auf dem Produktivserver landen. Danach wird der
# lokale Dev-Stand wiederhergestellt, auch bei Abbruch.
#
# Anschließend bootet der Prod-Build einmal im Container ido-web. Bricht
# das ab, wird nichts hochgeladen. Der Check fängt Pakete ab, die zur
# Laufzeit gebraucht werden, aber in require-dev stehen. Läuft der
# Container nicht, wird der Check übersprungen und das gemeldet.
#
# IMMER ausgenommen (serverseitig, dürfen NICHT überschrieben oder
# gelöscht werden):
#   - config/app_local.php   Produktiv-DB-Zugang + Security-Salt
#   - config/.env            serverseitige Umgebung
#   - logs/, tmp/            Laufzeitdaten
#   - .DS_Store
#
# Der Upload läuft mit --delete. Dateien, die es lokal nicht mehr gibt,
# werden auch auf dem Server gelöscht. Die oben ausgenommenen Pfade sind
# davon nicht betroffen, lftp nimmt sie komplett aus der Verarbeitung.
# Im Zweifel vorher ./deploy.sh --dry-run laufen lassen, das listet jede
# Löschung auf, ohne etwas anzufassen.
#
# Das SFTP-Passwort wird beim ersten Aufruf des Tages abgefragt und
# bis Tagesende im macOS-Schlüsselbund gemerkt. Vergessen mit:
#   security delete-generic-password -s ido-shaack-deploy
#
set -euo pipefail

# ---- Optionen ----------------------------------------------------
# --dry-run   nichts übertragen, nur anzeigen, was passieren würde
DRY_RUN=false
for arg in "$@"; do
  case "$arg" in
    --dry-run) DRY_RUN=true ;;
    *) echo "Unbekannte Option: $arg" >&2; exit 1 ;;
  esac
done

# ---- Zugang ------------------------------------------------------
FTP_HOST="shaack.com"
FTP_USER="hosting113435"

# Zielverzeichnis auf dem Server — das lokale web/ entspricht dem
# Docroot ido.shaack.com (web/.htaccess leitet auf webroot/ um).
REMOTE_DIR="/httpdocs/ido.shaack.com"

# ---- ins Projektverzeichnis --------------------------------------
cd "$(dirname "$0")"
LOCAL_DIR="web"

# Container aus compose/, in dem der Boot-Check läuft. Die lokale
# PHP-CLI taugt dafür nicht: sie ist zu neu für CakePHP 4.5.
SMOKE_CONTAINER="ido-web"

# ---- Voraussetzungen ---------------------------------------------
if ! command -v lftp >/dev/null 2>&1; then
  echo "Fehler: 'lftp' fehlt. Installieren mit:  brew install lftp" >&2
  exit 1
fi
if ! command -v composer >/dev/null 2>&1; then
  echo "Fehler: 'composer' fehlt." >&2
  exit 1
fi

# ---- Passwort: einmal pro Tag abfragen, dazwischen aus dem
#      macOS-Schlüsselbund holen ----------------------------------
# Das Passwort liegt verschlüsselt im Schlüsselbund (kein Klartext
# auf der Platte). Die Datei merkt sich nur das Datum der letzten
# Eingabe — ist es nicht von heute, wird neu gefragt.
KEYCHAIN_SERVICE="ido-shaack-deploy"
STAMP_FILE="${HOME}/.ido-deploy-passdate"
TODAY="$(date +%Y-%m-%d)"

LFTP_PASSWORD=""
if [ "$(cat "$STAMP_FILE" 2>/dev/null)" = "$TODAY" ]; then
  LFTP_PASSWORD="$(security find-generic-password -s "$KEYCHAIN_SERVICE" -a "$FTP_USER" -w 2>/dev/null || true)"
fi

if [ -z "$LFTP_PASSWORD" ]; then
  read -rsp "Passwort für ${FTP_USER}: " LFTP_PASSWORD
  echo
  if security add-generic-password -U -s "$KEYCHAIN_SERVICE" -a "$FTP_USER" \
       -w "$LFTP_PASSWORD" 2>/dev/null; then
    echo "$TODAY" > "$STAMP_FILE"
    echo "Passwort für heute gemerkt (macOS-Schlüsselbund)."
  fi
else
  echo "Passwort aus dem Schlüsselbund (heute schon eingegeben)."
fi
export LFTP_PASSWORD

# ---- Bestätigung -------------------------------------------------
echo
echo "Quelle : ${LOCAL_DIR}/  (vendor/ wird vorher ohne Dev-Pakete gebaut)"
echo "Ziel   : ${FTP_USER}@${FTP_HOST}:${REMOTE_DIR}"
echo "Ausgenommen: config/app_local.php, config/.env, logs/, tmp/, .DS_Store"
echo "Löschen: JA (--delete) — was lokal fehlt, verschwindet auch auf dem Server."
if [ "$DRY_RUN" = true ]; then
  echo "Modus  : DRY-RUN, es wird NICHTS übertragen und NICHTS gelöscht."
  DRY_FLAG="--dry-run"
else
  DRY_FLAG=""
fi

# ---- vendor/ für Produktion bauen --------------------------------
# Der Dev-Stand wird in jedem Fall wiederhergestellt, auch wenn der
# Upload abbricht oder das Skript per Ctrl-C beendet wird. Sonst stünde
# die lokale Entwicklungsumgebung ohne PHPUnit und DebugKit da.
TEMP_ENV=false
cleanup() {
  if [ "$TEMP_ENV" = true ]; then
    rm -f "${LOCAL_DIR}/config/.env"
    TEMP_ENV=false
  fi
  echo
  echo "› Lokales vendor/ wird wieder auf den Dev-Stand gebracht …"
  ( cd "$LOCAL_DIR" && composer install --no-interaction --quiet )
}
trap cleanup EXIT

echo "› vendor/ wird ohne Dev-Pakete gebaut …"
( cd "$LOCAL_DIR" && composer install --no-dev --optimize-autoloader --no-interaction --quiet )

# ---- Boot-Check --------------------------------------------------
# Fängt Pakete ab, die zur Laufzeit gebraucht werden, aber in
# require-dev stehen und im Prod-Build darum fehlen. Genau daran ist die
# Seite schon einmal gestorben (josegonzalez/dotenv). Der Dry-Run kann
# das nicht finden, er vergleicht nur Dateilisten und führt nichts aus.
#
# Auf dem Server liegt eine config/.env, und der Zweig in
# config/bootstrap.php hängt allein an deren Existenz. Für den Test wird
# deshalb eine angelegt, falls lokal keine da ist, sonst liefe genau der
# Codepfad nicht an, der den Ausfall verursacht hat.
if command -v podman >/dev/null 2>&1 && podman ps --format '{{.Names}}' 2>/dev/null | grep -qx "$SMOKE_CONTAINER"; then
  if [ ! -f "${LOCAL_DIR}/config/.env" ]; then
    cp "${LOCAL_DIR}/config/.env.example" "${LOCAL_DIR}/config/.env"
    TEMP_ENV=true
  fi

  echo "› Boot-Check des Prod-Builds …"
  if podman exec -w /var/www/html "$SMOKE_CONTAINER" \
       php -r 'require "vendor/autoload.php"; require "config/bootstrap.php";' >/dev/null 2>&1; then
    echo "  ok, der Prod-Build bootet."
  else
    echo >&2
    echo "ABBRUCH: Der Prod-Build bootet nicht." >&2
    echo "Vermutlich wird ein Paket zur Laufzeit gebraucht, das in require-dev" >&2
    echo "steht und deshalb bei --no-dev fehlt. Fehlermeldung im Klartext:" >&2
    echo >&2
    podman exec -w /var/www/html "$SMOKE_CONTAINER" \
      php -d display_errors=1 -r 'require "vendor/autoload.php"; require "config/bootstrap.php";' 2>&1 | head -3 >&2
    echo >&2
    echo "Es wurde nichts hochgeladen." >&2
    exit 1
  fi

  if [ "$TEMP_ENV" = true ]; then
    rm -f "${LOCAL_DIR}/config/.env"
    TEMP_ENV=false
  fi
else
  echo "Achtung: Container '${SMOKE_CONTAINER}' läuft nicht, Boot-Check übersprungen."
  echo "         Für einen abgesicherten Deploy vorher ./run.sh starten."
fi

# ---- Upload ------------------------------------------------------
echo "$([ "$DRY_RUN" = true ] && echo '› Dry-Run läuft …' || echo '› Upload läuft …')"
lftp -u "$FTP_USER" --env-password "sftp://$FTP_HOST" <<EOF
set sftp:auto-confirm yes
set net:timeout 20
set net:max-retries 3
mirror --reverse --verbose --delete ${DRY_FLAG} --parallel=4 --exclude ^config/app_local\.php\$ --exclude ^config/\.env\$ --exclude ^logs/ --exclude ^tmp/ --exclude \.DS_Store ${LOCAL_DIR}/ ${REMOTE_DIR}
bye
EOF

echo
echo "✓ Deploy abgeschlossen."
if [ "$DRY_RUN" = false ]; then
  echo
  echo "Denk an den Cache: tmp/ ist vom Upload ausgenommen, auf dem Server"
  echo "liegt also weiter der alte Schema- und Routen-Cache. Nach Änderungen"
  echo "am Schema oder nach einem Framework-Update in Plesk unter"
  echo "${REMOTE_DIR}/tmp/cache/ die Unterordner models/ und persistent/ leeren."
fi
