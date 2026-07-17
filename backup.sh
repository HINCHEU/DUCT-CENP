#!/bin/bash

# Load environment variables from .env
if [ -f .env ]; then
    export $(cat .env | grep -v '^#' | awk '/=/ {print $1}')
else
    echo "❌ Error: .env file not found!"
    exit 1
fi

DATE=$(date +%Y-%m-%d_%H-%M-%S)
BACKUP_DIR="./backups"
BACKUP_FILE="$BACKUP_DIR/duct_cenp_$DATE.sql.gz"

# Telegram credentials
BOT_TOKEN="8709811015:AAFyr4TC0ql-OqsRfi4IDdo1vQVRmFjTOsg"
CHAT_ID="-1004499878029"

mkdir -p $BACKUP_DIR
echo "==> Creating database backup..."

# Dump and compress using the .env variables and current container name
docker exec duct_cenp_db mysqldump -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" 2>/dev/null | gzip > "$BACKUP_FILE"

# Check file exists and is not empty
if [ -s "$BACKUP_FILE" ]; then
    echo "==> Sending backup to Telegram..."
    curl -s -F chat_id="$CHAT_ID" \
         -F document=@"$BACKUP_FILE" \
         -F caption="✅ DUCT-CENP DB Backup - $DATE" \
         "https://api.telegram.org/bot$BOT_TOKEN/sendDocument" > /dev/null

    # Keep only last 7 backups locally
    ls -tp $BACKUP_DIR/*.sql.gz 2>/dev/null | grep -v '/$' | tail -n +8 | xargs -I {} rm -- {}
    
    echo "==> Backup successful!"
else
    echo "==> ❌ Backup failed!"
    curl -s -X POST "https://api.telegram.org/bot$BOT_TOKEN/sendMessage" \
         -d chat_id="$CHAT_ID" \
         -d text="❌ DUCT-CENP DB Backup FAILED on $DATE" > /dev/null
    exit 1
fi
