----Create new table __tj_notification_template_configs;

CREATE TABLE IF NOT EXISTS `#__tj_notification_template_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `provider` varchar(30) NOT NULL,
  `language` CHAR(7) NOT NULL DEFAULT '*', 
  `subject` text NOT NULL,
  `body` text NOT NULL,
  `params` text NOT NULL,
  `state` int(11) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_override` int(1) NOT NULL
  PRIMARY KEY (`id`),
  CONSTRAINT `#__tj_notification_template_configs` FOREIGN KEY (`template_id`) REFERENCES `#__tj_notification_templates` (`id`)
)AUTO_INCREMENT=1 ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `#__tj_notification_templates` MODIFY `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__tj_notification_templates` MODIFY `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
