#!/usr/bin/env bash
# Builds build/hikashop-for-siteorigin-page-builder/ and build/hikashop-for-siteorigin-page-builder.zip from the files .distignore keeps
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
out="$root/build/hikashop-for-siteorigin-page-builder"
rm -rf "$root/build"
mkdir -p "$out"
rsync -a --exclude-from="$root/.distignore" --exclude=/build --exclude=/bin "$root/" "$out/"
( cd "$root/build" && zip -qr hikashop-for-siteorigin-page-builder.zip hikashop-for-siteorigin-page-builder )
echo "$root/build/hikashop-for-siteorigin-page-builder.zip"
