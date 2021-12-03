CREATE TABLE IF NOT EXISTS `#__tj_notification_providers` (
  `provider` varchar(100) NOT NULL DEFAULT '',
  `state` int(1) NOT NULL DEFAULT 0,
   primary key(provider)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tj_notification_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL DEFAULT '',
  `client` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `state` int(11) NOT NULL DEFAULT 0,
  `created_on` datetime NULL DEFAULT NULL,
  `updated_on` datetime NULL DEFAULT NULL,
  `user_control` int(1) NOT NULL DEFAULT 0,
  `core` int(1) NOT NULL DEFAULT 0,
  `replacement_tags` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_2` (`client`,`key`),
  KEY `client` (`client`),
  KEY `key` (`key`)
) AUTO_INCREMENT=1 ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tj_notification_template_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL DEFAULT 0,
  `backend` varchar(50) NOT NULL DEFAULT '',
  `language` char(7) NOT NULL DEFAULT '*',
  `subject` text DEFAULT NULL,
  `body` text DEFAULT NULL,
  `params` text DEFAULT NULL,
  `state` int(11) NOT NULL DEFAULT 0,
  `created_on` datetime NULL DEFAULT NULL,
  `updated_on` datetime NULL DEFAULT NULL,
  `is_override` int(1) NOT NULL DEFAULT 0,
  `provider_template_id` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  CONSTRAINT `#__tj_notification_template_configs` FOREIGN KEY (`template_id`) REFERENCES `#__tj_notification_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

 CREATE TABLE IF NOT EXISTS `#__tj_notification_user_exclusions` (
  `user_id` int(11) NOT NULL DEFAULT 0,
  `client` varchar(100) NOT NULL DEFAULT '',
  `key` varchar(100) NOT NULL DEFAULT '',
  `provider` varchar(100) NOT NULL DEFAULT '',
   KEY `client1` (`client`(100),`provider`(50),`key`(100)),
   KEY `key` (`key`),
   KEY `provider` (`provider`),
   CONSTRAINT `#__tj_notification_user_exclusions_ibfk_1` FOREIGN KEY (`provider`) REFERENCES `#__tj_notification_providers` (`provider`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tj_notification_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL DEFAULT '',
  `client` varchar(100) NOT NULL DEFAULT '',
  `backend` varchar(50) NOT NULL DEFAULT '',
  `subject` varchar(250) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `body` text DEFAULT NULL,
  `from` varchar(500) NOT NULL DEFAULT '',
  `to` text DEFAULT NULL,
  `cc` text DEFAULT NULL,
  `bcc` text DEFAULT NULL,
  `date` datetime NULL DEFAULT NULL,
  `state` tinyint(2) NOT NULL DEFAULT 0,
  `params` text DEFAULT NULL,
  `priority`int(11) NOT NULL DEFAULT 0,
  `message` text DEFAULT NULL,
  `category` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__tjnotifications_subscriptions` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL ,
  `user_id` int(11) DEFAULT NULL,
  `backend` varchar(50) NOT NULL DEFAULT '',
  `address` text DEFAULT NULL,
  `device_id` text DEFAULT NULL,
  `platform` varchar(50) DEFAULT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `is_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified_by` int(11) NOT NULL DEFAULT 0,
  `checked_out` int(11) NOT NULL DEFAULT 0,
  `created_on` datetime NULL DEFAULT NULL,
  `updated_on` datetime NULL DEFAULT NULL,
  `checked_out_time` datetime NULL DEFAULT NULL,
  `params` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;
