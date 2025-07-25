-- Estructura de la base de datos para el sistema de inventario
CREATE DATABASE IF NOT EXISTS govil_inventario;
USE govil_inventario;

-- Tabla de telas
CREATE TABLE IF NOT EXISTS telas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL,
    color VARCHAR(50) NOT NULL,
    metros DECIMAL(10,2) NOT NULL,
    costo DECIMAL(10,2) NOT NULL,
    fecha_ingreso DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de productos (uniformes y otros)
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('uniforme','otro') DEFAULT 'uniforme',
    color VARCHAR(50),
    cantidad INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    direccion VARCHAR(200)
);

-- Tabla de órdenes
CREATE TABLE IF NOT EXISTS ordenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

-- Detalle de cada producto en la orden
CREATE TABLE IF NOT EXISTS detalle_orden (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (orden_id) REFERENCES ordenes(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Movimientos de inventario (ingresos y salidas)
CREATE TABLE IF NOT EXISTS movimientos_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT,
    tela_id INT,
    tipo_movimiento ENUM('ingreso','salida') NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    descripcion VARCHAR(255),
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    FOREIGN KEY (tela_id) REFERENCES telas(id)
);

ALTER TABLE productos ADD COLUMN foto VARCHAR(255) AFTER precio; 