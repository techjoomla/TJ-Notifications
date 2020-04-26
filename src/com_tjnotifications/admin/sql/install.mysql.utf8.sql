CREATE TABLE IF NOT EXISTS `#__tj_notification_providers` (
  `provider` varchar(100) NOT NULL,
  `state` int(1) NOT NULL,
   primary key(provider)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tj_notification_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `client` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `state` int(11) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_control` int(1) NOT NULL,
  `core` int(1) NOT NULL,
  `replacement_tags` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_2` (`client`,`key`),
  KEY `client` (`client`),
  KEY `key` (`key`)
)AUTO_INCREMENT=1 ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tj_notification_template_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `provider` varchar(30) NOT NULL,
  `language` char(7) NOT NULL DEFAULT '*',
  `subject` text NOT NULL,
  `body` text NOT NULL,
  `params` text NOT NULL,
  `state` int(11) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_override` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `#__tj_notification_template_configs` FOREIGN KEY (`template_id`) REFERENCES `#__tj_notification_templates` (`id`)
)AUTO_INCREMENT=1 ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

 CREATE TABLE IF NOT EXISTS `#__tj_notification_user_exclusions` (
  `user_id` int(11) NOT NULL,
  `client` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `provider` varchar(100) NOT NULL,
   KEY `client1` (`client`(100),`provider`(50),`key`(100)),
   KEY `key` (`key`),
   KEY `provider` (`provider`),
   CONSTRAINT `#__tj_notification_user_exclusions_ibfk_1` FOREIGN KEY (`provider`) REFERENCES `#__tj_notification_providers` (`provider`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
