-- MariaDB provides the root user via environment variables in Docker
-- We just need to ensure the admin database exists and the local grants are correct if needed,
-- but standard Docker setup handles root access.

FLUSH PRIVILEGES;

CREATE DATABASE IF NOT EXISTS pokegame_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE pokegame_admin;

CREATE TABLE IF NOT EXISTS clases (
    id_clase INT AUTO_INCREMENT PRIMARY KEY,
    nombre_clase VARCHAR(100) NOT NULL,
    db_name VARCHAR(64) NOT NULL UNIQUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS usuarios_clases (
    db_usuario VARCHAR(50) PRIMARY KEY,
    db_name VARCHAR(64) NOT NULL,
    FOREIGN KEY (db_name) REFERENCES clases (db_name) ON DELETE CASCADE
);

-- Global Role for all trainers
CREATE ROLE IF NOT EXISTS 'entrenador_role';

GRANT SELECT ON pokegame_admin.usuarios_clases TO 'entrenador_role';

-- Grant role to root and set as default (for DBeaver and role management)
GRANT 'entrenador_role' TO 'root' @'%' WITH ADMIN OPTION;

SET DEFAULT ROLE 'entrenador_role' FOR 'root' @'%';

FLUSH PRIVILEGES;