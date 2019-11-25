--
-- Notifications Logs Tables : Table structure for table `#__tj_notification_logs`
--
CREATE TABLE IF NOT EXISTS `#__tj_notification_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `client` varchar(100) NOT NULL,
  `provider` varchar(50) NOT NULL,
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
)
 DEFAULT CHARSET=utf8
 AUTO_INCREMENT=0 ;
