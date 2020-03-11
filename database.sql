CREATE DATABASE IF NOT EXISTS tec_materiales;

USE tec_materiales;

CREATE TABLE users(
  id                  int(255) auto_increment not null,
  nombre              varchar(50) NOT NULL,
  apellidos           varchar(100),
  nick                varchar(255) NOT NULL,
  rol                 varchar(20),
  correo              varchar(255) NOT NULL,
  contrasena          varchar(255) NOT NULL,
  numero_control      varchar(255) NOT NULL,
  departmento         varchar(255),
  clave_electronica   varchar(255),
  descripcion         text,
  imagen              varchar(255),
  created_at          datetime DEFAULT NULL,
  updated_at          datetime DEFAULT NULL,
  remember_token      varchar(255),
  CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDB;

CREATE TABLE categorias(
  id                  int(255) auto_increment NOT NULL,
  nombre              varchar(100),
  created_at          datetime DEFAULT NULL,
  updated_at          datetime DEFAULT NULL,
  CONSTRAINT pk_categorias PRIMARY KEY(id)
)ENGINE=InnoDB;

CREATE TABLE departamentos(
  id                    int(255) auto_increment NOT NULL,
  nombre                varchar(100) NOT NULL,
  direccion             varchar(255) NOT NULL,
  telefono              int(100),
  correo                int(255),
  created_at            datetime DEFAULT NULL,
  updated_at            datetime DEFAULT NULL,
  CONSTRAINT pk_departamentos PRIMARY KEY(id)
)ENGINE=InnoDB;


CREATE TABLE salidas(
  id                    int(255) auto_increment NOT NULL,
  usuario_id            int(255) NOT NULL,
  vehiculo_id           int(255) NOT NULL,
  titulo                varchar(255) NOT NULL,
  contenido             text NOT NULL,
  imagen                varchar(255),
  status                varchar(255),
  fecha                 Date DEFAULT NULL,
  hora_inicio           Time DEFAULT NULL,
  hora_final            Time DEFAULT NULL,
  created_at            datetime DEFAULT NULL,
  updated_at            datetime DEFAULT NULL,
  CONSTRAINT pk_salidas PRIMARY KEY(id),
  CONSTRAINT fk_salida_usuario FOREIGN KEY(usuario_id) REFERENCES users(id),
  CONSTRAINT fk_salida_vehiculo FOREIGN KEY(vehiculo_id) REFERENCES vehiculos(id)
)ENGINE=InnoDB;


CREATE TABLE mantenimiento(
  id                    int(255) auto_increment NOT NULL,
  usuario_id            int(255) NOT NULL,
  lugar_id              int(255) NOT NULL,
  titulo                varchar(255) NOT NULL,
  contenido             text NOT NULL,
  status                varchar(255),
  fecha                 Date DEFAULT NULL,
  hora_inicio           Time DEFAULT NULL,
  hora_final            Time DEFAULT NULL,
  imagen                varchar(255),
  created_at            datetime DEFAULT NULL,
  updated_at            datetime DEFAULT NULL,
  CONSTRAINT pk_mantenimiento PRIMARY KEY(id),
  CONSTRAINT fk_mantenimiento_usuario FOREIGN KEY(usuario_id) REFERENCES users(id),
  CONSTRAINT fk_mantenimiento_lugar FOREIGN KEY(lugar_id) REFERENCES lugares(id)
)ENGINE=InnoDB;



CREATE TABLE eventos(
  id                    int(255) auto_increment NOT NULL,
  usuario_id            int(255) NOT NULL,
  lugar_id              int(255) NOT NULL,
  titulo                varchar(255) NOT NULL,
  contenido             text NOT NULL,
  imagen                varchar(255),
  status                varchar(255),
  distribucion          varchar(255),
  fecha                 Date DEFAULT NULL,
  hora_inicio           Time DEFAULT NULL,
  hora_final            Time DEFAULT NULL,
  created_at            datetime DEFAULT NULL,
  updated_at            datetime DEFAULT NULL,
  CONSTRAINT pk_eventos PRIMARY KEY(id),
  CONSTRAINT fk_evento_usuario FOREIGN KEY(usuario_id) REFERENCES users(id),
  CONSTRAINT fk_evento_lugar FOREIGN KEY(lugar_id) REFERENCES lugares(id)
)ENGINE=InnoDB;

CREATE TABLE lugares(
  id                    int(255) auto_increment NOT NULL,
  lugar                 varchar(255) NOT NULL,
  ubicacion             varchar(255),
  status                varchar(255),
  imagen                varchar(255),
  created_at            datetime DEFAULT NULL,
  updated_at            datetime DEFAULT NULL,
CONSTRAINT pk_lugares PRIMARY KEY(id)
)ENGINE=InnoDB;

CREATE TABLE vehiculos(
  id                    int(255) auto_increment NOT NULL,
  vehiculo              varchar(255) NOT NULL,
  marca                 varchar(255) NOT NULL,
  modelo                varchar(255) NOT NULL,
  placas                varchar(255) NOT NULL,
  status                varchar(255),
  kilometraje           varchar(255),
  imagen                varchar(255),
  fecha_mantenimiento   varchar(255),
  created_at            datetime DEFAULT NULL,
  updated_at            datetime DEFAULT NULL,
CONSTRAINT pk_vehiculos PRIMARY KEY(id)
)ENGINE=InnoDB;
