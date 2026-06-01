#!/bin/bash
# Run this after installing MAMP to sync the project.
# Usage: bash sync-to-mamp.sh

MAMP_DIR="/Applications/MAMP/htdocs/alumni"

echo "Syncing NJOSA Alumni Portal to MAMP..."
rsync -av --exclude='.git' /Users/crateshub/alumni/ "$MAMP_DIR/"
echo ""
echo "Done! Visit: http://localhost:8888/alumni/"
echo "phpMyAdmin:  http://localhost:8888/MAMP/ → Tools → phpMyAdmin"
