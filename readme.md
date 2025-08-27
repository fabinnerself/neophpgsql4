# 🚀 Proyecto NEOPHPGSQL

Este proyecto demuestra cómo conectar una aplicación **PHP nativo** a una base de datos **PostgreSQL en la nube (Neon.tech)**, con soporte para despliegue en entornos como **Render** y **Vercel**, además de ejecución local mediante **Docker**.

La finalidad es proporcionar un ejemplo funcional y reutilizable para desarrolladores que deseen integrar PHP con PostgreSQL en arquitecturas modernas (serverless, cloud, contenedores), respetando buenas prácticas de configuración y seguridad.

🔗 **Versión en vivo en Render:**  
👉 [https://neophpgsql4.onrender.com](https://neophpgsql4.onrender.com)

---

## 📁 Estructura del Proyecto

```
/
├── .env                    # Variables de entorno (NO subir a Git)
├── .env.example            # Plantilla de variables de entorno
├── index.php               # Script principal: conexión a Neon y consulta a tabla `book`
├── api/                    # Punto de entrada para serverless (Vercel)
│   └── index.php           # Handler para funciones en Vercel
├── Dockerfile              # Definición del contenedor Docker
├── docker-compose.yml      # Configuración para levantar el servicio con Docker Compose
├── compose.yml             # Alternativa para Docker Compose (si se usa con Docker Swarm)
├── render.yml              # Configuración automática para despliegue en Render
├── start.sh                # Script de inicio para Render
└── .gitignore              # Archivos y carpetas ignorados por Git
```

---

## 🛠️ Requisitos

- **PHP 8.0 o superior**
- Extensión **PDO para PostgreSQL** habilitada (`pdo_pgsql`)
  - En entornos locales (como XAMPP), asegúrate de que en `php.ini` estén descomentadas las líneas:
    ```ini
    extension=pgsql
    extension=pdo_pgsql
    ```
- Acceso a una base de datos PostgreSQL en [Neon.tech](https://console.neon.tech) (gratis)
- Composer (opcional, para futuras dependencias)

---

## 🔧 Instalación y Configuración

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/neophpgsql.git
cd neophpgsql
```

### 2. Configurar variables de entorno

Copia el archivo de ejemplo y configúralo con tus credenciales:

```bash
cp .env.example .env
```

#### Para desarrollo local (PostgreSQL local)

```env
DB_HOST=localhost
DB_PORT=5432
DB_NAME=library
DB_USER=postgres
DB_PASSWORD=tu_contraseña_local
```

#### Para producción (Neon.tech)

> ⚠️ **No uses este archivo en producción.** Las variables deben configurarse en el panel de Render o Vercel.

```env
DB_HOST=ep-soft-wildflower-adgo4b7g-pooler.c-2.us-east-1.aws.neon.tech
DB_PORT=5432
DB_NAME=neondb
DB_USER=tu_usuario@neondb_owner
DB_PASSWORD=tu_contraseña_segura
```

---

## ▶️ Uso

### Ejecución local

Inicia un servidor PHP local:

```bash
php -S localhost:8000
```

Accede desde tu navegador:

```
http://localhost:8000/index.php
```

Se mostrará:
- Estado de la conexión a PostgreSQL
- Lista de tablas disponibles
- Resultados de una consulta a la tabla `book`

---

## 🐳 Ejecución con Docker

Este proyecto incluye un `Dockerfile` para ejecutar la aplicación en un contenedor.

### 1. Instalar Docker

Sigue las instrucciones oficiales:  
👉 [https://docs.docker.com/get-docker/](https://docs.docker.com/get-docker/)

### 2. Construir la imagen

```bash
docker build -t neophpgsql .
```

### 3. Ejecutar el contenedor

Reemplaza las variables con tus credenciales de Neon:

```bash
docker run -d -p 8080:80 \
  -e DB_HOST="ep-xxxxxx.c-2.us-east-1.aws.neon.tech" \
  -e DB_PORT="5432" \
  -e DB_NAME="neondb" \
  -e DB_USER="tu_usuario" \
  -e DB_PASSWORD="tu_contraseña" \
  --name neophpgsql-container \
  neophpgsql
```

Accede a la aplicación:

```
http://localhost:8080
```

### 4. Comandos útiles de Docker

```bash
# Listar contenedores en ejecución
docker ps

# Detener un contenedor
docker stop neophpgsql-container

# Eliminar un contenedor
docker rm neophpgsql-container

# Listar imágenes
docker image ls

# Eliminar una imagen
docker image rm neophpgsql

# Eliminar contenedores detenidos
docker container prune
```

---

## ☁️ Despliegue en Render

Este proyecto está configurado para desplegarse automáticamente en [Render](https://render.com) usando el archivo `render.yml`.

### Pasos:

1. Crea una nueva **Web Service** en Render desde tu repositorio de GitHub.
2. Render detectará automáticamente `render.yml`.
3. Define las siguientes variables de entorno en el panel de Render:

   | Variable | Valor |
   |--------|-------|
   | `DB_HOST` | Endpoint de Neon (ej: `ep-...aws.neon.tech`) |
   | `DB_PORT` | `5432` |
   | `DB_NAME` | `neondb` |
   | `DB_USER` | Usuario de Neon (ej: `usuario@neondb_owner`) |
   | `DB_PASSWORD` | Contraseña de Neon |
   | `PORT` | `3000` (Render lo gestiona automáticamente) |

4. ¡Listo! Render desplegará tu API automáticamente.

> ✅ El script `start.sh` y el `Dockerfile` son usados por Render para iniciar el servicio.

---

## 📝 Notas importantes

- 🔐 Nunca subas el archivo `.env` a Git. Está incluido en `.gitignore`.
- 🔐 Las credenciales de base de datos deben gestionarse siempre mediante **variables de entorno**, especialmente en producción.
- 💡 El driver `PDO-PostgreSQL` es el recomendado para mayor compatibilidad y seguridad.
- 🌐 Neon.tech requiere `sslmode=require`. Esta configuración se aplica automáticamente en el código.
- 🔄 Cada `git push` a la rama principal desencadena un nuevo despliegue en Render (si está conectado).

---

## 🧪 Funcionalidades demostradas

- Conexión segura a PostgreSQL en Neon.tech
- Uso de PDO con sentencias preparadas
- Lectura de variables de entorno
- Consulta y visualización de datos
- Soporte para serverless (Vercel/Render)
- Empaquetado con Docker para portabilidad

---

## 🤝 Contribución

Si tienes sugerencias, mejoras o encuentras errores, no dudes en abrir un issue o enviar un PR.

---

## 📚 Recursos

- [Neon.tech – PostgreSQL serverless](https://neon.tech)
- [Render – Despliegue continuo](https://render.com)
- [Vercel – Serverless Functions](https://vercel.com)
- [Docker – Container platform](https://www.docker.com)

---

## 📜 Licencia

Este proyecto está bajo la licencia MIT. Para más detalles sobre los términos de la licencia, visita [MIT License](https://choosealicense.com/licenses/mit/).

---

## 🚀 Autor

👤 Favian Medina Gemio

| Recurso      | Dirección                                                                 |
|--------------|---------------------------------------------------------------------------|
| 📧 Email     | [favian.medina.gemio@gmail.com](mailto:favian.medina.gemio@gmail.com)     |
| 💻 GitHub    | [https://github.com/fabinnerself](https://github.com/fabinnerself)        |
| 🧠 LinkedIn  | [https://www.linkedin.com/in/favian-medina-gemio/](https://www.linkedin.com/in/favian-medina-gemio/) |
| 💼 Portafolio| [https://favian-medina-cv.vercel.app/](https://favian-medina-cv.vercel.app/) |

(c) 2025