DROP TABLE IF EXISTS `#__timeline_items`;

CREATE TABLE `#__timeline_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `date` DATETIME DEFAULT NULL,
  `state` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = published, 0 = unpublished, 2 = archived, -2 = trashed',
  `ordering` INT(11) DEFAULT 0,
  `created_by` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checked_out` INT(10) UNSIGNED DEFAULT NULL,
  `checked_out_time` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
