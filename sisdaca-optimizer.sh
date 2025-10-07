#!/bin/bash

# Variables
PROJECT_DIR="/var/www/nodejs/frontendapi/public"
LOG_FILE="/var/www/nodejs/frontendapi/public/storage/logs/sisdaca-optmize.log"
BRANCH="main"
USER="frontendapi"

# Inicio del script
echo -e "\n=== Iniciando Optimizacion: $(date) ===" >> "$LOG_FILE"

# Cambiar al directorio del proyecto
cd "$PROJECT_DIR" || { echo "Error: No se pudo acceder a $PROJECT_DIR" >> "$LOG_FILE"; exit 1; }

# Actualizar dependencias
echo "-> Generando nueva build de assets..." >> "$LOG_FILE"
sudo -u "$USER" npm run build >> "$LOG_FILE" 2>&1

echo "-> Limpiando cache del dia pasado..." >> "$LOG_FILE"
sudo -u "$USER" php artisan optimize:clear >> "$LOG_FILE" 2>&1
echo "-> Generando cache optimizado..." >> "$LOG_FILE"
sudo -u "$USER" php artisan optimize >> "$LOG_FILE" 2>&1

echo "=== Fin de la optimizacion: $(date) ===" >> "$LOG_FILE"
