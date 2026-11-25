-- ============================================================
-- Proyecto Integrador - Inkafarma (Botica Chaclacayo)
-- Script de base de datos: db_farmacia
-- ============================================================

-- 1. Crear la base de datos con juego de caracteres utf8mb4
CREATE DATABASE db_farmacia DEFAULT CHARACTER SET = 'utf8mb4';

-- 2. Usar la base de datos recién creada
USE db_farmacia;

-- 3. Tabla usuarios (acceso al sistema)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,            -- Identificador único del usuario
    usuario VARCHAR(100) NOT NULL UNIQUE,         -- Correo o alias de acceso (no se repite)
    password VARCHAR(255) NOT NULL,               -- Contraseña guardada con hash
    nombre_completo VARCHAR(150) NOT NULL         -- Nombre que se muestra en el menú
) ENGINE=InnoDB;

-- 4. Tabla categorias (clasifica los productos)
CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,  -- Identificador de la categoría
    nombre_categoria VARCHAR(100) NOT NULL        -- Nombre de la categoría
) ENGINE=InnoDB;

-- 5. Tabla insumos_medicos (catálogo e inventario)
CREATE TABLE insumos_medicos (
    id INT AUTO_INCREMENT PRIMARY KEY,            -- Identificador del insumo
    codigo VARCHAR(20) NOT NULL UNIQUE,           -- Código de barras o SKU interno
    nombre VARCHAR(150) NOT NULL,                 -- Nombre comercial y presentación
    precio DECIMAL(10,2) NOT NULL,                -- Precio de venta
    stock INT NOT NULL,                           -- Unidades disponibles
    id_categoria INT NOT NULL,                    -- Enlace con la tabla categorias
    foto VARCHAR(100) NULL,                       -- Nombre del archivo de imagen (opcional)
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
        ON DELETE CASCADE                         -- Si se borra la categoría, se borran sus productos
) ENGINE=InnoDB;

-- 6. Datos iniciales: usuario administrador
--    Usuario: admin@inkafarma.com  |  Contraseña: admin2026
INSERT INTO usuarios (usuario, password, nombre_completo) VALUES
('admin@inkafarma.com', '$2y$10$pN.eyhBr1YioZ/n4YwWBO.zvJUY5y.TuTiguMGW2X7PN8Fkw6xrSC', 'Administrador Inkafarma');

-- 7. Datos iniciales: 3 categorías (IDs 1, 2 y 3)
INSERT INTO categorias (nombre_categoria) VALUES
('Analgésicos'),
('Antibióticos'),
('Cuidado Personal');

-- 8. Datos iniciales: productos de ejemplo
INSERT INTO insumos_medicos (codigo, nombre, precio, stock, id_categoria, foto) VALUES
('INK-001', 'Paracetamol 500mg x 100 tabletas', 12.50, 120, 1, NULL),
('INK-002', 'Amoxicilina 500mg x 20 cápsulas', 18.90, 60, 2, NULL),
('INK-003', 'Ibuprofeno 400mg x 20 tabletas', 9.80, 15, 1, NULL),
('INK-004', 'Naproxeno 550mg x 10 tabletas', 14.20, 45, 1, NULL),
('INK-005', 'Azitromicina 500mg x 3 tabletas', 22.00, 18, 2, NULL),
('INK-006', 'Ciprofloxacino 500mg x 10 tabletas', 16.50, 8, 2, NULL),
('INK-007', 'Alcohol en gel 250 ml', 7.50, 80, 3, NULL),
('INK-008', 'Mascarillas descartables x 50', 18.00, 30, 3, NULL);

-- 9. Consulta de verificación con INNER JOIN
SELECT i.id, i.codigo, i.nombre, i.precio, i.stock, c.nombre_categoria
FROM insumos_medicos i
INNER JOIN categorias c ON i.id_categoria = c.id_categoria;
