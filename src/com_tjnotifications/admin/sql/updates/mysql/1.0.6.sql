----Change table Key;

ALTER TABLE `#__tj_notification_user_exclusions` DROP INDEX `client1`, ADD INDEX `client1` (`client`(100), `provider`(50), `key`(100));
