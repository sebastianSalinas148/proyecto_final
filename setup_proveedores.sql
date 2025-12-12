
CREATE TABLE IF NOT EXISTS proveedores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empresa VARCHAR(100) NOT NULL UNIQUE,
    contacto VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    producto_servicio VARCHAR(150) NOT NULL,
    estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


INSERT INTO proveedores (empresa, contacto, telefono, email, producto_servicio, estado) VALUES
('Deportes S.A', 'Luis Ramírez', '555-1234', 'luis@deportes.com', 'Balones', 'Activo'),
('Cesped Premium', 'Ana Torres', '555-5678', 'ana@cesped.com', 'Mantenimiento cesped', 'Activo'),
('Iluminación Pro', 'Carlos Vega', '555-9012', 'carlos@iluminacion.com', 'Iluminación', 'Inactivo');
