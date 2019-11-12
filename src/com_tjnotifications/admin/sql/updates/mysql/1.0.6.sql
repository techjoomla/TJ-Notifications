----Change table Key;

ALTER TABLE `#__tj_notification_user_exclusions` CHANGE KEY `client1` (`client`(100),`provider`(50),`key`(100));
