#!/bin/bash

# Telegram credentials
BOT_TOKEN="8709811015:AAFyr4TC0ql-OqsRfi4IDdo1vQVRmFjTOsg"
CHAT_ID="-1004499878029"

# Fetch data for the message
COMMIT_MSG=$(git log -1 --pretty=%B | head -n 1)
SERVER_IP=$(curl -s ifconfig.me)
CURRENT_TIME=$(date "+%Y-%m-%d %H:%M:%S %Z")

# Build the message
MESSAGE=$(cat <<EOF
🚀 DUCT-CENP updated
📌 Change: $COMMIT_MSG
🖥 Server: $SERVER_IP
⏱ Time: $CURRENT_TIME
EOF
)

echo "==> Sending update notification to Telegram..."
curl -s -X POST "https://api.telegram.org/bot$BOT_TOKEN/sendMessage" \
     -d chat_id="$CHAT_ID" \
     --data-urlencode text="$MESSAGE" > /dev/null
