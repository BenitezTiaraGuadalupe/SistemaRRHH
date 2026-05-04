-- MySQL Workbench Forward Engineering (FK alineadas: PK sólo sobre id donde aplica)



SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';



-- -----------------------------------------------------

-- Schema talent_link ( coincide con DB_NAME en config )

-- -----------------------------------------------------

CREATE SCHEMA IF NOT EXISTS `talent_link` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `talent_link` ;





CREATE TABLE IF NOT EXISTS `talent_link`.`roles` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `nombre` VARCHAR(45) NOT NULL,

  PRIMARY KEY (`id`))

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`usuarios` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `roles_id` INT NOT NULL,

  `correo` VARCHAR(100) NOT NULL,

  `password` VARCHAR(45) NOT NULL,

  PRIMARY KEY (`id`),

  INDEX `fk_usuarios_roles1_idx` (`roles_id`),

  CONSTRAINT `fk_usuarios_roles1`

    FOREIGN KEY (`roles_id`)

    REFERENCES `talent_link`.`roles` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION)

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`empresas` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `nombre` VARCHAR(100) NOT NULL,

  `usuarios_id` INT NOT NULL,

  PRIMARY KEY (`id`),

  INDEX `fk_empresas_usuarios1_idx` (`usuarios_id`),

  CONSTRAINT `fk_empresas_usuarios1`

    FOREIGN KEY (`usuarios_id`)

    REFERENCES `talent_link`.`usuarios` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION)

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`paises` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `nombre` VARCHAR(100) NOT NULL,

  PRIMARY KEY (`id`))

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`provincias` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `nombre` VARCHAR(100) NOT NULL,

  `paises_id` INT NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE INDEX `uq_provincias_nombre_pais` (`nombre`, `paises_id`),

  INDEX `fk_provincias_paises1_idx` (`paises_id`),

  CONSTRAINT `fk_provincias_paises1`

    FOREIGN KEY (`paises_id`)

    REFERENCES `talent_link`.`paises` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION)

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`ciudades` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `nombre` VARCHAR(100) NOT NULL,

  `provincias_id` INT NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE INDEX `uq_ciudades_nombre_prov` (`nombre`, `provincias_id`),

  INDEX `fk_ciudades_provincias1_idx` (`provincias_id`),

  CONSTRAINT `fk_ciudades_provincias1`

    FOREIGN KEY (`provincias_id`)

    REFERENCES `talent_link`.`provincias` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION)

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`candidatos` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `usuarios_id` INT NOT NULL,

  `nombre` VARCHAR(45) NOT NULL,

  `apellido` VARCHAR(45) NOT NULL,

  `fecha_nac` DATETIME NOT NULL,

  `ciudades_id` INT NOT NULL,

  PRIMARY KEY (`id`),

  INDEX `fk_candidatos_usuarios_idx` (`usuarios_id`),

  INDEX `fk_candidatos_ciudades1_idx` (`ciudades_id`),

  CONSTRAINT `fk_candidatos_usuarios`

    FOREIGN KEY (`usuarios_id`)

    REFERENCES `talent_link`.`usuarios` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION,

  CONSTRAINT `fk_candidatos_ciudades1`

    FOREIGN KEY (`ciudades_id`)

    REFERENCES `talent_link`.`ciudades` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION)

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`estado_busqueda` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `nombre` VARCHAR(45) NOT NULL,

  PRIMARY KEY (`id`))

ENGINE = InnoDB;





-- Nota: se elimina empresas_usuarios_id; empresa se identifica sólo por empresas_id.

