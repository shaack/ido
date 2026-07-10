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
# IMMER ausgenommen (serverseitig, dürfen NICHT überschrieben werden):
#   - config/app_local.php   Produktiv-DB-Zugang + Security-Salt
#   - config/.env            serverseitige Umgebung
#   - logs/, tmp/            Laufzeitdaten
#   - .DS_Store
#
# Es werden neue und geänderte Dateien hochgeladen. Auf dem Server
# verwaiste Dateien werden NICHT gelöscht (kein --delete).
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

# ---- Voraussetzungen ---------------------------------------------
if ! command -v lftp >/dev/null 2>&1; then
  echo "Fehler: 'lftp' fehlt. Installieren mit:  brew install lftp" >&2
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
echo "Quelle : ${LOCAL_DIR}/"
echo "Ziel   : ${FTP_USER}@${FTP_HOST}:${REMOTE_DIR}"
echo "Ausgenommen: config/app_local.php, config/.env, logs/, tmp/, .DS_Store"
if [ "$DRY_RUN" = true ]; then
  echo "Modus  : DRY-RUN — es wird NICHTS übertragen, nur angezeigt."
  DRY_FLAG="--dry-run"
else
  DRY_FLAG=""
fi
read -rp "$([ "$DRY_RUN" = true ] && echo 'Simulieren?' || echo 'Hochladen?') [j/N] " ANSWER
[ "${ANSWER:-}" = "j" ] || { echo "Abgebrochen."; exit 0; }

# ---- Upload ------------------------------------------------------
echo "$([ "$DRY_RUN" = true ] && echo '› Dry-Run läuft …' || echo '› Upload läuft …')"
lftp -u "$FTP_USER" --env-password "sftp://$FTP_HOST" <<EOF
set sftp:auto-confirm yes
set net:timeout 20
set net:max-retries 3
mirror --reverse --verbose ${DRY_FLAG} --parallel=4 --exclude ^config/app_local\.php\$ --exclude ^config/\.env\$ --exclude ^logs/ --exclude ^tmp/ --exclude \.DS_Store ${LOCAL_DIR}/ ${REMOTE_DIR}
bye
EOF

echo
echo "✓ Deploy abgeschlossen."
