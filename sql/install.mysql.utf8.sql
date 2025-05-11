-- -----------------------------------------------------
-- Table `#__lineadetiempo_eventos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__lineadetiempo_eventos` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(255) NOT NULL COMMENT 'Título del evento',
  `descripcion` TEXT NOT NULL COMMENT 'Descripción detallada',
  `fecha` DATE NOT NULL COMMENT 'Fecha del evento (YYYY-MM-DD)',
  
  -- Campos para multimedia
  `tipo_media` ENUM('imagen','video','embed') NOT NULL DEFAULT 'imagen' COMMENT 'Tipo de contenido multimedia',
  `url_media` VARCHAR(2048) NULL COMMENT 'URL de imagen/video',
  `texto_media` TEXT NULL COMMENT 'Texto alternativo o descripción de media',
  `creditos_media` VARCHAR(255) NULL COMMENT 'Créditos/autor del media',
  
  -- Metadatos y orden
  `orden` INT(11) NOT NULL DEFAULT 0 COMMENT 'Orden de visualización',
  `publicado` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Estado de publicación',
  
  -- Auditoría
  `creado` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `creado_por` INT(11) NOT NULL COMMENT 'Usuario que creó el registro',
  `modificado` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  `modificado_por` INT(11) NULL COMMENT 'Usuario que modificó el registro',
  
  PRIMARY KEY (`id`),
  
  -- Índices para optimización
  INDEX `idx_fecha` (`fecha`),
  INDEX `idx_publicado` (`publicado`),
  INDEX `idx_orden` (`orden`),
  
  -- Claves foráneas
  FOREIGN KEY (`creado_por`) 
    REFERENCES `#__users`(`id`)
    ON DELETE CASCADE,
    
  FOREIGN KEY (`modificado_por`) 
    REFERENCES `#__users`(`id`)
    ON DELETE SET NULL
    
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci 
COMMENT='Almacena los eventos de la línea de tiempo';