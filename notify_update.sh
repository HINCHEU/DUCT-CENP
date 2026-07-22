#!/bin/bash

# Telegram credentials
BOT_TOKEN="8709811015:AAFyr4TC0ql-OqsRfi4IDdo1vQVRmFjTOsg"
CHAT_ID="-1004499878029"

# Fetch data for the message
SERVER_IP=$(curl -s ifconfig.me)
CURRENT_TIME=$(date "+%Y-%m-%d %H:%M:%S %Z")

# List all commits introduced by the latest pull (ORIG_HEAD is set by git pull)
if git rev-parse --verify ORIG_HEAD > /dev/null 2>&1; then
    COMMITS=$(git log ORIG_HEAD..HEAD --pretty=format:"• %s" --no-merges)
else
    # Fallback: show only the last commit if ORIG_HEAD is unavailable
    COMMITS=$(git log -1 --pretty=format:"• %s")
fi

# If no new commits were found, notify anyway
if [ -z "$COMMITS" ]; then
    COMMITS="(no new commits)"
fi

# Build the message
MESSAGE=$(cat <<EOF
🚀 DUCT-CENP updated
🖥 Server: $SERVER_IP
⏱ Time: $CURRENT_TIME

📋 Changes:
$COMMITS
EOF
)

echo "==> Sending update notification to Telegram..."
curl -s -X POST "https://api.telegram.org/bot$BOT_TOKEN/sendMessage" \
     -d chat_id="$CHAT_ID" \
     --data-urlencode text="$MESSAGE" > /dev/null
