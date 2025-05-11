CREATE TABLE `#__lineadetiempo_eventos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(255) NOT NULL,
  `descripcion` TEXT,
  `imagen` VARCHAR(255),
  `fecha` DATE NOT NULL,
  `red_social` ENUM('twitter', 'facebook', 'instagram', 'youtube') DEFAULT NULL,
  `url_redsocial` VARCHAR(255),
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `published` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `#__lineadetiempo_eventos` ADD `ordering` INT(11) NOT NULL DEFAULT 0 AFTER `published`;