CREATE TABLE IF NOT EXISTS `talent_link`.`busquedas` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `nombre_puesto` VARCHAR(100) NOT NULL,

  `empresas_id` INT NOT NULL,

  `estado_busqueda_id` INT NOT NULL,

  PRIMARY KEY (`id`),

  INDEX `fk_busquedas_empresas1_idx` (`empresas_id`),

  INDEX `fk_busquedas_estado_busqueda1_idx` (`estado_busqueda_id`),

  CONSTRAINT `fk_busquedas_empresas1`

    FOREIGN KEY (`empresas_id`)

    REFERENCES `talent_link`.`empresas` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION,

  CONSTRAINT `fk_busquedas_estado_busqueda1`

    FOREIGN KEY (`estado_busqueda_id`)

    REFERENCES `talent_link`.`estado_busqueda` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION)

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`personal_rrhh` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `usuarios_id` INT NOT NULL,

  `nombre` VARCHAR(45) NOT NULL,

  `apellido` VARCHAR(45) NOT NULL,

  PRIMARY KEY (`id`),

  INDEX `fk_personal_rrhh_usuarios1_idx` (`usuarios_id`),

  CONSTRAINT `fk_personal_rrhh_usuarios1`

    FOREIGN KEY (`usuarios_id`)

    REFERENCES `talent_link`.`usuarios` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION)

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`estado_ofertas` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `nombre` VARCHAR(45) NOT NULL,

  PRIMARY KEY (`id`))

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`ofertas` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `busquedas_id` INT NOT NULL,

  `personal_rrhh_id` INT NOT NULL,

  `estado_ofertas_id` INT NOT NULL,

  PRIMARY KEY (`id`),

  INDEX `fk_ofertas_busquedas1_idx` (`busquedas_id`),

  INDEX `fk_ofertas_personal_rrhh1_idx` (`personal_rrhh_id`),

  INDEX `fk_ofertas_estado_ofertas1_idx` (`estado_ofertas_id`),

  CONSTRAINT `fk_ofertas_busquedas1`

    FOREIGN KEY (`busquedas_id`)

    REFERENCES `talent_link`.`busquedas` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION,

  CONSTRAINT `fk_ofertas_personal_rrhh1`

    FOREIGN KEY (`personal_rrhh_id`)

    REFERENCES `talent_link`.`personal_rrhh` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION,

  CONSTRAINT `fk_ofertas_estado_ofertas1`

    FOREIGN KEY (`estado_ofertas_id`)

    REFERENCES `talent_link`.`estado_ofertas` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION)

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`etapas` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `nombre` VARCHAR(45) NOT NULL,

  PRIMARY KEY (`id`))

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`postulaciones` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `ofertas_id` INT NOT NULL,

  `etapas_id` INT NOT NULL,

  PRIMARY KEY (`id`),

  INDEX `fk_postulaciones_ofertas1_idx` (`ofertas_id`),

  INDEX `fk_postulaciones_etapas1_idx` (`etapas_id`),

  CONSTRAINT `fk_postulaciones_ofertas1`

    FOREIGN KEY (`ofertas_id`)

    REFERENCES `talent_link`.`ofertas` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION,

  CONSTRAINT `fk_postulaciones_etapas1`

    FOREIGN KEY (`etapas_id`)

    REFERENCES `talent_link`.`etapas` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION)

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`habilidades` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `nombre` VARCHAR(100) NOT NULL,

  `version` VARCHAR(45) NULL,

  `descripcion` VARCHAR(100) NULL,

  PRIMARY KEY (`id`))

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`habilidades_por_busqueda` (

  `busquedas_id` INT NOT NULL,

  `habilidades_id` INT NOT NULL,

  PRIMARY KEY (`busquedas_id`, `habilidades_id`),

  INDEX `fk_busquedas_has_habilidades_habilidades1_idx` (`habilidades_id`),

  CONSTRAINT `fk_busquedas_has_habilidades_busquedas1`

    FOREIGN KEY (`busquedas_id`)

    REFERENCES `talent_link`.`busquedas` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION,

  CONSTRAINT `fk_busquedas_has_habilidades_habilidades1`

    FOREIGN KEY (`habilidades_id`)

    REFERENCES `talent_link`.`habilidades` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION)

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`permisos` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `nombre` VARCHAR(100) NOT NULL,

  PRIMARY KEY (`id`))

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`permisos_por_roles` (

  `permisos_id` INT NOT NULL,

  `roles_id` INT NOT NULL,

  PRIMARY KEY (`permisos_id`, `roles_id`),

  INDEX `fk_permisos_has_roles_roles1_idx` (`roles_id`),

  CONSTRAINT `fk_permisos_has_roles_permisos1`

    FOREIGN KEY (`permisos_id`)

    REFERENCES `talent_link`.`permisos` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION,

  CONSTRAINT `fk_permisos_has_roles_roles1`

    FOREIGN KEY (`roles_id`)

    REFERENCES `talent_link`.`roles` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION)

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`postulaciones_por_candidatos` (

  `postulaciones_id` INT NOT NULL,

  `candidatos_id` INT NOT NULL,

  PRIMARY KEY (`postulaciones_id`, `candidatos_id`),

  INDEX `fk_postulaciones_has_candidatos_candidatos1_idx` (`candidatos_id`),

  CONSTRAINT `fk_postulaciones_has_candidatos_postulaciones1`

    FOREIGN KEY (`postulaciones_id`)

    REFERENCES `talent_link`.`postulaciones` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION,

  CONSTRAINT `fk_postulaciones_has_candidatos_candidatos1`

    FOREIGN KEY (`candidatos_id`)

    REFERENCES `talent_link`.`candidatos` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION)

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`modalidades` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `nombre` VARCHAR(45) NOT NULL,

  PRIMARY KEY (`id`))

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`detalle_busquedas` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `busquedas_id` INT NOT NULL,

  `descripcion` VARCHAR(500) NULL,

  `cantidad_vacantes` INT NOT NULL,

  `anios_experiencia` INT NULL,

  `modalidades_id` INT NOT NULL,

  `ciudades_id` INT NULL,

  `provincias_id` INT NULL,

  `paises_id` INT NULL,

  PRIMARY KEY (`id`),

  INDEX `fk_detalle_busquedas_busquedas1_idx` (`busquedas_id`),

  INDEX `fk_detalle_busquedas_modalidades1_idx` (`modalidades_id`),

  INDEX `fk_detalle_busquedas_ciudades1_idx` (`ciudades_id`),

  INDEX `fk_detalle_busquedas_provincias1_idx` (`provincias_id`),

  INDEX `fk_detalle_busquedas_paises1_idx` (`paises_id`),

  CONSTRAINT `fk_detalle_busquedas_busquedas1`

    FOREIGN KEY (`busquedas_id`)

    REFERENCES `talent_link`.`busquedas` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION,

  CONSTRAINT `fk_detalle_busquedas_modalidades1`

    FOREIGN KEY (`modalidades_id`)

    REFERENCES `talent_link`.`modalidades` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION,

  CONSTRAINT `fk_detalle_busquedas_ciudades1`

    FOREIGN KEY (`ciudades_id`)

    REFERENCES `talent_link`.`ciudades` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION,

  CONSTRAINT `fk_detalle_busquedas_provincias1`

    FOREIGN KEY (`provincias_id`)

    REFERENCES `talent_link`.`provincias` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION,

  CONSTRAINT `fk_detalle_busquedas_paises1`

    FOREIGN KEY (`paises_id`)

    REFERENCES `talent_link`.`paises` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION)

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`habilidades_candidatos` (

  `habilidades_id` INT NOT NULL,

  `candidatos_id` INT NOT NULL,

  PRIMARY KEY (`habilidades_id`, `candidatos_id`),

  INDEX `fk_habilidades_has_candidatos_candidatos1_idx` (`candidatos_id`),

  CONSTRAINT `fk_habilidades_has_candidatos_habilidades1`

    FOREIGN KEY (`habilidades_id`)

    REFERENCES `talent_link`.`habilidades` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION,

  CONSTRAINT `fk_habilidades_has_candidatos_candidatos1`

    FOREIGN KEY (`candidatos_id`)

    REFERENCES `talent_link`.`candidatos` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION)

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`versiones` (

  `id` INT NOT NULL AUTO_INCREMENT,

  `version` VARCHAR(45) NOT NULL,

  PRIMARY KEY (`id`))

ENGINE = InnoDB;





CREATE TABLE IF NOT EXISTS `talent_link`.`versiones_por_habilidades` (

  `versiones_id` INT NOT NULL,

  `habilidades_id` INT NOT NULL,

  PRIMARY KEY (`versiones_id`, `habilidades_id`),

  INDEX `fk_versiones_has_habilidades_habilidades1_idx` (`habilidades_id`),

  CONSTRAINT `fk_versiones_has_habilidades_versiones1`

    FOREIGN KEY (`versiones_id`)

    REFERENCES `talent_link`.`versiones` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION,

  CONSTRAINT `fk_versiones_has_habilidades_habilidades1`

    FOREIGN KEY (`habilidades_id`)

    REFERENCES `talent_link`.`habilidades` (`id`)

    ON DELETE NO ACTION

    ON UPDATE NO ACTION)

ENGINE = InnoDB;





SET SQL_MODE=@OLD_SQL_MODE;

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;


