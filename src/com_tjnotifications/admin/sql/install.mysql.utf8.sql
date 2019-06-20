CREATE TABLE IF NOT EXISTS `#__tj_notification_providers` (
  `provider` varchar(100) NOT NULL,
  `state` int(1) NOT NULL,
   primary key(provider)
 )
  AUTO_INCREMENT =0
  DEFAULT CHARSET =utf8;


CREATE TABLE IF NOT EXISTS `#__tj_notification_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `email_status` int(1) NOT NULL,
  `sms_status` int(1) NOT NULL,
  `push_status` int(1) NOT NULL,
  `web_status` int(1) NOT NULL,
  `email_body` text NOT NULL,
  `sms_body` text NOT NULL,
  `push_body` text NOT NULL,
  `web_body` text NOT NULL,
  `email_subject` text NOT NULL,
  `sms_subject` text NOT NULL,
  `push_subject` text NOT NULL,
  `web_subject` text NOT NULL,
  `state` int(11) NOT NULL,
  `created_on` date NOT NULL,
  `updated_on` date NOT NULL,
  `is_override` int(1) NOT NULL,
  `user_control` int(1) NOT NULL,
  `core` int(1) NOT NULL,
  `replacement_tags` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_2` (`client`,`key`),
  KEY `client` (`client`),
  KEY `key` (`key`)
)
 DEFAULT CHARSET=utf8
 AUTO_INCREMENT=0 ;

 CREATE TABLE IF NOT EXISTS `#__tj_notification_user_exclusions` (
  `user_id` int(11) NOT NULL,
  `client` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `provider` varchar(100) NOT NULL,
   KEY `client1` (`client`,`provider`,`key`),
   KEY `key` (`key`),
   KEY `provider` (`provider`),
   CONSTRAINT `#__tj_notification_user_exclusions_ibfk_1` FOREIGN KEY (`provider`) REFERENCES `#__tj_notification_providers` (`provider`)
)
 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__tjnotification_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `client` varchar(100) NOT NULL,
  `provider` varchar(100) NOT NULL,
  `subject` varchar(250) NOT NULL,
  `title` varchar(100) NOT NULL,
  `body` text NOT NULL,
  `from` varchar(100) NOT NULL,
  `to` varchar(100) NOT NULL,
  `cc` varchar(100) NOT NULL,
  `bcc` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `state` int(11) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client` (`client`),
  KEY `key` (`key`)
)
 DEFAULT CHARSET=utf8
 AUTO_INCREMENT=0 ;
