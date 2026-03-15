---
description: Cómo subir la imagen de PokéGame a Docker Hub
---

Para subir tu contenedor a Docker Hub y que cualquiera pueda descargarlo, sigue estos pasos:

### 1. Preparar la Imagen
Asegúrate de que el `Dockerfile` incluye los archivos del proyecto (ya lo he actualizado para ti).

### 2. Iniciar Sesión en Docker Hub
Abre una terminal y ejecuta:
```bash
docker login
```
Introduce tu nombre de usuario y contraseña de Docker Hub.

### 3. Construir la Imagen
Sustituye `TU_USUARIO` por tu nombre de usuario de Docker Hub:
```bash
docker build -t TU_USUARIO/pokegame-app:latest .
```

### 4. Subir (Push) a Docker Hub
// turbo
```bash
docker push TU_USUARIO/pokegame-app:latest
```

### 5. Uso en otro Servidor
Ahora, en cualquier `docker-compose.yml` puedes usar la imagen directamente en lugar de construirla localmente:

```yaml
  app:
    image: TU_USUARIO/pokegame-app:latest
    ports:
      - "8080:80"
    environment:
      DB_HOST: db
      DB_NAME: pokegame
    depends_on:
      - db
```

> [!IMPORTANT]
> Recuerda que la base de datos (MariaDB) no necesita subirse, ya es una imagen oficial. Solo necesitas subir la parte de la aplicación que hemos programado.
