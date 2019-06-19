--
-- Notifications Logs Tables : Table structure for table `#__tjnotification_logs`
--
CREATE TABLE IF NOT EXISTS `#__tjnotification_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `client` varchar(100) NOT NULL,
  `provider` varchar(100) NOT NULL,
  `subject` varchar(250) NOT NULL,
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

