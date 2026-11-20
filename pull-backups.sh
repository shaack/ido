#!/usr/bin/env bash
#
# pull-backups.sh — holt die Datenbank-Sicherungen von ido.shaack.com auf
# diesen Rechner, damit es eine Kopie außerhalb des Servers gibt.
#
#   ./pull-backups.sh
#
# Ziel ist ~/Shared/Work/Projects/Ido/backups/, außerhalb des Repos und damit
# über bisync-shared gesichert. Geholt werden nur Dateien, die dort noch
# fehlen. Lokal wird nichts gelöscht, auch wenn der Server alte Sicherungen
# abräumt.
#
# Zugang über den SSH-Alias ghv.shaack.com (Schlüssel, kein Passwort). Auf
# dem Server gibt es kein rsync, daher ssh + scp.
#
set -euo pipefail

SSH_HOST="ghv.shaack.com"
REMOTE_DIR="httpdocs/ido.shaack.com/db-backups"
LOCAL_DIR="${HOME}/Shared/Work/Projects/Ido/backups"

mkdir -p "$LOCAL_DIR"

missing=()
while IFS= read -r name; do
  [ -f "$LOCAL_DIR/$name" ] || missing+=("$name")
done < <(ssh "$SSH_HOST" "cd $REMOTE_DIR 2>/dev/null && ls -1 ido-*.sql.gz 2>/dev/null" || true)

if [ ${#missing[@]} -eq 0 ]; then
  echo "Nichts Neues, alle Sicherungen liegen schon in $LOCAL_DIR."
  exit 0
fi

for name in "${missing[@]}"; do
  scp -q "$SSH_HOST:$REMOTE_DIR/$name" "$LOCAL_DIR/$name.part"
  mv "$LOCAL_DIR/$name.part" "$LOCAL_DIR/$name"
  echo "  $name"
done
echo "✓ ${#missing[@]} Sicherung(en) nach $LOCAL_DIR geholt."
