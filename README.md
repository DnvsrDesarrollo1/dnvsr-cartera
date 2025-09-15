# SISDACA - Sistema de Administración de Cartera

Este es el repositorio para el "Sistema de Administración de Cartera - SISDACA", una aplicación web construida con Laravel para la gestión integral de beneficiarios, proyectos, pagos y planes financieros.

## Descripción General

SISDACA es una herramienta robusta diseñada para facilitar la administración de carteras de beneficiarios. Permite un seguimiento detallado de los pagos, la gestión de planes financieros, y la organización de beneficiarios dentro de diferentes proyectos. El sistema está orientado a optimizar los flujos de trabajo a través de operaciones masivas y una interfaz de usuario clara y reactiva.

## Características Principales

-   **Gestión de Beneficiarios:**
    -   Creación, visualización y edición de perfiles de beneficiarios.
    -   Búsqueda avanzada y filtrado.
    -   Consulta de datos personales, historial de pagos y planes asociados.
-   **Gestión de Proyectos:**
    -   Agrupación de beneficiarios por proyectos para un mejor seguimiento.
-   **Operaciones Masivas:**
    -   Importación de Beneficiarios, Pagos, Planes y Vouchers desde archivos CSV.
    -   Creación y reajuste masivo de planes de pago.
    -   Exportación de reportes en PDF, Excel y CSV.
-   **Gestión Financiera:**
    -   Registro y seguimiento de pagos.
    -   Creación y ajuste de planes de pago.
    -   Generación de Vouchers.
-   **Dashboard Interactivo:**
    -   Visualización de datos y estadísticas clave a través de gráficos.

## Stack Tecnológico

El sistema está construido sobre un stack moderno de PHP y JavaScript:

-   **Backend:** Laravel 11, PHP 8.3
-   **Frontend:** Livewire, Tailwind CSS, Blade
-   **Base de Datos:** PostgreSQL 17
-   **Servidor:** Apache / Nginx
-   **Librerías Clave:**
    -   `livewire/livewire`: Para componentes dinámicos.
    -   `power-components/livewire-powergrid`: Para tablas de datos interactivas.
    -   `barryvdh/laravel-dompdf`: Para la generación de PDFs.
    -   `openspout/openspout` y `phpoffice/phpspreadsheet`: Para la manipulación de archivos Excel y CSV.
    -   `highcharts`: Para la visualización de gráficos.

## Instalación y Configuración

Para levantar un entorno de desarrollo local, sigue estos pasos:

1.  **Clonar el repositorio:**
    ```bash
    git clone https://github.com/DnvsrDesarrollo1/dnvsr-cartera.git
    cd dnvsr-cartera
    ```

2.  **Instalar dependencias de PHP:**
    ```bash
    composer install
    ```

3.  **Instalar dependencias de Node.js:**
    ```bash
    npm install
    ```

4.  **Configurar el entorno:**
    -   Copia el archivo de ejemplo `.env.example` a `.env`.
    -   Configura las variables de entorno, especialmente la conexión a la base de datos (`DB_*`) y la URL de la aplicación (`APP_URL`).
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Ejecutar las migraciones de la base de datos:**
    ```bash
    php artisan migrate
    ```

6.  **Compilar los assets del frontend:**
    ```bash
    npm run dev
    ```

7.  **Iniciar el servidor de desarrollo:**
    ```bash
    php artisan serve
    ```

## Repositorio

El código fuente está alojado en GitHub y se puede acceder a través del siguiente enlace:
[https://github.com/DnvsrDesarrollo1/dnvsr-cartera.git](https://github.com/DnvsrDesarrollo1/dnvsr-cartera.git)
