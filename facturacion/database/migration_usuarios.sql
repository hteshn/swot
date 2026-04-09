-- Script de migración para agregar nuevos campos a la tabla de usuarios
-- Ejecutar este script si ya existe la base de datos

USE facturacion_db;

-- Verificar si la columna 'celular' existe antes de agregarla
SET @dbname = DATABASE();
SET @tablename = 'usuarios';
SET @columnname = 'celular';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_NAME = @tablename
      AND TABLE_SCHEMA = @dbname
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 'Columna celular ya existe'",
  "ALTER TABLE usuarios ADD COLUMN celular VARCHAR(20) AFTER nombre_completo"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Verificar si la columna 'edad' existe antes de agregarla
SET @columnname = 'edad';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_NAME = @tablename
      AND TABLE_SCHEMA = @dbname
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 'Columna edad ya existe'",
  "ALTER TABLE usuarios ADD COLUMN edad INT AFTER celular"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Verificar si la columna 'pais' existe antes de agregarla
SET @columnname = 'pais';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_NAME = @tablename
      AND TABLE_SCHEMA = @dbname
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 'Columna pais ya existe'",
  "ALTER TABLE usuarios ADD COLUMN pais VARCHAR(50) AFTER edad"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Verificar si la columna 'departamento' existe antes de agregarla
SET @columnname = 'departamento';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_NAME = @tablename
      AND TABLE_SCHEMA = @dbname
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 'Columna departamento ya existe'",
  "ALTER TABLE usuarios ADD COLUMN departamento VARCHAR(100) AFTER pais"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Mostrar estructura actualizada de la tabla usuarios
DESCRIBE usuarios;

-- Actualizar datos de ejemplo con información completa
UPDATE usuarios 
SET 
    celular = '+54-11-9876-5432',
    edad = 35,
    pais = 'Argentina',
    departamento = 'Buenos Aires'
WHERE username = 'admin';

UPDATE usuarios 
SET 
    celular = '+54-11-1234-5678',
    edad = 28,
    pais = 'Argentina',
    departamento = 'Córdoba'
WHERE username = 'vendedor1';

-- Mostrar usuarios actualizados
SELECT * FROM usuarios;
