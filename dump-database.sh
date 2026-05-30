#!/usr/bin/env bash
# dump-database.sh
# Exports a clean, buyer-ready SQL file to Database/database.sql
# Works by dumping the full database then stripping user-generated rows from the SQL file.
# Your live database is NEVER modified.
# Usage: bash dump-database.sh

set -euo pipefail

# ── Config ────────────────────────────────────────────────────────────────────
ENV_FILE="src/.env"
OUT_DIR="Database"
OUT_FILE="$OUT_DIR/database.sql"

# ── Read .env ─────────────────────────────────────────────────────────────────
if [[ ! -f "$ENV_FILE" ]]; then
    echo "❌  $ENV_FILE not found. Run this script from the project root."
    exit 1
fi

get_env() {
    grep -E "^${1}=" "$ENV_FILE" | head -1 | cut -d= -f2- | tr -d '"' | tr -d "'"
}

DB_HOST=$(get_env DB_HOST)
DB_PORT=$(get_env DB_PORT)
DB_DATABASE=$(get_env DB_DATABASE)
DB_USERNAME=$(get_env DB_USERNAME)
DB_PASSWORD=$(get_env DB_PASSWORD)

DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-3306}

if [[ -z "$DB_DATABASE" || -z "$DB_USERNAME" ]]; then
    echo "❌  DB_DATABASE or DB_USERNAME is empty in $ENV_FILE"
    exit 1
fi

MYSQL_ARGS=(-h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME")
if [[ -n "$DB_PASSWORD" ]]; then
    MYSQL_ARGS+=("-p${DB_PASSWORD}")
fi

echo "📦  Database: $DB_DATABASE @ $DB_HOST:$DB_PORT"

# ── Tables to dump as empty (structure only, no rows) ─────────────────────────
EMPTY_TABLES=(
    users
    social_accounts
    user_logins
    activity_log
    admin_notifications
    transactions
    webhook_events
    kyc_submissions
    subscribers
    user_notifications
    notification_logs
    notification_suppressions
    notification_preferences
    support_tickets
    support_messages
    support_attachments
    bug_reports
    user_subscriptions
    credit_logs
    payments
    phone_verification_codes
    contracts
    contract_parties
    blog_posts
    blog_post_tag
    cache
    cache_locks
    jobs
    job_batches
    failed_jobs
    sessions
    password_reset_tokens
)

mkdir -p "$OUT_DIR"

# ── Step 1: Dump all tables with data ─────────────────────────────────────────
echo "💾  Dumping full database..."

mysqldump "${MYSQL_ARGS[@]}" \
    --single-transaction \
    --routines \
    --triggers \
    --add-drop-table \
    --disable-keys \
    --extended-insert \
    --set-gtid-purged=OFF \
    "$DB_DATABASE" > "$OUT_FILE"

# ── Step 2: Strip INSERT rows for user-generated tables (post-process the SQL) ─
echo "🧹  Removing user-generated rows from SQL file..."

for tbl in "${EMPTY_TABLES[@]}"; do
    # Remove all INSERT INTO `table` ... lines for this table
    perl -i -0pe "s/INSERT INTO \`${tbl}\`[^\n]*\n//g" "$OUT_FILE"
done

echo "✅  Done → $OUT_FILE ($(du -sh "$OUT_FILE" | cut -f1))"
echo "    Your live database was not modified."
