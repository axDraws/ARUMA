CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role_id INT NOT NULL,  -- Clave Foránea para el rol
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Definición de la Clave Foránea
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT 
    -- Se recomienda RESTRICT o NO ACTION para evitar borrar roles con usuarios asignados.
);

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE COMMENT 'Ej: Administrador, Usuario Estándar, Editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE services (
    id_servicios INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE appointments (
    apppointments_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    status ENUM('pending', 'confirmed', 'canceled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Asegúrate de que el primer rol insertado sea el Administrador para que su ID sea 1
INSERT INTO roles (role_name) VALUES ('Administrador'); 
INSERT INTO roles (role_name) VALUES ('Usuario Estándar');
INSERT INTO roles (role_name) VALUES ('Invitado');

-- Ahora, 'Administrador' tendrá ID = 1.