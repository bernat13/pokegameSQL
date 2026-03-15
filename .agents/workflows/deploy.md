---
description: Cómo desplegar PokéGame en un servidor (HTTPS Interno)
---

Para desplegar la aplicación en un servidor usando una IP interna con HTTPS cifrado (autofirmado), sigue estos pasos.

### Requisitos Previos
- Tener instalado **Docker** y **Docker Compose** en el servidor.
- Puertos **80** y **443** abiertos en el firewall del servidor.

### Pasos para el Despliegue

1. **Subir los Archivos al Servidor**
   Copia estos archivos y carpetas a una carpeta en tu servidor:
   - `docker-compose-prod.yml`
   - `nginx_conf/` (carpeta que contiene `default.conf`)
   - `ssl/` (carpeta que contiene `nginx.crt` y `nginx.key`)
   - `pokegameSQL.sql`

2. **Levantar los Contenedores**
   Ejecuta el siguiente comando en la carpeta donde has subido los archivos:
   // turbo
   ```bash
   docker compose -f docker-compose-prod.yml up -d
   ```

3. **Inicialización de la Base de Datos**
   La base de datos se cargará automáticamente la primera vez. Si ya tenías una base de datos funcionando, los datos se mantendrán gracias al volumen persistente.

### Acceso
Accede mediante:
`https://LA_IP_DE_TU_SERVIDOR`

> [!WARNING]
> Al usar un certificado autofirmado, el navegador mostrará un aviso de seguridad. Debes pulsar en **"Opciones avanzadas"** y luego en **"Acceder a... (no seguro)"**. Esto es normal en entornos internos sin dominio.

### Mantenimiento
- **Ver logs**: `docker compose logs -f`
- **Reiniciar**: `docker compose restart`
- **Actualizar cambios**: Si subes una nueva versión a Docker Hub, ejecuta:
  ```bash
  docker compose -f docker-compose-prod.yml pull
  docker compose -f docker-compose-prod.yml up -d
  ```
