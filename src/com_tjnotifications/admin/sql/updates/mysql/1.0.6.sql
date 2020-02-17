----Change table Key;

ALTER TABLE `#__tj_notification_user_exclusions` DROP INDEX `client1`;
ALTER TABLE `#__tj_notification_user_exclusions` ADD INDEX `client1` (`client`(100), `provider`(50), `key`(100));
