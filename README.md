# PokéGame DB Master Project 🚀

Bienvenido al repositorio oficial de **PokéGame**, un juego donde el verdadero poder no está en los botones, sino en tu conocimiento de MariaDB y SQL.

## 1. Guía Técnica y de Dinámicas (Database Tutorial)

Esta sección explica el motor interno del juego para que puedas dominar el servidor y machacar a tus rivales.

### 📊 El Mapa del Poder (ERD)

![Database Schema](https://raw.githubusercontent.com/bernat13/pokegame/main/src/img/db_diagram.png)

### 📋 Definición del Mundo (Tablas)

| Tabla | Descripción |
| :--- | :--- |
| **entrenadores** | Registro oficial de jugadores y sus perfiles SQL. |
| **especies** | El ADN de los Pokémon (stats base, tipos, nombres). |
| **equipo_pokemon** | Tus Pokémon reales con sus niveles y stats actuales. |
| **historial_combates** | Registros públicos de cada victoria y derrota. |

### 📈 Lógica de Juego y Fórmulas
- **Daño**: `Daño = FLOOR((Atk / Def) * Nivel * 2 * Suerte * Tipos)`.
- **Level Up**: Al ganar combates subes de nivel y tus stats se recalcularán automáticamente: `Stat_Base + (Nivel * 3)`, con un tope absoluto de **300**.
- **Seguridad**: Triggers internos vigilan que nadie toque tus Pokémon ni tus procedures.

---

## 2. 🚀 Despliegue en Producción

Sigue estos pasos para poner el servidor de PokéGame en marcha en un entorno real.

### Requisitos Previos
- Docker y Docker Compose instalados.
- Un servidor con los puertos 80 y 443 abiertos.

### Paso 1: Configuración de Variables
Asegúrate de que el archivo `docker-compose-prod.yml` tiene las contraseñas y nombres de base de datos correctos.

### Paso 2: Generar Certificados (Opcional)
Si vas a usar HTTPS interno con Nginx, asegúrate de tener los archivos `.crt` y `.key` en la carpeta `./ssl`.

### Paso 3: Lanzar el Sistema
Ejecuta el siguiente comando para levantar la base de datos, la aplicación, Nginx y el túnel de zrok:

```bash
docker compose -f docker-compose-prod.yml up -d
```

---

## 3. 🛠️ Configuración de Producción (YAML)

Aquí tienes el archivo `docker-compose-prod.yml` utilizado actualmente:

```yaml
services:
  db:
    image: mariadb:latest
    container_name: pokegamesql-db
    environment:
      MARIADB_ROOT_PASSWORD: 'root'
      MARIADB_DATABASE: pokegame_admin
      MARIADB_ROOT_HOST: '%'
    volumes:
      - ./init_admin.sql:/docker-entrypoint-initdb.d/init.sql
      - ./pokegame-db-data:/var/lib/mysql
    ports:
      - "3308:3306"
    networks:
      - pokegame-network
    restart: always

  app:
    image: bernat13/pokegame-app:latest
    container_name: pokegamesql-app
    environment:
      DB_HOST: db
      DB_NAME: pokegame
    depends_on:
      - db
    networks:
      - pokegame-network
    restart: always

  nginx:
    image: nginx:latest
    container_name: pokegamesql-nginx
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx_conf:/etc/nginx/conf.d:ro
      - ./ssl:/etc/nginx/ssl:ro
    depends_on:
      - app
    networks:
      - pokegame-network
    restart: always

  zrok:
    image: openziti/zrok
    container_name: pokegamesql-zrok
    restart: always
    user: "root"
    networks:
      - pokegame-network
    depends_on:
      - nginx
    volumes:
      - ./zrok_env:/mnt
    environment:
      - HOME=/mnt
    command: share reserved pokegamesql --headless --insecure

networks:
  pokegame-network:
    driver: bridge

volumes:
  pokegame-db-data:
```

## 4. 🔄 CI/CD y Automatización

El proyecto incluye una **GitHub Action** que automatiza el despliegue:

- **Trigger**: Cada vez que se hace un `push` o un `Pull Request` a la rama `main`.
- **Acción**: Construye la imagen de Docker y la sube automáticamente a Docker Hub como `bernat13/pokegame-app:latest`.

### Configuración Necesaria
Para que esto funcione, debes añadir los siguientes **Secrets** en tu repositorio de GitHub (`Settings > Secrets and variables > Actions`):

1. `DOCKERHUB_USERNAME`: Tu usuario de Docker Hub (ej: `bernat13`).
2. `DOCKERHUB_TOKEN`: Un [Access Token](https://docs.docker.com/docker-hub/access-tokens/) de Docker Hub (no uses tu contraseña real).

---
> [!IMPORTANT]
> **RECORDATORIO**: La carpeta `pokegame-db-data/` está ignorada en el `.gitignore` por seguridad. ¡Haz copias de seguridad externas de tus datos!
