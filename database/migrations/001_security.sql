-- Requerido para hashes creados por password_hash(). Es una ampliación sin pérdida de datos.
ALTER TABLE usuario MODIFY contrasena VARCHAR(255) NULL;

-- Recuperación de contraseña heredada por el sistema anterior.
CREATE TABLE IF NOT EXISTS recuperacion (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    token VARCHAR(128) NOT NULL,
    fecha DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_recuperacion_token (token),
    KEY idx_recuperacion_usuario_fecha (id_usuario, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_login_log_lookup ON login_log (usuario, ip, exito, fecha);
