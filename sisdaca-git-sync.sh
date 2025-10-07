#!/bin/bash

PROJECT_DIR="/var/www/nodejs/frontendapi/public"
LOG_FILE="/var/www/nodejs/frontendapi/public/storage/logs/sisdaca-updater.log"
BRANCH="main"
USER="frontendapi"
GIT_RESET="false"

log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> "$LOG_FILE"
}

log_message "=== INICIANDO ACTUALIZACIÓN ==="

if [ ! -d "$PROJECT_DIR" ]; then
    log_message "ERROR: Directorio $PROJECT_DIR no existe"
    exit 1
fi

if [ ! -w "$(dirname "$LOG_FILE")" ]; then
    echo "Error: Sin permisos de escritura para el archivo de log" >&2
    exit 1
fi

cd "$PROJECT_DIR" || { log_message "ERROR: No se pudo acceder a $PROJECT_DIR"; exit 1; }

if ! sudo -u "$USER" git rev-parse --git-dir > /dev/null 2>&1; then
    log_message "ERROR: No es un repositorio git válido"
    exit 1
fi

STASH_APPLIED=false
HAD_CHANGES=false

REPO_STATUS=$(sudo -u "$USER" git status --porcelain)
if [ -n "$REPO_STATUS" ]; then
    HAD_CHANGES=true
    log_message "Cambios locales detectados"

    if [ "$GIT_RESET" = "false" ]; then
        log_message "Haciendo stash de cambios locales..."
        if ! sudo -u "$USER" git stash push -u -m "Auto-stash by updater $(date)" >> "$LOG_FILE" 2>&1; then
            log_message "ERROR: Falló el stash de cambios"
            exit 1
        fi
        STASH_APPLIED=true
    fi
fi

if [ "$GIT_RESET" = "true" ] && [ "$HAD_CHANGES" = "true" ]; then
    log_message "Reseteando cambios locales (configurado)..."
    sudo -u "$USER" git reset --hard HEAD >> "$LOG_FILE" 2>&1
    sudo -u "$USER" git clean -fd --exclude=.env >> "$LOG_FILE" 2>&1
fi

log_message "Sincronizando con repositorio remoto..."
if ! sudo -u "$USER" git fetch origin >> "$LOG_FILE" 2>&1; then
    log_message "ERROR: Falló el fetch del repositorio"
    exit 1
fi

LOCAL_HASH=$(sudo -u "$USER" git rev-parse HEAD)
REMOTE_HASH=$(sudo -u "$USER" git rev-parse "origin/$BRANCH")

if [ "$LOCAL_HASH" != "$REMOTE_HASH" ]; then
    log_message "Cambios remotos detectados. Actualizando..."

    if ! sudo -u "$USER" git pull --no-edit --strategy=recursive --strategy-option=theirs origin "$BRANCH" >> "$LOG_FILE" 2>&1; then
        log_message "ERROR en git pull. Reseteando a origin/$BRANCH..."
        if ! sudo -u "$USER" git reset --hard "origin/$BRANCH" >> "$LOG_FILE" 2>&1; then
            log_message "ERROR: No se pudo resetear al branch remoto"
            exit 1
        fi
    fi

    if [ "$STASH_APPLIED" = true ] && [ "$GIT_RESET" = "false" ]; then
        log_message "Reaplicando cambios locales desde stash..."
        if ! sudo -u "$USER" git stash pop >> "$LOG_FILE" 2>&1; then
            log_message "CONFLICTO: Error al reaplicar cambios. Resolver manualmente."
        fi
    fi

    log_message "Actualizando dependencias Composer..."
    if [ -f "composer.json" ]; then
        if ! sudo -u "$USER" composer install --no-dev --no-ansi --no-interaction --optimize-autoloader >> "$LOG_FILE" 2>&1; then
            log_message "ADVERTENCIA: Error en composer install"
        fi
    fi

    log_message "Actualizando dependencias NPM..."
    if [ -f "package.json" ]; then
        if ! sudo -u "$USER" npm install --omit=dev --no-fund --no-audit >> "$LOG_FILE" 2>&1; then
            log_message "ADVERTENCIA: Error en npm install"
        fi

        log_message "Compilando assets..."
        if ! sudo -u "$USER" npm run production --no-color >> "$LOG_FILE" 2>&1; then
            log_message "ADVERTENCIA: Error en npm run production"
        fi
    fi

    log_message "Optimizando aplicación Laravel..."

    sudo -u "$USER" php artisan optimize:clear >> "$LOG_FILE" 2>&1

    if ! sudo -u "$USER" php artisan migrate --force --no-interaction >> "$LOG_FILE" 2>&1; then
        log_message "ERROR: Falló la migración de base de datos"
        exit 1
    fi

    sudo -u "$USER" php artisan optimize >> "$LOG_FILE" 2>&1
    sudo -u "$USER" php artisan view:cache >> "$LOG_FILE" 2>&1
    sudo -u "$USER" php artisan event:cache >> "$LOG_FILE" 2>&1

    log_message "Actualización completada correctamente"
else
    log_message "No hay cambios remotos para aplicar"
fi

if [ "$STASH_APPLIED" = true ]; then
    sudo -u "$USER" git stash drop >> "$LOG_FILE" 2>&1
fi

log_message "=== ACTUALIZACIÓN FINALIZADA ==="
