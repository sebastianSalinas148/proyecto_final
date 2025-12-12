
CREATE TABLE IF NOT EXISTS canchas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(50),
    capacidad INT,
    precio_hora DECIMAL(10, 2) NOT NULL,
    disponible BOOLEAN DEFAULT TRUE,
    imagen VARCHAR(255),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS reservas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    cancha_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    cantidad_personas INT,
    total DECIMAL(10, 2) NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'cancelada') DEFAULT 'confirmada',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY(cancha_id) REFERENCES canchas(id) ON DELETE CASCADE,
    INDEX(usuario_id),
    INDEX(cancha_id),
    INDEX(fecha)
);


INSERT INTO canchas (nombre, descripcion, tipo, capacidad, precio_hora, disponible) VALUES
('Cancha Principal', 'Cancha de fútbol 11 con superficie de pasto sintético de alta calidad', 'Fútbol 11', 22, 50.00, TRUE),
('Cancha Sintética A', 'Cancha de fútbol 7 con superficie sintética profesional', 'Fútbol 7', 14, 35.00, TRUE),
('Cancha Indoor', 'Cancha de fútbol 5 techada con piso de madera', 'Fútbol 5', 10, 25.00, TRUE),
('Cancha de Entrenamiento', 'Cancha auxiliar para entrenamientos y práctica', 'Fútbol 7', 14, 30.00, TRUE);
