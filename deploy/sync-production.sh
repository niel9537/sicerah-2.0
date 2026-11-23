#!/usr/bin/env bash
set -Eeuo pipefail

SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd -P)"
DEST="/home/niasbaratkabgo/public_html/bappelitbangda.niasbaratkab.go.id"

# Fail closed if required tools or application files are missing.
command -v rsync >/dev/null || { echo 'STOP: rsync tidak tersedia pada hosting.' >&2; exit 1; }
[[ -f "$SRC/artisan" && -f "$SRC/composer.json" ]] || { echo 'STOP: root repository bukan Laravel.' >&2; exit 1; }
[[ -d "$DEST" && -f "$DEST/artisan" && -f "$DEST/.env" ]] || { echo 'STOP: Laravel existing/.env tidak ditemukan. Periksa lokasi.' >&2; exit 1; }
[[ ! -L "$DEST" ]] || { echo 'STOP: destination berupa symlink; perlu pemeriksaan manual.' >&2; exit 1; }

# Do not use --delete: keep existing files not tracked by Git.
rsync -a --itemize-changes \
  --exclude='/.git/' \
  --exclude='/.github/' \
  --exclude='/.cpanel.yml' \
  --exclude='/deploy/' \
  --exclude='/.env' \
  --exclude='/.env.*' \
  --exclude='/storage/' \
  --exclude='/vendor/' \
  --exclude='/bootstrap/cache/' \
  --exclude='/node_modules/' \
  --exclude='/tests/' \
  --exclude='/public/storage/' \
  --exclude='/public/uploads/' \
  --exclude='/public/files/' \
  --exclude='/uploads/' \
  --exclude='/files/' \
  --exclude='/index.php' \
  --exclude='/.htaccess' \
  --exclude='/public/index.php' \
  --exclude='/public/.htaccess' \
  --exclude='*.sql' \
  --exclude='*.sqlite' \
  --exclude='*.sqlite3' \
  --exclude='*.log' \
  "$SRC/" "$DEST/"

echo 'Selesai sinkronisasi selektif. .env/storage/vendor/index.php existing tidak ditimpa.'
echo 'PERHATIAN: Tidak ada composer install, migrasi database, ataupun cache clear otomatis.'
