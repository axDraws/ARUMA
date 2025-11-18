-- aruma_schema.sql
CREATE DATABASE IF NOT EXISTS aruma_spa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aruma_spa;

-- Usuarios (clientes y administradores)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role ENUM('client','admin') NOT NULL DEFAULT 'client',
  nombre VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  telefono VARCHAR(30),
  password_hash VARCHAR(255) NOT NULL,
  fecha_nac DATE NULL,
  direccion VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Terapeutas
CREATE TABLE therapists (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  especialidad VARCHAR(100),
  telefono VARCHAR(30),
  activo TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Servicios
CREATE TABLE services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  duracion_min INT NOT NULL DEFAULT 60,
  precio DECIMAL(10,2) NOT NULL DEFAULT 0,
  categoria VARCHAR(80),
  activo TINYINT(1) DEFAULT 1,
  descripcion TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Productos
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(180) NOT NULL,
  descripcion TEXT,
  precio DECIMAL(10,2) NOT NULL DEFAULT 0,
  imagen_path VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Reservas
CREATE TABLE reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  servicio_id INT NOT NULL,
  therapist_id INT NULL,
  fecha DATE NOT NULL,
  hora TIME NOT NULL,
  duracion_min INT NULL,
  estado ENUM('pendiente','confirmada','en_proceso','completada','cancelada') DEFAULT 'pendiente',
  notas TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (servicio_id) REFERENCES services(id) ON DELETE RESTRICT,
  FOREIGN KEY (therapist_id) REFERENCES therapists(id) ON DELETE SET NULL,
  INDEX (cliente_id),
  INDEX (fecha)
) ENGINE=InnoDB;

-- Historial (registro de eventos, pagos, cambios de estado)
CREATE TABLE history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reservation_id INT NULL,
  user_id INT NULL,
  evento VARCHAR(200) NOT NULL,
  detalle TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Seeds mínimos de ejemplo
INSERT INTO users (role, nombre, email, telefono, password_hash) VALUES
('admin','Admin Aruma','admin@aruma.local','+52 000 000 0000', '$2y$10$EXAMPLEHASHADMIN0000000000000000000000000000'),
('client','Juan Pérez','juan@ejemplo.com','+52 555 123 4567', '$2y$10$EXAMPLEHASHCLIENT00000000000000000000000000');

INSERT INTO therapists (nombre, especialidad, telefono) VALUES
('María García','Masajes','+52 111 111 1111'),
('Ana López','Faciales','+52 222 222 2222');

INSERT INTO services (nombre, duracion_min, precio, categoria, descripcion) VALUES
('Masaje Relajante',60,800,'Masajes','Masaje relajante para aliviar tensiones'),
('Tratamiento Facial',90,1000,'Faciales','Tratamiento nutritivo y renovador'),
('Hidroterapia',45,600,'Terapias','Sesión de hidroterapia');

INSERT INTO products (nombre, descripcion, precio) VALUES
('Acondicionador Bio Marine','Acondicionador profesional Salerm',335.00),
('Mascarilla Multi Proteinas','Mascarilla reparadora',399.00);

