# Proyecto NEOPHPGSQL

Este proyecto contiene scripts PHP para demostrar la conexión a bases de datos PostgreSQL, de manera remota (usando Neon - https://console.neon.tech) y está configurado para ser desplegado en Vercel.

## Estructura

```
/
├── .env              # Variables de entorno para la conexión (NO SUBIR A GIT)
├── .env.example      # Ejemplo para la conexión local
├── index.php         # Script para conectar a la BD de Neon y leer la tabla 'book'
├── api/              # Directorio para funciones serverless en Vercel
│   └── index.php     # Punto de entrada para Vercel
├── composer.json     # Dependencias del proyecto
├── vercel.json       # Configuración para despliegue en Vercel
└── .gitignore        # Archivos a ignorar en el repositorio
```

## Requisitos

- PHP 8.0 o superior
- Extensión PDO de PHP para PostgreSQL (`pdo_pgsql`) habilitada. verificar en archivo C:\xampp\php\php.ini 
que estas lines esten habilitadas:

extension=pgsql
extension=pdo_pgsql



## Instalación

1.  Clona el repositorio.

3.  Para trabajar localmente Crea tu archivo `.env` a partir de `.env.example` y configúralo con los datos de tu base de datos PostgreSQL local.
    ```
    # .env
    DB_HOST=localhost
    DB_PORT=5432
    DB_NAME=library
    DB_USER=postgres
    DB_PASSWORD=tu_contraseña_local
    ```
4.  Cuando se publique en Render, se deben especificar las variables de entorno con los datos de conexión a la base de datos en Neon.
    ```
    # .env
    NEON_DB_HOST=ep-....aws.neon.tech
    NEON_DB_PORT=5432
    NEON_DB_NAME=neondb
    NEON_DB_USER=tu_usuario_neon
    NEON_DB_PASSWORD=tu_contraseña_neon
    ```

## Uso
Al ejecutar index.php se conecta a una base de datos PostgreSQL remota en Neon, realiza una consulta a la tabla `book` y muestra los resultados.
 
Para ejecutarlo localmente, navega a:

`http://localhost/tu_dir_proyecto/index.php` (o la URL correspondiente en tu configuración)

existe una version online de este proyecto en Render:
[Render online https://neophpgsql4.onrender.com/](https://neophpgsql4.onrender.com/)

 ## Docker
 Para instalar Docker, sigue las instrucciones en la [documentación oficial](https://docs.docker.com/get-docker/).
 para correr el proyecto en docker estos son los comandos para ejecutarlo: 

docker build -t neophpgsql .
docker run -d -p 8080:80 \
  -e DB_HOST="your-endpoint-id.neon.tech" \
  -e DB_PORT="5432" \
  -e DB_NAME="your_database_name" \
  -e DB_USER="your_username" \
  -e DB_PASSWORD="your_password" \
  --name neophpgsql-container neophpgsql 

en localhost:8080 se puede acceder a la aplicacion

comandos utiles docker
docker container prune --Borra todos los contenedores detenidos --
Docker container stop (id del contenedor) -- detiene un contenedor
Docker container rm (id del contenedor) -- borra un contenedor
docker ps -- lista los contenedores corriendo

docker image ls -- lista las imagenes de docker
docker image rm (id de la imagen) -- borra una imagen



## Notas importantes para Render
- Los archivos render.yaml, start.sh y Dockerfile son necesarios para la configuracion de Render.
- Importante tambien especificar las variables de entorno :

   DB_HOST=***.....amazonaws.aws.neon.tech
   DB_PORT=5432
   DB_NAME=neondb
   DB_USER=neondb_owner
   DB_PASSWORD=password

