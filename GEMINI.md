# GEMINI - Sistema de Administracion de Cartera SISDACA

## Resumen del Sistema

1	INTRODUCCIÓN
Esta guía de uso describe la estructura y funcionamiento del sistema "Sistema de Administracion de Cartera - SISDACA", una aplicación desarrollada en Laravel para gestionar beneficiarios, pagos, planes y vouchers.
2	REQUERIMIENTOS TECNICOS
2.1	ENTORNO SERVIDOR
•	PHP ^8.3
•	Servidor web compatible con PHP (Apache, Nginx, etc.)
•	Composer (gestor de dependencias de PHP)
•	Node.js y NPM (para compilación de assets frontend)
2.2	BASE DE DATOS
•	PostgreSQL ^17.0
•	Puerto de base de datos: 5432 (configurable)
•	Servidor de base de datos: 20.20.1.118 (Datawarehouse de la empresa)
2.3	CONFIGURACION DEL ENTORNO
•	Archivo “.env” configurado con las variables de entorno necesarias
2.4	ALMACENAMIENTO
Sistema de archivos local.
2.5	CACHÉ Y SESIONES
•	Caché configurada para usar la base de datos
•	Sesiones configuradas para usar la base de datos
•	Tiempo de vida de la sesión: 480 minutos (configurable)
2.6	SEGURIDAD
BCRYPT_ROUNDS configurado a 12 para hashing de contraseñas.
El proyecto implementa múltiples capas de seguridad utilizando los frameworks Laravel Fortify, Jetstream y Sanctum. A continuación, se detallan los principales componentes de seguridad:
2.7	FRONT-END
Vite configurado para compilación de assets.
3	REQUERIMIENTOS ESPECÍFICOS DE LARAVEL
•	Extensiones de PHP requeridas por Laravel (BCMath, INTL, OpenSSL, PGSQL, PDO_PGSQL, PDO, Mbstring, Tokenizer, XML, XSL, Zip)
•	Permisos de escritura en directorios específicos (storage, bootstrap/cache)
3.1	ENTORNO DE DESARROLLO
•	Git para control de versiones
3.2	DEPLOYMENT / LANZAMIENTO
-	Laravel Valet, Laragon, o similares para desarrollo local
-	Git para control de versiones
4	ESTRUCTURA DEL PROYECTO
El proyecto sigue la estructura estándar de Laravel:
-	app/: Contiene el código principal de la aplicación.
-	Http/Controllers/*: Controladores.
-	Models/: Modelos de Eloquent.
-	routes/: Definiciones de rutas.
-	config/: Archivos de configuración.
-	database/: Migraciones y factories.
5	AUTENTICACIÓN
La aplicación utiliza Laravel Fortify mediante Jetstream para la autenticación. Las rutas protegidas están envueltas en el middleware auth:sanctum.
6	SECCIONES Y FUNCIONALIDAD
6.1	INICIO
Vista destinada a facilitar la importación masiva de registros de tipo Pago, Planes, Beneficiarios y Vouchers.
Este solo admite archivos de tipo Comma Separated Values (CSV), además, se deberá especificar el carácter separador de registros (comúnmente usados “,” “;” “|”).
6.2	BENEFICIARIOS
Esta vista permite acceder a la tabla que contiene a todos los beneficiarios existentes en la tabla Beneficiaries, también permite las siguientes operaciones:
-	Creación masiva de planes de pagos.
-	Reajuste masivo de planes de pagos.
-	Exportación masiva / especifica de documentos PDF con los planes de pago del o los beneficiarios.
-	Búsqueda avanzada de beneficiarios.
-	Exportación masiva / especifica de la lista de beneficiarios, en formato Excel o CSV.
-	Revisión de perfil del beneficiario:
o	Datos personales y crediticios del beneficiario
o	Ver lista de planes de pago
o	Ver historial de pagos
o	Generación detallada de:
	Reajuste de plan de pagos
	Activación de cartera
	Diferimiento de cobro (se anexa al final del plan de pagos)
6.3	PROYECTOS
Esta vista permite acceder a la tabla que contiene a todos los proyectos existentes en la tabla Projects, estos estan relacionados a un usuario del sistema para su seguimiento, también permite las siguientes operaciones:
-	Revisión de datos de proyectos y su grupo de beneficiarios relacionados.
7	LISTA DE LIBRERIAS INDISPENSABLES PARA EL SISTEMA
La siguiente lista está contenida dentro el archivo composer.json del sistema.
-	"php": "^8.3",
-	"bacon/bacon-qr-code": "*",
-	"barryvdh/laravel-dompdf": "^3.0",
-	"laravel-lang/common": "^6.4",
-	"laravel/framework": "^11.9",
-	"laravel/jetstream": "^5.2",
-	"laravel/sanctum": "^4.0",
-	"laravel/tinker": "^2.9",
-	"livewire/livewire": "^3.5",
-	"openspout/openspout": "^4.26",
-	"phpoffice/phpspreadsheet": "^3.3",
-	"power-components/livewire-powergrid": "^6.0"
8	LISTA DE CDN’S UTILIZADAS POR EL SISTEMA
-	<script src="https://code.highcharts.com/highcharts.js"></script>
-	<script src="https://code.highcharts.com/highcharts-more.js"></script>
-	<script src="https://code.highcharts.com/modules/exporting.js"></script>
-	<script src="https://code.highcharts.com/modules/export-data.js"></script>
-	<script src="https://code.highcharts.com/modules/accessibility.js"></script>
-	<script src="https://code.highcharts.com/highcharts-more.js"></script>
-	<script src="https://code.highcharts.com/modules/dumbbell.js"></script>
-	<script src="https://code.highcharts.com/modules/lollipop.js"></script>
-	<link rel="preconnect" href="https://fonts.googleapis.com">
-	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
-	<link ref="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
-	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
-	<script src="https://cdn.tailwindcss.com"></script>
9   REPOSITORIO PUBLICO DEL SISTEMA
-   https://github.com/DnvsrDesarrollo1/dnvsr-cartera.git

## CARACTERISTICAS DE LANZAMIENTO/DEPLOY
Este sistema se encuentra en produccion a traves de una maquina virtual en ProxMox Virtual Environment 8.4.1, cuyas caracteristicas tecnicas son:
RAM 4GB
Procedador 4 cores 1 socket [x86-64-v2-AES]
Disco Duro local-lvm:vm-510-disk-0,discard=on,iothread=1,size=64G,ssd=1
IP 20.20.1.198
Accesible desde SSH pvssisdaca@20.20.1.198 / root@20.20.1.198
BD Postgres 17 alojado en el Datawarehouse de la empresa 20.20.1.118:5432.

El proyecto esta enlazado a un repositorio publico en GitHub, el cual se encuentra en la siguiente URL:
https://github.com/DnvsrDesarrollo1/dnvsr-cartera.git

## ADMINISTRACION DEL SERVIDOR VM
Puedes acceder de forma segura a traves de estas credenciales

ssh root@20.20.1.198 
pwd: 515.t3ma5

## ADMINISTRACION DEL SERVIDOR VM
## Acceso Manual (para el usuario)
Puedes acceder de forma segura a través de estas credenciales para una sesión interactiva:

ssh root@20.20.1.198
pwd: 515.t3ma5

### Acceso Automatizado (para Gemini)
Para la automatización y el acceso desde el agente Gemini, se ha configurado la autenticación mediante clave SSH. La autenticación por
contraseña no es compatible con el agente.
**Clave Privada:** `C:\Laragon\www\dnvsr-cartera\gemini_ssh_key`
**Clave Pública:** `C:\Laragon\www\dnvsr-cartera\gemini_ssh_key.pub`
**Comando de Conexión:** `ssh -i C:\Laragon\www\dnvsr-cartera\gemini_ssh_key -o StrictHostKeyChecking=no root@20.20.1.198 'comando'`

**Importante:** Los archivos `gemini_ssh_key` y `gemini_ssh_key.pub` en el directorio raíz del proyecto son indispensables para esta
conexión y no deben ser eliminados.

## INTERACCION MODO DESARROLLADOR Y COPILOTO
Tus interacciones de tipo "revision", "analisis" y sus retroalimentaciones deben de especificar el bloque dentro los archivos, indicando la linea (correspondiente a la ubicacion dentro el archivo) y una breve linea de codigo inicial y final.
