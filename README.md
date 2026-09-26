# GreenCycle

GreenCycle es una aplicación web gamificada en la que cada persona usuaria administra un vivero digital. El sistema permite gestionar árboles cuyo crecimiento, salud y estado dependen de reglas determinadas por el servidor.

El proyecto está desarrollado como una aplicación cliente-servidor utilizando Laravel para el backend y HTML, CSS y JavaScript para la interfaz.

## Alcance Actual

Actualmente el proyecto se encuentra en el desarrollo del Sprint 1.

En esta etapa se establecen las bases del proyecto, incluyendo:

* Modelado de datos.
* Migraciones y estructura de la base de datos.
* Seeders.
* Configuración del proyecto Laravel.
* Preparación de la API para la gestión de árboles.
* Configuración de PostgreSQL mediante Neon.
* Configuración del entorno de desarrollo.
* Despliegue del proyecto en Render.
* Estructura base de la interfaz.

Las funcionalidades de cuidado de árboles, progreso, niveles, deterioro de salud, cooldown, procesos programados, cosecha, monedas, tienda e inventario corresponden a sprints posteriores.

La implementación de autenticación, autorización y las funcionalidades completas de la API se encuentra en desarrollo según el avance del Sprint 1.

## Stack / Requisitos locales

Para ejecutar el proyecto localmente se recomienda contar con:

* Windows 10 o superior.
* Laravel Herd.
* PHP 8.5.
* Composer 2.
* Node.js 26.
* npm.
* Git.
* Visual Studio Code.

No es necesario instalar PostgreSQL localmente, ya que el proyecto utiliza PostgreSQL alojado en Neon.

Docker tampoco es necesario para el entorno definido actualmente.

## Instalación

### 1. Clonar el repositorio

Abra PowerShell y ejecute:

```powershell
git clone https://github.com/jeini-sudo/GreenCycle.git
Set-Location GreenCycle
```

### 2. Configurar Laravel Herd

Si se utiliza Laravel Herd para el entorno local:

```powershell
herd init
herd isolate 8.5
```

### 3. Seleccionar la versión de Node.js

Si el proyecto cuenta con una versión de Node.js definida mediante la configuración correspondiente:

```powershell
nvm use
```



### 4. Instalar las dependencias

Instalar las dependencias de PHP:

```powershell
composer install
```

Instalar las dependencias de frontend:

```powershell
npm install
```

También puede utilizarse `npm ci` cuando se desee realizar una instalación basada estrictamente en el archivo de bloqueo del proyecto.

### 5. Crear el archivo de configuración

Copie `.env.example` como `.env`.

En PowerShell:

```powershell
Copy-Item .env.example .env
```

Después genere la clave de la aplicación:

```powershell
php artisan key:generate
```

### 6. Configurar la base de datos

GreenCycle utiliza PostgreSQL mediante Neon.

En el archivo `.env` se deben configurar las variables correspondientes:

```dotenv
DB_CONNECTION=pgsql
DB_URL="URL_DE_NEON"
DB_SSLMODE=require
```

La URL real de conexión debe permanecer únicamente en el archivo `.env` y no debe publicarse en GitHub.

El archivo `.env.example` debe contener únicamente valores de ejemplo o espacios para las variables necesarias.

### 7. Limpiar la configuración almacenada

Ejecute:

```powershell
php artisan optimize:clear
```

### 8. Ejecutar las migraciones y seeders

Para crear las tablas y ejecutar los seeders:

```powershell
php artisan migrate --seed
```

En un entorno de desarrollo, si se necesita reconstruir completamente la base de datos, puede utilizarse:

```powershell
php artisan migrate:fresh --seed
```

Este último comando elimina las tablas existentes, por lo que no debe utilizarse sobre una base de datos que contenga información que se quiera conservar.

### 9. Iniciar el frontend

Ejecute:

```powershell
npm run dev
```

Mantenga esta terminal abierta mientras desarrolla la aplicación.

### 10. Ejecutar la aplicación

Si se utiliza Laravel Herd, la aplicación podrá abrirse mediante el dominio local configurado por Herd.

También puede utilizarse el servidor de desarrollo de Laravel:

```powershell
php artisan serve
```

En ese caso, la aplicación estará disponible normalmente en:

```text
http://127.0.0.1:8000
```

## Configuración del entorno

Las variables principales utilizadas para la conexión con la base de datos son:

```dotenv
DB_CONNECTION=pgsql
DB_URL=
DB_SSLMODE=require
```

No se deben incluir en el repositorio:

* `.env`
* Contraseñas.
* Tokens.
* APP_KEY.
* Claves de API.
* Credenciales de Neon.
* Cadenas reales de conexión a bases de datos.
* Información privada.

Antes de realizar un commit se recomienda comprobar los archivos modificados:

```powershell
git status
```


## Pruebas y verificación

El proyecto contempla pruebas automatizadas mediante PHPUnit.

Para ejecutar las pruebas:

```powershell
php artisan test
```

Actualmente las pruebas automatizadas se encuentran pendientes de acuerdo con el avance del proyecto.

Para comprobar el formato del código PHP mediante Laravel Pint:

```powershell
.\vendor\bin\pint --test
```

Para corregir automáticamente el formato:

```powershell
.\vendor\bin\pint
```

Para generar los recursos frontend de producción:

```powershell
npm run build
```

Las pruebas específicas de las reglas temporales, como cooldown, deterioro, salud, progreso y estados, corresponden principalmente al Sprint 2.


### API futura

Durante los siguientes sprints se contempla agregar funcionalidades como:

```http
POST /api/trees/{tree}/care
GET /api/trees/{tree}/effects
POST /api/trees/{tree}/effects/{shop_item_id}
GET /api/shop/items
GET /api/inventory
POST /api/inventory/{item}/use
POST /api/shop/items/{item}/buy
PUT /api/trees/{tree}/harvest
DELETE /api/trees/{tree}
```

Estas rutas no deben considerarse implementadas únicamente por aparecer documentadas aquí. Su implementación corresponde al avance de los siguientes sprints.

## Credenciales de demostración

Actualmente no se incluyen credenciales personales o cuentas de demostración en este README.

Si posteriormente se crea una cuenta específica para demostraciones, se deberá utilizar una cuenta independiente y nunca credenciales personales de los integrantes del equipo.

## Seguridad

Nunca se deben publicar en el repositorio:

* El archivo `.env`.
* Contraseñas.
* Tokens.
* Credenciales de Neon.
* APP_KEY.
* Claves de API.
* Cadenas reales de conexión.
* Información privada.

El archivo `.env.example` debe documentar las variables necesarias para configurar el proyecto sin incluir valores secretos.

## Equipo

Brandon Víquez Camacho - C4L087

Jeinier Daniel Jiménez Navarro - C4G272

Kendall Pérez Cruz - C4I340

Repositorio:

https://github.com/jeini-sudo/GreenCycle