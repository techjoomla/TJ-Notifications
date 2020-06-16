ALTER TABLE `#__tj_notification_templates` MODIFY `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__tj_notification_templates` MODIFY `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';

CREATE TABLE IF NOT EXISTS `#__tj_notification_template_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `backend` varchar(50) NOT NULL,
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

CREATE TABLE IF NOT EXISTS `#__tj_notification_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `client` varchar(100) NOT NULL,
  `backend` varchar(50) NOT NULL,
  `subject` varchar(250) NOT NULL,
  `title` varchar(100) NOT NULL,
  `body` text NOT NULL,
  `from` varchar(500) NOT NULL,
  `to` text NOT NULL,
  `cc` text NOT NULL,
  `bcc` text NOT NULL,
  `date` datetime NOT NULL,
  `state` tinyint(2) NOT NULL,
  `params` text NOT NULL,
  `priority`int(11) NOT NULL,
  `message` text NOT NULL,
  `category` text NOT NULL,
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tjnotifications_subscriptions` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` varchar(255) DEFAULT NULL
	`user_id` int(11) DEFAULT NULL,
	`backend` varchar(50) NOT NULL,
	`address` text DEFAULT NULL,
  `device_id` text DEFAULT NULL,
  `platform` varchar(50) DEFAULT NULL,
	`state` tinyint(1) NOT NULL,
	`is_confirmed` tinyint(1) NOT NULL,
	`created_by` int(11) NOT NULL,
	`modified_by` int(11) NOT NULL,
	`checked_out` int(11) NOT NULL,
	`created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`params` text DEFAULT NULL,
	PRIMARY KEY (`id`),
    KEY `user_id_idx` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;