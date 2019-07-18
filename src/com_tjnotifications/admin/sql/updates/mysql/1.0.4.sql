--
-- Change default table engine to InnoDB;
-- Necessary for indexes to work
--

ALTER TABLE `#__tj_notification_providers` ENGINE = InnoDB;
ALTER TABLE `#__tj_notification_templates` ENGINE = InnoDB;
ALTER TABLE `#__tj_notification_user_exclusions` ENGINE = InnoDB;

SET FOREIGN_KEY_CHECKS = 0;
ALTER TABLE `#__tj_notification_providers` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__tj_notification_user_exclusions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 1;
ALTER TABLE `#__tj_notification_templates` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
